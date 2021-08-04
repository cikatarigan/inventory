<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class LocationShelf extends Model
{

	public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public static function boot(){
        parent::boot();

        static::creating(function($model)
        {
            $model->user_id = Auth::id();
        });
    }

    public function good_shelves(){
      return $this->hasMany(LocationShelf::class);
    }
}
