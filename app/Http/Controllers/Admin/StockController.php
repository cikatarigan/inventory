<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\Location;
use App\Models\LocationShelf;

use App\Models\StockTransaction;
use Yajra\DataTables\DataTables;


class StockController extends Controller
{
	public function index(Request $request){


		if( $request->isMethod('post') ){
            $model = Good::all();
            $location = Location::all();
            $sublocation = LocationShelf::with(['location'])->get();


            return DataTables::of($model)->addColumn('stock', function ($good) use ($sublocation){
            	$stocks = [];
                foreach ($sublocation as $sublocation) {
                    $stocks[] =[
                        'location'=> $sublocation->name_shelf,
                        'sublocation' => $sublocation->location->name,
                        'stock' => $good->getBalanceByShelf($sublocation->id)
                    ];
                }
            return $stocks;
            })->make();
		}
		return view('stock.index');
	}

    public function detail(Request $request, $id){

        $id =  $request->id;
        $location = Location::all();
        $shelf = LocationShelf::all();
        $good = Good::find($id);

        if ($request->isMethod('post')){

        $model  = StockTransaction::with(['location_shelf.location','good'])->where('good_id', $id)->orderBy('created_at', 'DESC');

            if ($request->location_id) {
                  $model->where('location_id', '=', $request->location_id);
            };

            if( $request->date_start){
                $startDate = $request->date_start;
                $endDate = $request->date_end;
                $model->whereDate('created_at', '>=', $startDate )->whereDate('created_at', '<=' ,$endDate );

            }
            return DataTables::of($model)->make();
        }



        return view('stock.detail',['id' =>$id, 'location' => $location, 'good' => $good]);
    }




}
