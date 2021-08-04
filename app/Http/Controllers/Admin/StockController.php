<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\Location;
use App\Models\StockTransaction;
use DataTables;


class StockController extends Controller
{
	public function index(Request $request){
		if( $request->isMethod('post') ){
            $model = Good::all();
            $location = Location::all(); 
            return DataTables::of($model)->addColumn('stock', function ($good) use ($location){
            	$stocks = [];
                foreach ($location as $location) {
                    $stocks[] =[
                        'location'=> $location->name,
                        'stock' => $good->getBalanceByWarehouse($location->id)
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
        $good = Good::find($id);
        
        if ($request->isMethod('post')){

        $model  = StockTransaction::where('good_id', $id)->with(['stockentries.location_shelf.location','good'])->orderBy('created_at', 'DESC'); 

            if ($request->location_id) {                   
                  $model->where('location_id', '=', $request->location_id);
            };

            if( $request->date_start){
                $startDate = $request->date_start;
                $endDate = $request->date_end;
                $model->whereDate('created_at', '>=', $startDate )->whereDate('created_at', '<=' ,$endDate );

            }
            return DataTables::of($model)->addColumn('location_shelf', function($model){
                return $model->location->location_shelf;
            })->make();
        }



        return view('stock.detail',['id' =>$id, 'location' => $location, 'good' => $good]);
    }
}
