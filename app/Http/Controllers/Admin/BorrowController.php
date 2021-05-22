<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use Illuminate\Http\Request;
use App\User;
use DataTables;

class BorrowController extends Controller
{   

    public function index(Request $request)
    {
        if( $request->isMethod('post') ){
            $model = Borrow::with(['good', 'user'])->get();
            return DataTables::of($model)->make();
        }
    
        return view('borrow.index');
    }

    public function create(Request $request)
    {
        $users = User::all();
        if ($request->isMethod('post')){

            try{
                DB::statement('SET autocommit=0');
                DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, good_locations WRITE, stock_transactions WRITE, goods WRITE');
    
            $borrow = New Borrow;
            $borrow->amount = $request->amount;
            $borrow->good_id = $request->good_id;
            $borrow->user_id = $request->user_id;
            $borrow->save();

            $stocktransaction = New stocktransaction;
            $stocktransaction->start_balance = $goods->getBalanceByWarehouse($stockentry->location_id);
            $stocktransaction->amount = $request->amount;
            $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
            $stocktransaction->type = StockTransaction::TYPE_OUT;
            $stocktransaction->good_id = $request->good_id;
            $stocktransaction->user_id = $request->user_id;
            $stocktransaction->location_id = $request->location_id;

                DB::getPdo()->exec('UNLOCK TABLES');
            }catch(\Exception $e){
                DB::statement('ROLLBACK');

                throw $e;
            }
        }
        return view('borrow.add',['users' => $users]);
    }
}
