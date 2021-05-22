<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\Image;
use App\Models\GoodImage;
use App\Models\Location;
use App\Models\GoodLocation;
use DataTables;
use validator;
use DB;
use Illuminate\Support\Facades\Storage;


class GoodController extends Controller
{
    public function index(Request $request)
    {
    
        $name_shelf = DB::table('good_locations')->select('name_shelf')->groupBy('name_shelf')->get();
        $location = Location::all();

        if( $request->isMethod('post') ){
            $model = Good::with(['good_location']);
            return DataTables::of($model)->make();
        }
        
        return view('good.index',['location' => $location,'name_shelf' => $name_shelf]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $brand = DB::table('goods')->select('brand')->groupBy('brand')->get();
        $category = DB::table('goods')->select('category')->groupBy('category')->get();

         if ($request->isMethod('POST')){

            $validator = $request->validate([
                'name'=>'required|string|max:60|unique:goods',
                'brand'=>'required',
                'category'=>'required',
                'description'=>'required',
                'barcode'=>'required',
                'unit'=>'required',
            ]);

            $good = New Good;
            $good->name = $request->name;
            $good->brand = $request->brand;
            $good->category = $request->category;
            $good->description = $request->description;
            $good->barcode = $request->barcode;
            $good->unit = $request->unit;
            $good->isexpired = $request->isexpired;
            $good->save();

            if($request->images != null){
                foreach ($request->images as $image) {
                    $filename  = $image->getClientOriginalName();
                    $path      = $image->store('good');
                    $extension = $image->getClientOriginalExtension();
                    $size      = $image->getSize();
                    
                    $image            = new Image();
                    $image->filename  = time() . '_' . $filename;
                    $image->title     = time() . '_' . $filename;
                    $image->path      = $path;
                    $image->extension = $extension;
                    $image->size      = $size;
                    $image->save();

                    $goodimage = new GoodImage();
                    $goodimage->good()->associate($good);
                    $goodimage->image()->associate($image);
                    $goodimage->save();
                }
            }
            

            return response()->json([
                'success' => true,
                 'message'   => 'Good Successfully Add'
            ]);
        }
        return view('good.add',['brand' => $brand, 'category' => $category]);
    }

    public function update(Request $request, $id)
    {
        $good = Good::find($id);
        $brand = DB::table('goods')->select('brand')->groupBy('brand')->get();
        $category = DB::table('goods')->select('category')->groupBy('category')->get();
        if ($request->isMethod('POST')) 
        {
            
            $validator = $request->validate([
                'name'=>'required|string|max:60',
                'brand'=>'required',
                'category'=>'required',
                'description'=>'required',
                'barcode'=>'required',
                'unit'=>'required',
            ]);

            $good->name = $request->name;
            $good->brand = $request->brand;
            $good->category = $request->category;
            $good->description = $request->description;
            $good->barcode = $request->barcode;
            $good->unit = $request->unit;

            if($request->deleted_image != null){
                foreach ($request->deleted_image as $deleted_images) {
                    $goodimage = GoodImage::find($deleted_images);
                    Storage::delete($goodimage->image->path);
                    $goodimage->delete();
                }
            }
            
            if($request->images != null)
            {
                foreach ($request->images as $image) {

                    $filename  = $image->getClientOriginalName();
                    $path      = $image->store('good');
                    $extension = $image->getClientOriginalExtension();
                    $size      = $image->getSize();

                    $image            = new Image();
                    $image->filename  = time() . '_' . $filename;
                    $image->title     = $request->title;
                    $image->path      = $path;
                    $image->extension = $extension;
                    $image->size      = $size;
                    $image->save();

                    $goodimage = new GoodImage();            
                    $goodimage->good()->associate($good);
                    $goodimage->image()->associate($image);
                    $goodimage->save();    
                }
            }

            $good->save();

            return response()->json([
                'success' => true,
                'message'   => 'Good Successfully Updated'
            ]);
        }
        
        return view('good.update', ['good' => $good, 'brand' => $brand , 'category' => $category]);
    }

    public function location(Request $request)
    {
        if ($request->isMethod('POST')){

            $validator = $request->validate([
                'good_id'=>'required',
                'location_id'=>'required',
                'name_shelf'=>'required',
            ]);
            $good = new GoodLocation();
            $good->good_id = $request->good_id;
            $good->location_id = $request->location_id;
            $good->name_shelf = $request->name_shelf;
            $good->save();

            return response()->json([
                'success' => true,
                'message'   => 'Good Successfully Location Added'
            ]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $good = Good::find($request->id);
        $good->delete();
            return response()->json([
                'success'=>true,
                'message'   => 'Good Successfully Delete'
            ]);
    }

    public function trash(Request $request)
    {
         if( $request->isMethod('post') ){

            $model = Good::onlyTrashed()->get();
        
            return DataTables::of($model)->make();
        }

        return view('good.trash');
    }

    public function restore(Request $request)
    {
        $good = Good::onlyTrashed()->find($request->id);
        $good->restore();
            return response()->json([
                'success'=>true,
                'message'   => 'Good Successfully Restore'
            ]);
    }
}