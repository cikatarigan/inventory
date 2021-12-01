<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Yajra\DataTables\DataTables;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        if( $request->isMethod('post') ){

            $model = Unit::all();

            return DataTables::of($model)->make();
        }

        return view('unit.index');
    }

    public function create(Request $request)
    {
         if ($request->isMethod('POST')){

            $validator = $request->validate([
                'name'=>'required|string|max:60|unique:units',
                'description' => 'required',
            ]);

            $unit = New Unit;
            $unit->name = $request->name;
            $unit->description = $request->description;
            $unit->save();

            return response()->json([
                'success' => true,
                 'message'   => 'Unit Successfully Add'
            ]);
        }
    }
}
