<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiveBack;
use DataTables;
use App\Models\Good;
use App\User;
use App\Models\GoodLocation;
use App\Models\Location;
use App\Models\Borrow;
use DB;

class ReturnController extends Controller
{
    public function index (Request $request)
    {
        if( $request->isMethod('post') ){
            $model = GiveBack::with(['good', 'user','good_location'])->get();
            return DataTables::of($model)->make();
        }

    	return view('return.index');
    }


    public function check ()
    {

    }

    public function create(Request $request)
    {
        $location  = Location::all();
        $nameshelf = Goodlocation::select(['*', 'name_shelf as text'])->get();
    
        
    	return view('return.add',['location' => $location, 'nameshelf' => $nameshelf]);
    }
}
