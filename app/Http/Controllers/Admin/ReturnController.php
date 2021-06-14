<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiveBack;
use DataTables;
use App\Models\Good;
use App\User;
use App\Models\GoodLocation;
use App\Models\Location;
use App\Models\Borrow;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ReturnController extends Controller
{
    public function index (Request $request)
    {
        if( $request->isMethod('post') ){
            $model = GiveBack::with(['good', 'user','good_location'])->get();
            return DataTables::of($model)->make();
        }

        return view('return.index');
    }


    public function check (Request $request)
    {

      if($request->isMethod('POST')){

            $this->validate($request, [
                'location' => 'required',
                'good' => 'required',
                'amount' => ['required', 'numeric'],

           ]); 

            $good = Good::find($request->good);
   
            $this->validate($request, [
                'amount' => ['numeric','max: ' . ($good->borrow()->where('user_id', $request->user)->sum('amount')) ],
            ]);


                 return response()->json([
                    'success'=>true,
                ]);   
        }
    }

    public function create(Request $request)
    {

 

        if($request->isMethod('post')){
             $user = User::find($request->userview);
            if(Hash::check($request->password, $user->password)){

                try{
                    DB::statement('SET autocommit=0');
                    DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, good_locations WRITE, stock_transactions WRITE, goods WRITE, borrows write, give_backs write');

                    $return = new GiveBack;
                    $return->good_id = $request->goodview;
                    $return->user_id = $request->userview;
                    $return->amount = $request->amountview;
                    $return->location_id = $request->locationview;
                    $return->name_shelf = $request->nameview;
                    $return->description = $request->descriptionview;
                    $return->status = 'Status';
                    $return->save();

                    $borrow = Borrow::where('user_id', $request->user_id)->where('good_id', $request->good_id)->sum('amount');

                    dd($borrow);
                    $borrow->status = Borrow::Done; 
                    $borrow->update();

                    $goodlocation = Goodlocation::firstOrCreate(['good_id' => $request->good_id, 'location_id' => $request->location_id, 'name_shelf' => $request->nameshelf]);


                    $goods = $return->good_id;

                    $stocktransaction = New stocktransaction;
                    $stocktransaction->start_balance = $goods->getBalanceByWarehouse($request->location);
                    $stocktransaction->amount = $request->amount;
                    $stocktransaction->end_balance = $stocktransaction->start_balance + $stocktransaction->amount;
                    $stocktransaction->type = StockTransaction::TYPE_IN;
                    $stocktransaction->good_id = $request->good;
                    $stocktransaction->user_id = $request->user;
                    $stocktransaction->location_id = $request->location;

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
            }else {
                return response()->json([
                'success'=>false,
                'message'   => 'Password User Salah'
                ]);  
            }
        }
     return view('return.add');
    }

}
