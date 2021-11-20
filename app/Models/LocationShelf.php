<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class LocationShelf extends Model
{

    use SoftDeletes;

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

    public function good_shelf(){
        return $this->hasMany(GoodShelf::class);
    }
}
