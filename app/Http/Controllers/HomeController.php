<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StockEntry;
use App\Models\Good;
use App\Models\Location;
use App\Models\Goodlocation;
use App\User;
use App\Models\Sample;
use App\Models\Allotment;
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
         $expired = StockEntry::with(['good', 'location'])->whereHas('good',  function($query){
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
         $good_borrow = DB::table('goods')->join('good_locations', 'goods.id', '=', 'good_locations.good_id')
        ->where('good_locations.location_id', $location->id)->whereNull('goods.isexpired')
        ->select([ 'good_id as id','goods.name as text'])->get();
        
        return response()->json([
            'results'  => $good_borrow
        ]);
    }


    public function goods(Request $request, Location $location)
    {
        
        $goods = DB::table('goods')->join('good_locations', 'goods.id', '=', 'good_locations.good_id')
        ->where('good_locations.location_id', $location->id)
        ->select([ 'good_id as id','goods.name as text'])->get();
        
        return response()->json([
            'results'  => $goods
        ]);
    }


    public function shelf(Request $request, Location $location)
    {
        $shelf = DB::table('good_locations')->join('locations','good_locations.location_id', '=', 'locations.id')->where('locations.id', $location->id)->groupBy('name_shelf')->select('name_shelf as id','name_shelf as text')->get();

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



    
}
