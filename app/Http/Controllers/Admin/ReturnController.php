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


        $amount = Borrow::where('good_id', $request->good)->get();

        $validator = $request->validate([
            'location' => 'required',
            'good' => 'required',
        ]);

        $this->validate($request, [
           'amount' => ['required', 'numeric','max:' . ($amount->GetBalanceBorrow($request->good))],
       ]); 

            //  return response()->json([
            //     'success'=>true,
            // ]);   
    }
}

    public function create(Request $request)
    {
        $location  = Location::all();
        $nameshelf = Goodlocation::select(['*', 'name_shelf as text'])->get();

        if($request->isMethod('post')){
            if(Hash::check($request->password, $user->password)){

                try{
                    DB::statement('SET autocommit=0');
                    DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, good_locations WRITE, stock_transactions WRITE, goods WRITE, borrows write, give_backs write');

                    $return = new GiveBack;
                    $return->good_id = $request->good_id;
                    $return->user_id = $request->user_id;
                    $return->amount = $request->amount;
                    $return->location_id = $request->location_id;
                    $return->name_shelf = $request->name_shelf;
                    $return->description = $request->description;
                    $return->save();

                    $borrow = Borrow::where('user_id', $request->user_id)->where('good_id', $request->good_id);
                    $borrow->status = Borrow::Done;
                    $borrow->update();

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
     return view('return.add',['location' => $location, 'nameshelf' => $nameshelf]);
    }

}
