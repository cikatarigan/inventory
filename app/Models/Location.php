<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    public function stockentry()
    {
    	return $this->hasMany(StockEntry::class);
    }

    public function goodlocation()
    {
        return $this->hasMany(GoodLocation::class);
    }



    // public function getGood($good_id)
    //  {
    //     $data = $this->stockentry()->with('good')
    //     ->where('good_id', $good_id)->get();

    //     return ($data) ;
    //  }  



}
