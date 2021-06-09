<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Allotment;
use App\Models\AllotmentItem;
use App\Models\Location;
use App\Models\Good;
use App\Models\GoodLocation;
use App\Models\StockTransaction;
use App\Models\StockEntry;
use Carbon\Carbon;
use DataTables;
use App\User;
use Auth;
use Illuminate\Support\Facades\DB;
use validator;
use Illuminate\Support\Facades\Hash;


class AllotmentController extends Controller
{
    
    public function index(Request $request)
    {

    	if( $request->isMethod('post') ){
    		$model = Allotment::with(['location', 'good', 'user'])->get();
            return DataTables::of($model)->make();
        }
        return view('allotment.index');
    }


    public function check(Request $request)
    {

        if($request->isMethod('POST')){
            
            $amount = Good::find($request->goodview);

            $validator = $request->validate([
            'locationview' => 'required',
            'goodview' => 'required',
           ]);

            $this->validate($request, [
             'amountview' => ['required', 'numeric', 'max:' . ($amount->getBalanceByWarehouse($request->locationview))],
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


            $user = User::find($request->user);
            $amount = Good::find($request->good);

            $entry = StockEntry::whereDate('date_expired', '>=', Carbon::now())->where('good_id' , $request->good)->where('location_id', $request->location)->orderBy('created_at', 'asc')->get();

         
            

            if(Hash::check($request->password, $user->password)){

            try{
            DB::statement('SET autocommit=0');
            DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, good_locations WRITE, stock_transactions WRITE, goods WRITE, allotments WRITE, allotment_items WRITE' );

            $allotment = New Allotment;
            $allotment->location_id = $request->location;
            $allotment->good_id = $request->good;
            $allotment->amount = $request->amount;
            $allotment->user_id = $request->user;
            $allotment->description = $request->description;
            $allotment->save();

            foreach ($entry as  $key => $item) {
                $itemallotment = New AllotmentItem;
                $itemallotment->entry_id =  $item->id;
                $itemallotment->allotment_id = $allotment->id;
                $itemallotment->amount = $entry->amount[$key];
                $itemallotment->save();
            }
             

            $goods = $allotment->good;

            $stocktransaction = New stocktransaction;
            $stocktransaction->start_balance = $amount->getBalanceByWarehouse($request->location);
            $stocktransaction->amount = $request->amount;
            $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
            $stocktransaction->type = StockTransaction::TYPE_OUT;
            $stocktransaction->good_id = $request->good;
            $stocktransaction->user_id = Auth::id();
            $stocktransaction->location_id = $request->location;

            $allotment->stock_transaction()->save($stocktransaction);

            DB::getPdo()->exec('UNLOCK TABLES');
            }catch(\Exception $e){
                DB::statement('ROLLBACK');

                throw $e;
            }

               return response()->json([
                    'success'=>true,
                    'message'   => 'Pemberian Telah Berhasil'
            ]);
            }else{
                 return response()->json([
                    'success'=>false,
                    'message'   => 'Password User Salah'
                ]);  
            }


        }

    	return view('allotment.add', ['users' => $users]);
    }
}
