<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\models\location;

class StockTransaction extends Model
{
    const TYPE_IN	= "IN";
    const TYPE_OUT = "OUT";

    public function location(){
          return $this->belongsTo(Location::class);
    }

    public function good(){
          return $this->belongsTo(Good::class);
    }

    public function stock_entry(){
    	return $this->belongsTo('App\Models\StockEntry', 'location_id', 'location_id');
    }

}

