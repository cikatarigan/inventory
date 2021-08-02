<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodShelf extends Model
{
     protected $table = 'good_shelves';

     protected $fillable = ['good_id', 'location_shelf_id'];
     public $timestamps = false;

     public function location()
    {
        return $this->belongsTo(Location::class);
    }

       public function good()
   {
   	   return $this->belongsTo('App\Models\Good');
   }

}
