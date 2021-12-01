<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use \Yajra\Datatables\Datatables;
use validator;
use App\Models\LocationShelf;
use App\Models\StockEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        if( $request->isMethod('post') ){
            $model = Location::with(['locationshelf'])->get();
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
        $check = LocationShelf::where('location_id', $request->id)->first();

        if($check){
            return response()->json([
                'success' =>false,
                'message' => 'Location Masih di Gunakan'
            ]);
        }

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

    public function destroy_trash(Request $request)
    {
        $check =  StockEntry::where('location_shelf_id', $request->id)->where(function($query){
            $query->where('status', 'No Expired')
                    ->orWhere('status', 'Still Use');
        })->first();

        if($check){
            return response()->json([
                'success'=>false,
                'message'   => 'Sub Location Masih Di gunakan'
            ]);
        }
        $location = LocationShelf::find($request->id);
        $location->delete();
            return response()->json([
                'success'=>true,
                'message'   => 'Sub Location Successfully Delete'
            ]);
    }

    public function sub_trash(Request $request)
    {
         if( $request->isMethod('post') ){
            $model = LocationShelf::onlyTrashed()->get();

            return DataTables::of($model)->make();
        }

        return view('location.subtrash');
    }

    public function sub_restore(Request $request)
    {
        $sublocation = LocationShelf::onlyTrashed()->find($request->id);


        $location = Location::find($sublocation->id);
        // dd($location);
        if( $location){
            $sublocation->restore();
            return response()->json([
                'success' => true,
                'message' => 'Sub Location Succesfully Restore'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Sub Location has been removed'
            ]);
        }

    }
}
