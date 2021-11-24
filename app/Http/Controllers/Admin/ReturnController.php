<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiveBack;
use \Yajra\Datatables\Datatables;
use App\Models\Good;
use App\User;
use App\Models\GoodLocation;
use App\Models\Location;
use App\Models\Borrow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\StockEntry;
use Illuminate\Support\Str;
use App\Models\StockTransaction;
use App\Models\GoodShelf;

class ReturnController extends Controller
{
    public function index (Request $request)
    {
        if( $request->isMethod('post') ){
            $model = GiveBack::with(['good', 'user' , 'location_shelf.location'])->get();
            return DataTables::of($model)->make();
        }

        return view('return.index');
    }


    public function check (Request $request)
    {

      if($request->isMethod('POST')){
            $goods = Good::find($request->goods);

            $this->validate($request, [
                'location' => 'required',
                'goods' => 'required',
                'amount' => ['required', 'numeric'],
                'nameshelf' =>'required',

           ]);

            $this->validate($request, [
                'amount' => ['numeric','max: ' . ($goods->borrow()->where('user_id', $request->user)->sum('amount')) ],
            ]);


                 return response()->json([
                    'success'=>true,
                ]);
        }
    }

    public function create(Request $request)
    {

        if($request->isMethod('post')){
             $user = User::find($request->data_user);
             $borrow = Borrow::where('good_id', $request->data_goods)->where('user_id', $request->data_user)->where('status', 'Still Borrow')->first();

             $goods = Good::find($request->data_goods);

             if(!Hash::check($request->password, $user->password)){
                 return response()->json([
                    'success'=>false,
                    'message'   => 'Password User Salah'
                ]);
            }

                try{
                    DB::statement('SET autocommit=0');
                    DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, expireds WRITE, good_shelves WRITE, stock_transactions WRITE, goods WRITE, borrows WRITE, give_backs WRITE');

                    $return = new GiveBack;
                    $return->good_id = $request->data_goods;
                    $return->user_id = $request->data_user;
                    $return->amount = $request->data_amount;
                    $return->location_shelf_id = $request->data_shelf;
                    $return->borrow_id = $borrow->id;

                    $return->save();



                    if($request->data_amount >= $borrow->amount){
                        $borrow = Borrow::find($borrow->id);
                        $borrow->status = Borrow::DONE;
                        $borrow->update();
                    }

                    $stockentry = Stockentry::find($request->data_entryid);
                    $amount = $stockentry->amount - $stockentry->stock_use;
                    $stockentry->stock_use = $stockentry->stock_use  - $data->amount;
                    if($stockentry->status == 'Out Of Stock'){
                        $stockentry->status = Stockentry::TYPE_STILL_USE;
                    }
                    $stockentry->save();

                    // $goodshelf = GoodShelf::firstOrCreate(['good_id' => $request->data_goods, 'location_shelf_id' =>$request->data_shelf]);

                    $goods = $return->good;

                    $stocktransaction = New Stocktransaction;
                    $stocktransaction->start_balance = $goods->getBalanceByWarehouse($request->data_location);
                    $stocktransaction->amount = $request->data_amount;
                    $stocktransaction->end_balance = $stocktransaction->start_balance + $stocktransaction->amount;
                    $stocktransaction->type = StockTransaction::TYPE_IN;
                    $stocktransaction->good_id = $request->data_goods;
                    $stocktransaction->user_id = $request->data_user;
                    $stocktransaction->location_id = $request->data_location;
                    $stocktransaction->location_shelf_id = $request->data_shelf;

                    $return->stock_transaction()->save($stocktransaction);
                    DB::getPdo()->exec('UNLOCK TABLES');
                }catch(\Exception $e){
                    DB::statement('ROLLBACK');

                    throw $e;
                }
                return response()->json([
                    'success'=>true,
                    'message'   => 'Pengembalian Telah Berhasil'
                ]);

        }
     return view('return.add');
    }

}
