<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Good extends Model
{
	use SoftDeletes;

    public function good_images()
	{
		return $this->hasMany('App\Models\GoodImage', 'good_id', 'id');
	}

    public function good_location(){
    	return $this->belongsTo('App\Models\GoodLocation', 'location_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
	public function stock_transaction()
    {
     	return $this->hasMany(StockTransaction::class);
    }

    public function getBalanceByWarehouse($location_id)
     {
     	$last_transaction = $this->stock_transaction()
        ->where('location_id', $location_id)
     	->orderBy('created_at', 'desc')->first();

     	return ($last_transaction) ? $last_transaction->end_balance : 0 ;
     }
}
