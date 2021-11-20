<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Allotment;
use App\Models\AllotmentItem;
use App\Models\Location;
use App\Models\Good;
use App\Models\GoodShelf;
use App\Models\StockTransaction;
use App\Models\StockEntry;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use validator;
use Illuminate\Support\Facades\Hash;
use \Yajra\Datatables\Datatables;

class AllotmentController extends Controller
{

    public function index(Request $request)
    {

    	if( $request->isMethod('post') ){
    		$model = Allotment::with(['location_shelf.location', 'good', 'user'])->whereBetween('created_at', array($request->startdate, $request->enddate))->get();
            return DataTables::of($model)->make();
        }
        return view('allotment.index');
    }


    public function check(Request $request)
    {

        if($request->isMethod('POST')){

            $goods = Good::find($request->goods);

            $validator = $request->validate([
            'location_id' => 'required',
            'location_shelf' => 'required',
            'goods' => 'required',
            'user' => 'required',
            'amount' => 'required',
           ]);

            $this->validate($request, [
             'amount' => ['required', 'numeric', 'max:' . ($goods->getBalanceByWarehouse($request->location_id)),'min:1'],
            ]);

             return response()->json([
                'success'=>true,
            ]);
        }

    }

    public function create(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())->get();


        if ($request->isMethod('POST')){

            $user = User::find($request->data_user);
            $goods = Good::find($request->data_goods);

            if(!Hash::check($request->password, $user->password)){
                 return response()->json([
                    'success'=>false,
                    'message'   => 'Password User Salah'
                ]);
            }

            if($goods->getBalanceByWarehouse($request->data_location) < $request->data_amount){
                return response()->json([
                    'success'=>false,
                    'message' => 'stock tidak cukup'
                ]);
            }

            $entry = StockEntry::where('status', '!=', 'Out Of Stock')->where('good_id' , $request->data_goods)->where('location_shelf_id', $request->data_shelf)->orderBy('created_at', 'asc')->get();


            try{
            DB::statement('SET autocommit=0');
            DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, stock_transactions WRITE, goods WRITE, allotments WRITE, allotment_items WRITE, good_shelves WRITE' );

            $allotment = New Allotment;
            $allotment->location_shelf_id = $request->data_shelf;
            $allotment->good_id = $request->data_goods;
            $allotment->amount = $request->data_amount;
            $allotment->user_id = $request->data_user;
            $allotment->description = $request->data_description;
            $allotment->save();


            $amount =  $request->data_amount;
            foreach ($entry as  $item) {
                $stock_amount = $item->amount -  $item->allotment_item()->sum('amount') ;
                if($stock_amount >  0 ){
                    $itemallotment = New AllotmentItem;
                    $itemallotment->entry_id =  $item->id;
                    $itemallotment->allotment_id = $allotment->id;

                    $stockentry = Stockentry::find($item->id);

                    if($amount <= $stock_amount){
                        $itemallotment->amount = $amount;

                        $stockentry->stock_use = $item->stock_use  + $amount;
                        if($item->stock_use >= $amount){
                            $stockentry->status = Stockentry::TYPE_OUT_STOCK;
                        }
                        $stockentry->save();
                        $itemallotment->save();
                        break;
                    }else{
                       $itemallotment->amount = $stock_amount;
                       $stockentry->stock_use = $stock_amount;
                       $stockentry->status = Stockentry::TYPE_OUT_STOCK;
                       $stockentry->save();
                       $itemallotment->save();
                       $amount = $amount - $itemallotment->amount;
                    }
                }

            }

            $goods = $allotment->good;

            $stocktransaction = New stocktransaction;
            $stocktransaction->start_balance = $goods->getBalanceByWarehouse($request->data_location);
            $stocktransaction->amount = $request->data_amount;
            $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
            $stocktransaction->type = StockTransaction::TYPE_OUT;
            $stocktransaction->good_id = $request->data_goods;
            $stocktransaction->user_id = Auth::id();
            $stocktransaction->location_id = $request->data_location;
            $stocktransaction->location_shelf_id = $request->data_shelf;
            $allotment->stock_transaction()->save($stocktransaction);

            $good_shelf = GoodShelf::where('good_id', $request->data_goods)->where('location_shelf_id',$request->data_shelf)->first();
            if($stocktransaction->end_balance == 0){
                $good_shelf->delete();
            }


            DB::getPdo()->exec('UNLOCK TABLES');
            }catch(\Exception $e){
                DB::statement('ROLLBACK');

                throw $e;
            }

               return response()->json([
                    'success'=>true,
                    'message'   => 'Pemberian Telah Berhasil'
            ]);



        }

    	return view('allotment.add', ['users' => $users]);
    }
}
