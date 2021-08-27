<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StockEntry;
use App\Models\Good;
use App\Models\Location;
use App\Models\LocationShelf;
use App\User;
use App\Models\Sample;
use App\Models\Allotment;
use App\Models\Expired;
use Auth;
use DB;
use App\Models\StockTransaction;
use App\Models\GoodShelf;
use App\Models\Borrow;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
         // $expired = StockEntry::with(['good', 'location_shelf.location'])->whereHas('good',  function($query){
         //    $query->whereNotNull('isexpired');
         // })->whereBetween('date_expired', [ Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(30)->format('Y-m-d') ])->where('status', '!=' ,'expired')->get();
          $expired = Good::with(['stockentry','location.locationshelf'])->whereHas('stockentry', function($query){
            $query->whereBetween('date_expired', [ Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(30)->format('Y-m-d') ])->where('status', '!=' ,'expired');       })->whereNotNull('isexpired')->get();
          
          
         $borrow = Borrow::with('good.good_images')->where('status', 'Still Borrow')->get();
         $allotment = Allotment::where('user_id', Auth::id())->get();
         $borrow_user = Borrow::with('good.good_images')->where('status', 'Still Borrow')->where('user_id', Auth::id())->get();
         $goods = Good::count();
         $users = User::count();
         $sample = Sample::count();

        return view('home.index',['expired' => $expired, 'goods'=> $goods, 'users' => $users ,'sample' => $sample , 'borrow' => $borrow, 'allotment' => $allotment, 'borrow_user' => $borrow_user]);
    }

    public function locations(Request $request)
    {
        $locations = Location::where('name', 'LIKE', "%$request->term%")
            ->select(['id', 'name as text'])->get();

        return response()->json([
            'results'  => $locations
        ]);
    }


    public function shelf(Request $request, Location $location)
    {

        $shelf = DB::table('location_shelves')->join('locations','location_shelves.location_id', '=', 'locations.id')->where('locations.id', $location->id)->select('location_shelves.id as id','name_shelf as text')->get();

        return response()->json([
            'results'  => $shelf
        ]);
    }

    public function goods(Request $request, LocationShelf $shelf)
    {
       

        $goods = Good::with(['good_shelves.location_shelves',])->whereHas('good_shelves.location_shelves', function($query) use ($shelf){
            $query->where('id', $shelf->id);
        })->select([ '*','goods.name as text'])->get();
  
        //  $goods = DB::table('goods')->join('good_shelves', 'goods.id', '=', 'good_shelves.good_id')
        // ->whereHas('location_shelves.location_id', $location->id)->whereNull('goods.isexpired')
        // ->select([ '*','goods.name as text'])->get();
        return response()->json([
            'results'  => $goods
        ]);
    }


    public function users(Request $request)
    {
      $user = User::whereHas('borrow', function($q){
           $q->groupBy('user_id');
      })->select(['id', 'name as text'])->get();
    
        return response()->json([
            'results'  => $user
        ]);
    }


    public function borrows(Request $request, User $user)
    {
        $borrow = DB::table('goods')->join('borrows', 'goods.id', '=' , 'borrows.good_id')
        ->where('borrows.user_id' , $user->id)
        ->select([ 'good_id as id','goods.name as text'])->get();

        return response()->json([
            'results'  => $borrow
        ]);
    }

    public function scan(Request $request)
    {
        if( $request->isMethod('post') ){
        }
        return view('scan.index');
    }

    public function check_result(Request $request)
    {
         if($request->isMethod('POST')){
            $goods = Good::find($request->good);    
 
            $validator = $request->validate([
            'user' => 'required',
            'amount' => 'required',
           ]);

            $this->validate($request, [
             'amount' => ['required', 'numeric','max:' . ($goods->getBalanceByWarehouse($request->location))],
            ]); 

             return response()->json([
                'success'=>true,
            ]);   
        }
    }

    public function result (Request $request)
    {
        $search = $request->q;
        $data = StockEntry::with(['good.good_shelves.location_shelves'])->where('qrcode', $search)->first();

        $amount = $data->amount -  $data->allotment_item()->sum('amount');
        $users = User::where('id', '!=', auth()->id())->get();

        if ($request->isMethod('post')){
           
            $validator = $request->validate([
            'user_id' => 'required',
       
           ]);
            $this->validate($request, [
             'amount' => ['required', 'numeric','max:' . ($amount)],
            ]); 
            if($request->log == 1){
                $data = New Borrow;
                $data->description = $request->description;
                $data->status = Borrow::STILL_BORROW;
                $data->good_id = $request->good_id;
                $data->user_id = $request->user;
                $data->handle_by = Auth::id();
                $data->amount = $request->amount;
                $data->save();
            }else if($request->log == 2){
                $data = New GiveBack;
                $data->good_id = $request->good_id;
                $data->user_id = $request->user;
                $data->handle_by = Auth::id();
                $data->amount = $request->amount;
                $data->save();
            }else {
                $data = New Allotment;
                $data->description = $request->description;
                $data->good_id = $request->good_id;
                $data->user_id = $request->user;
                $data->handle_by = Auth::id();
                $data->amount = $request->amount;
                $data->save();

                $amount =  $request->amount;
                foreach ($entry as  $item) {
                $stock_amount = $item->amount -  $item->allotment_item()->sum('amount') ;
                if($stock_amount >  0 ){
                    $itemallotment = New AllotmentItem;
                    $itemallotment->entry_id =  $item->id;
                    $itemallotment->allotment_id = $allotment->id;
                     
                    if($amount <= $stock_amount){
                        $itemallotment->amount = $amount;
                        $itemallotment->save();
                        break;
                    }else{
                       $itemallotment->amount = $stock_amount;
                       $itemallotment->save();
                       $amount = $amount - $itemallotment->amount;  
                    }
                }
            
            }
        }

                

             return response()->json([
                'success' => true,
                'message'   => 'Successfully'
            ]);

        }

        return view('scan.result', ['data' => $data, 'amount' => $amount ,'users' => $users]);
    }


    public function expired(Request $request, $id)
    {
                $data = StockEntry::with('location_shelf.location')->find($id);
               
                $goods = Good::find($data->good_id);

        if ($request->isMethod('post')){

            try{
            DB::statement('SET autocommit=0');
            DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, stock_transactions WRITE, goods WRITE, allotments WRITE, allotment_items WRITE, expireds WRITE, good_shelves WRITE' );

                              
                $expired = New Expired;
                $expired->good_id = $data->good_id; 
                $expired->entry_id = $data->id;
                $expired->location_shelf_id = $data->location_shelf_id;
                $expired->amount =  $data->amount -  $data->allotment_item()->sum('amount');
                $expired->location_id = $data->location_shelf->location->id;
                $expired->save();

                $stockentry = StockEntry::find($data->id);
                $stockentry->status = StockEntry::TYPE_EXPIRED;
                $stockentry->save();

                $stocktransaction = New stocktransaction;
                $stocktransaction->start_balance = $goods->getBalanceByWarehouse($data->location_shelf->location->id);
                $stocktransaction->amount = $expired->amount;
                $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
                $stocktransaction->type = StockTransaction::TYPE_OUT;
                $stocktransaction->good_id = $data->good_id;
                $stocktransaction->user_id = 1;
                $stocktransaction->location_id = $data->location_shelf->location->id;
                $stocktransaction->location_shelf_id = $data->location_shelf_id;
                $expired->stock_transaction()->save($stocktransaction);

                $good_shelf = GoodShelf::where('good_id', $data->good_id)->where('location_shelf_id', $data->location_shelf_id)->first();
                if($stocktransaction->end_balance == 0){
                    $good_shelf->delete();
                }

                DB::getPdo()->exec('UNLOCK TABLES');
                }catch(\Exception $e){
                    DB::statement('ROLLBACK');

                    throw $e;
                }


                return response()->json([
                'success' => true,
                'message'   => 'Successfully'
            ]);
        }
    }

    public function view()
    {
        return view('stockentry.view');
    }
}
