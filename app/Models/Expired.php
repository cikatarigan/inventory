<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expired extends Model
{

    public function location()
    {	
        return $this->belongsTo(Location::class);
    }

	public function good()
    {
        return $this->belongsTo(Good::class);
    }

   public function stock_transaction()
    {
    	return $this->morphOne(StockTransaction::class, "detailable");
    }
}
