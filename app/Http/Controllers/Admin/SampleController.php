<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sample;
use DataTables;
use Illuminate\Support\Facades\Storage;

class SampleController extends Controller
{
    public function index(Request $request)
    {       
        if( $request->isMethod('post') ){
        
        $model = Sample::all();
    
        return DataTables::of($model)->make();
    }
        return view('sample.index');
    }

    public function add(Request $request)
    {

        if ($request->isMethod('POST')){

            $validator = $request->validate([
                'title'=>'required|string|max:191',
                 'photo' => 'required|mimes:jpeg,png',
            ]);

            $sample = new Sample();
            $sample->cupboard = $request->cupboard;
            $sample->code = $request->code;
            $sample->years = $request->years;
            $sample->title = $request->title;
            $sample->batch = $request->batch;
            $sample->division = $request->division;
            $sample->planting_year = $request->planting_year;
            $sample->block = $request->block;
            $sample->row = $request->row;
            $sample->number_tree = $request->number_tree;
            $sample->description = $request->description;
            $sample->location = $request->location;
            $sample->photo = $request->file('photo')->store('sample');
            $sample->save();


            return response()->json([
                'success' => true,
            ]);
        }
        return view('sample.add');
    }

    public function update(Request $request,$id)
    {
        $sample = Sample::find($id);

        if ($request->isMethod('POST')) 
        {

            $validator = $request->validate([
                'title'=>'required|string|max:191',
                 'photo' => 'required|mimes:jpeg,png',
            ]);

            $sample->cupboard = $request->cupboard;
            $sample->code = $request->code;
            $sample->years = $request->years;
            $sample->title = $request->title;
            $sample->batch = $request->batch;
            $sample->division = $request->division;
            $sample->planting_year = $request->planting_year;
            $sample->block = $request->block;
            $sample->row = $request->row;
            $sample->number_tree = $request->number_tree;
            $sample->description = $request->description;
            $sample->location = $request->location;
            if($request->file != null){
                Storage::delete($sample->photo);
                     $sample->photo = $request->file('photo')->store('sample');   
            }
            $sample->save();
        }
        return view('sample.update',['sample' => $sample]);
    }

    public function view(Request $request, $id)
    {
        $sample = Sample::find($id);
        return view('sample.show', ['sample' => $sample]);
    }

    public function destroy(Request $request)
    {
        $sample = sample::find($request->id_delete);
            Storage::delete($sample->photo);
            $sample->delete();
    
        return response()->json([
            'success'=>true,
            'message'=>'Sample Berhasil dihapus',
        ]);
        
    }
}
