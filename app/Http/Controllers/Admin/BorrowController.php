<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Good;
use App\Models\Borrow;
use Illuminate\Http\Request;
use App\User;
use DataTables;
use Illuminate\Support\Facades\Hash;
use App\Models\StockTransaction;
use DB;
use App\Models\GoodShelf;

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

    public function check(Request $request)
    {

          if($request->isMethod('POST')){
            $amount = Good::find($request->goods);    
 
            $validator = $request->validate([
            'location' => 'required',
            'goods' => 'required',
           ]);

            $this->validate($request, [
             'amount' => ['required', 'numeric','max:' . ($amount->getBalanceByWarehouse($request->location)),'min:1'],
            ]); 

             return response()->json([
                'success'=>true,
            ]);   
          }
    }

    public function create(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())->get();

        if ($request->isMethod('post')){
            $user = User::find($request->data_user);
            $amount = Good::find($request->data_goods);
            if(Hash::check($request->password, $user->password)){

                try{
                    DB::statement('SET autocommit=0');
                    DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, good_shelves WRITE, stock_transactions WRITE, goods WRITE, borrows write');
        
                $borrow = New Borrow;
                $borrow->amount = $request->data_amount;
                $borrow->good_id = $request->data_goods;
                $borrow->user_id = $request->data_user;
                $borrow->description = $request->data_description;
                $borrow->location_shelf_id = $request->data_shelf;
                $borrow->status = Borrow::STILL_BORROW;
                $borrow->save();

                $goods = $borrow->good;

                $stocktransaction = New stocktransaction;
                $stocktransaction->start_balance = $goods->getBalanceByWarehouse($request->data_location);
                $stocktransaction->amount = $request->data_amount;
                $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
                $stocktransaction->type = StockTransaction::TYPE_OUT;
                $stocktransaction->good_id = $request->data_goods;
                $stocktransaction->user_id = $request->data_user;
                $stocktransaction->location_id = $request->data_location;
                $stocktransaction->location_shelf_id = $request->data_shelf;
                $borrow->stock_transaction()->save($stocktransaction);

                $good_shelf = GoodShelf::where('good_id', $request->data_goods)->where('location_shelf_id', $request->data_shelf)->first();
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
                    'message'   => 'Peminjaman Telah Berhasil'
                     ]);
            }else {
               return response()->json([
                    'success'=>false,
                    'message'   => 'Password User Salah'
                ]);  
            }
        }
        return view('borrow.add',['users' => $users]);
    }
}
