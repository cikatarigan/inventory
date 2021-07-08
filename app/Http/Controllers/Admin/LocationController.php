<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use DataTables;
use validator;
use App\Models\LocationShelf;
use Illuminate\Http\Request;
use Auth;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        if( $request->isMethod('post') ){
        
            $model = Location::all();
 
              $shelf = LocationShelf::all();
 
            return DataTables::of($model)->addColumn('sub_location', function ($model) use ($shelf){
                $sub_location = [];
                 foreach ($shelf as $location) {
                    $sub_location[] =[
                        'sub_location'=> $model->name_shelf,
                
                    ];
                }
                return $sub_location;
               })->make();
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


    public function sub_location(Request $request)
    {
            
        $validator = $request->validate([
            'name_shelf'       => 'required|string|max:191|unique:location_shelves,name_shelf',
        ]);

        $goodlocation = New LocationShelf();
        $goodlocation->location_id = $request->id ;
        $goodlocation->name_shelf = $request->name_shelf;
        $goodlocation->user_id = Auth::id();
        $goodlocation->save();

        return response()->json([
            'success' => true,
            'message'   => 'Name Shelf Successfully Add'
        ]);

    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'name'       => 'required|string|max:191|unique:locations',
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
