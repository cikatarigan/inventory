<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StockEntry;
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
    public function index()
    {
        return view('home.index');
    }

    public function dashboard(Request $request)
    {
        $expired = StockEntry::whereBetween('date_expired', [ Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d')])->get();
        return view('home.index');
    }

    
}
