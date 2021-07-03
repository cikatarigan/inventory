<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expired;
use DataTables;

class ExpiredController extends Controller
{
public function index(Request $request)
    {
        if( $request->isMethod('post') ){
            $model = Expired::with(['good','location'])->get();
            return DataTables::of($model)->make();
        }
    
        return view('expired.index');
    }
}
