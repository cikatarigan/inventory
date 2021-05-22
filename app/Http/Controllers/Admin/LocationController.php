<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use DataTables;
use validator;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        if( $request->isMethod('post') ){
        
            $model = Location::all();
        
            return DataTables::of($model)->make();
        }

        return view('location.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
         if ($request->isMethod('POST')){

            $validator = $request->validate([
                'name'=>'required|string|max:60|unique:locations',
            ]);

            $location = New Location;
            $location->name = $request->name;
            $location->save();

            return response()->json([
                'success' => true,
                 'message'   => 'Location Successfully Add'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'name'       => 'required|string|max:191',
        ]);

        $location          = Location::find($id);
        $location->name   = $request->name;
        $location->save();
     
            return response()->json([
                'success' => true,
                'message'   => 'Location Successfully Edited'
            ]);

    }

    public function destroy(Request $request)
    {
        $location = Location::find($request->id);
        $location->delete();
            return response()->json([
                'success'=>true,
                'message'   => 'Location Successfully Delete'
            ]);
    }

    public function trash(Request $request)
    {
         if( $request->isMethod('post') ){
            $model = Location::onlyTrashed()->get();
            
            return DataTables::of($model)->make();
        }

        return view('location.trash');
    }

    public function restore(Request $request)
    {
        $location = Location::onlyTrashed()->find($request->id);
        $location->restore();
            return response()->json([
                'success'=>true,
                'message'   => 'Location Successfully Restore'
            ]);
    }
}
