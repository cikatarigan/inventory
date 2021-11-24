<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockEntry;
use App\Models\StockTransaction;
use App\Models\Good;
use App\Models\Location;
use App\Models\GoodShelf;
use DataTables;
use DateTime;
use Illuminate\Support\Facades\DB;
use validator;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Str;

class StockEntryController extends Controller
{
    public function index(Request $request)
    {

    	if( $request->isMethod('post') ){
    		$model = StockEntry::with(['location_shelf.location', 'good', 'user']);
            return DataTables::of($model)->make();
        }
        return view('stockentry.index');
    }

    public function create(Request $request, Location $location)
    {
            $good = Good::select(['*','name as text'])->get();
            $location  = Location::all();

            if ($request->isMethod('POST')){

            $validator = $request->validate([
                'good_id' => 'required',
                'amount' => 'required|numeric|min:1',
                'location_shelf' => 'required',
                'qrcode' => 'unique:stock_entries,qrcode'
            ]);

            try{
                DB::statement('SET autocommit=0');
                DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, location_shelves WRITE, good_shelves WRITE, stock_transactions WRITE, goods WRITE');

                $checkgood  = Good::find($request->good_id);

                $stockentry = New StockEntry;

                $stockentry->good_id = $request->good_id;
                $stockentry->amount = $request->amount;
                $stockentry->stock_use = 0;
                $stockentry->location_shelf_id = $request->location_shelf;
                $stockentry->qrcode = Str::random(15);
                if($request->date_expired){
                    $stockentry->date_expired = Carbon::parse($request->date_expired);
                }
                if($checkgood->isexpired == 'on'){
                    $stockentry->status = StockEntry::TYPE_STILL_USE;
                }else{
                    $stockentry->status = StockEntry::TYPE_NO_EXPIRED;
                }
                $stockentry->save();


                $goodshelf = new GoodShelf;
                $goodshelf->good_id = $request->good_id;
                $goodshelf->location_shelf_id = $request->location_shelf;
                $goodshelf->amount  = $request->amount;
                $goodshelf->save();

                $goods = $stockentry->good;

                $stocktransaction = New stocktransaction;
                $stocktransaction->start_balance = $goods->getBalanceByWarehouse($request->location_id);
                $stocktransaction->amount = $request->amount;
                $stocktransaction->end_balance = $stocktransaction->start_balance + $stocktransaction->amount;
                $stocktransaction->type = StockTransaction::TYPE_IN;
                $stocktransaction->good_id = $request->good_id;
                $stocktransaction->user_id = Auth::id();
                $stocktransaction->location_id = $request->location_id;
                $stocktransaction->location_shelf_id = $request->location_shelf;

                $stockentry->stock_transaction()->save($stocktransaction);

                DB::getPdo()->exec('UNLOCK TABLES');
            }catch(\Exception $e){
                DB::statement('ROLLBACK');

                throw $e;
            }

            return response()->json([
                'success' => true,
                'message'   => 'Stock Entry Successfully Add'
            ]);

        }
        return view('stockentry.add', ['good' => $good ,'location' => $location, ]);
    }



}

