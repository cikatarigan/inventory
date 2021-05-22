<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Allotment;
use App\Models\Location;
use App\Models\Good;
use App\Models\GoodLocation;
use App\Models\StockTransaction;
use DataTables;
use App\User;
use Auth;
use Illuminate\Support\Facades\DB;
use validator;


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

    public function locations(Request $request)
    {
        $locations = Location::where('name', 'LIKE', "%$request->term%")
            ->select(['id', 'name as text'])->get();

        return response()->json([
            'results'  => $locations
        ]);
    }

    public function goods(Request $request, Location $location)
    {
        
        $goods = DB::table('goods')->join('good_locations', 'goods.id', '=', 'good_locations.good_id')
        ->where('good_locations.location_id', $location->id)
        ->select([ '*','goods.name as text'])->get();

        return response()->json([
            'results'  => $goods
        ]);
    }


    public function check(Request $request)
    {

        if($request->isMethod('POST')){

            $amount = Good::find($request->good);

            $validator = $request->validate([
            'location' => 'required',
            'good' => 'required',
           ]);

            $this->validate($request, [
             'amount' => ['required', 'numeric', 'max:' . ($amount->getBalanceByWarehouse($request->location))],
            ]);    
        }

    }

    public function create(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())->get();
        

        if ($request->isMethod('POST')){


            $user_password = User::select('id')->where('password',$password)->first();

            try{
            DB::statement('SET autocommit=0');
            DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, good_locations WRITE, stock_transactions WRITE, goods WRITE, allotments WRITE');

            $allotment = New Allotment;
            $allotment->location_id = $request->location;
            $allotment->good_id = $request->good;
            $allotment->amount = $request->amount;
            $allotment->user_id = $request->user;
            $allotment->description = $request->description;
            $allotment->save();

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
        }

    	return view('allotment.add', ['users' => $users]);
    }
}
