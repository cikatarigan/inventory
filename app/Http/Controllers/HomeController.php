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
         $expired = StockEntry::with(['good', 'location_shelf.location'])->whereHas('good',  function($query){
            $query->whereNotNull('isexpired');
         })->whereBetween('date_expired', [ Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(30)->format('Y-m-d') ])->get();

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


    public function goods_borrow(Request $request, Location $location)
    {
        // $good_borrow = DB::table('goods')->join('good_shelves', 'goods.id', '=', 'good_shelves.good_id')
        // ->where('good_shelves.location_id', $location->id)
        // ->select([ 'good_id as id','goods.name as text'])->get();
        
       $good_borrow = Good::with(['good_shelves.location_shelves'])->whereHas('good_shelves.location_shelves', function($query)use ($location){
            $query->where('location_id', $location->id);
        })->select([ '*','goods.name as text'])->get();
  
        return response()->json([
            'results'  => $good_borrow
        ]);
    }


    public function goods(Request $request, Location $location)
    {
     
        $goods = Good::with(['good_shelves.location_shelves'])->whereHas('good_shelves.location_shelves', function($query) use ($location){
            $query->where('location_id', $location->id);
        })->select([ '*','goods.name as text'])->get();
  
        dd($goods);

        //  $goods = DB::table('goods')->join('good_shelves', 'goods.id', '=', 'good_shelves.good_id')
        // ->whereHas('location_shelves.location_id', $location->id)->whereNull('goods.isexpired')
        // ->select([ '*','goods.name as text'])->get();
        return response()->json([
            'results'  => $goods
        ]);
    }

    public function shelf(Request $request, Location $location)
    {
        $shelf = DB::table('location_shelves')->join('locations','location_shelves.location_id', '=', 'locations.id')->where('locations.id', $location->id)->select('*','name_shelf as text')->get();

        return response()->json([
            'results'  => $shelf
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

    public function result (Request $request)
    {
        $search = $request->q;
        $data = StockEntry::with('good')->where('qrcode', $search)->first();
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
            }else if($request->log == 2){
                $data = New GiveBack;
            }else {
                $data = New Allotment;
                $data->description = $request->description;
            }

                $data->good_id = $request->good_id;
                $data->user_id = $request->user;
                $data->handle_by = Auth::id();
                $data->amount = $request->amount;
                $data->save();

             return response()->json([
                'success' => true,
                'message'   => 'Successfully'
            ]);

        }

        return view('scan.result', ['data' => $data, 'amount' => $amount ,'users' => $users]);
    }


    public function expired(Request $request, $id)
    {
        if ($request->isMethod('post')){

                $data = StockEntry::find($id);

                $goods = Good::find($data->good_id);
                              
                $expired = New Expired;
                $expired->good_id = $data->good_id; 
                $expired->entry_id = $data->id;

                $expired->location_id = $data->location_shelf();
           
                $expired->amount =  $data->amount -  $data->allotment_item()->sum('amount');     
                $expired->save();

                $stocktransaction = New stocktransaction;
                $stocktransaction->start_balance = $goods->getBalanceByWarehouse($data->location_id);
                $stocktransaction->amount = $expired->amount;
                $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
                $stocktransaction->type = StockTransaction::TYPE_OUT;
                $stocktransaction->good_id = $data->good_id;
                $stocktransaction->user_id = 1;
                $stocktransaction->location_id = $data->location_id;
                $expired->stock_transaction()->save($stocktransaction);


                return response()->json([
                'success' => true,
                'message'   => 'Successfully'
            ]);
        }
    }

    
}
