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
use DB;

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
         $expired = StockEntry::with(['good', 'location','good_location'])->whereBetween('date_expired', [ Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(30)->format('Y-m-d') ])->get();

         $goods = Good::count();
         $users = User::count();
         $sample = Sample::count();

        return view('home.index',['expired' => $expired, 'goods'=> $goods, 'users' => $users ,'sample' => $sample]);
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
        ->select([ 'good_id as id','goods.name as text'])->get();

        return response()->json([
            'results'  => $goods
        ]);
    }


    public function users(Request $request)
    {

      $user = DB::table('borrows')->join('users', 'borrows.user_id', '=', 'users.id')->select([ '*','users.name as text'])->get();

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
