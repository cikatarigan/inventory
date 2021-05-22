<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockEntry;
use App\Models\StockTransaction;
use App\Models\Good;
use App\Models\Location;
use App\Models\GoodLocation;
use DataTables;
use DateTime;
use DB;
use validator;
use Carbon\Carbon;
use Auth;


class StockEntryController extends Controller
{
    public function index(Request $request)
    {

    	if( $request->isMethod('post') ){
    		$model = StockEntry::with(['location', 'good', 'user'])->get();
            return DataTables::of($model)->make();
        }
        return view('stockentry.index');
    }

    public function create(Request $request)
    {
        $good = Good::select(['*','name as text'])->get();
        $location  = Location::all();
        $nameshelf  =DB::table('good_locations')->where('location_id', $location->id)->select('name_shelf')->distinct()->get();
        
        if ($request->isMethod('POST')){

         $validator = $request->validate([
            'good_id' => 'required',
            'amount' => 'required',
            'location_id' => 'required',
        ]);

         try{
            DB::statement('SET autocommit=0');
            DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, good_locations WRITE, stock_transactions WRITE, goods WRITE');

            $stockentry = New StockEntry;
            $stockentry->good_id = $request->good_id;
            $stockentry->amount = $request->amount;
            $stockentry->location_id = $request->location_id;
            $stockentry->date_expired = Carbon::parse($request->date_expired);
            $stockentry->save();

            if($stockentry->date_expired != null){
                $good = Good::find($request->good_id);
                $good->expired_date = Carbon::parse($request->date_expired);
                $good->save();
            }

            $goodlocation = Goodlocation::firstOrCreate(['good_id' => $request->good_id, 'location_id' => $request->location_id, 'name_shelf' => $request->nameshelf]);

            $goods = $stockentry->good;
            
            $stocktransaction = New stocktransaction;
            $stocktransaction->start_balance = $goods->getBalanceByWarehouse($stockentry->location_id);
            $stocktransaction->amount = $request->amount;
            $stocktransaction->end_balance = $stocktransaction->start_balance + $stocktransaction->amount;
            $stocktransaction->type = StockTransaction::TYPE_IN;
            $stocktransaction->good_id = $request->good_id;
            $stocktransaction->user_id = Auth::id();
            $stocktransaction->location_id = $request->location_id;

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
    return view('stockentry.add', ['good' => $good ,'location' => $location, 'nameshelf' => $nameshelf]);
    }


}

