<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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



}

