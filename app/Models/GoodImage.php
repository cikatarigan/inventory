<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodImage extends Model
{
    public function image()
    {
        return $this->belongsTo('App\Models\Image');
    }

   public function good()
   {
   	   return $this->belongsTo('App\Models\Good');
   }
}
