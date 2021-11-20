<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\Image;
use App\Models\GoodImage;
use App\Models\Location;
use App\Models\StockEntry;
use App\Models\GoodLocation;
use \Yajra\Datatables\Datatables;
use validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class GoodController extends Controller
{
    public function index(Request $request)
    {

        $name_shelf = DB::table('location_shelves')->select('name_shelf')->groupBy('name_shelf')->get();
        $location = Location::all();

        if( $request->isMethod('post') ){
            $model = Good::with(['location.locationshelf']);
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
                'unit'=>'required',
            ]);

            $good = New Good;
            $good->name = $request->name;
            $good->brand = $request->brand;
            $good->category = $request->category;
            $good->description = $request->description;
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
        $unit = DB::table('goods')->select('unit')->groupBy('unit')->get();
        if ($request->isMethod('POST'))
        {

            $validator = $request->validate([
                'name'=>'required|string|max:60',
                'brand'=>'required',
                'category'=>'required',
                'description'=>'required',
                'unit'=>'required',
            ]);

            $good->name = $request->name;
            $good->brand = $request->brand;
            $good->category = $request->category;
            $good->description = $request->description;
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

        return view('good.update', ['good' => $good, 'brand' => $brand , 'category' => $category, 'unit'=> $unit]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $check =  StockEntry::where('good_id', $request->id)->where(function($query){
            $query->where('status', 'No Expired')
                    ->orWhere('status', 'Still Use');
        })->first();

        if($check){
            return response()->json([
                'success'=>false,
                'message'   => 'Barang Masih Di gunakan'
            ]);
        }
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
