<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Good extends Model
{
	use SoftDeletes;

    const EXPIRED   = "Expired";
    const GOOD = "Good";

    public function good_images()
	{
		return $this->hasMany('App\Models\GoodImage', 'good_id', 'id');
	}

    public function good_shelves(){
        return $this->hasMany(GoodShelf::class);
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

    //  public function getBalanceByShelf($location_shelf_id)
    //  {
    //  	$last_transaction = $this->stock_transaction()
    //     ->where('location_shelf_id', $location_shelf_id)
    //  	->orderBy('created_at', 'desc')->first();

    //  	return ($last_transaction) ? $last_transaction->end_balance : 0 ;
    //  }

     public function borrow(){
        return $this->hasMany(Borrow::class);
     }

    public function stockentry()
    {
        return $this->hasMany(StockEntry::class);
    }

}
