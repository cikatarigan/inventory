<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class GoodLocation extends Model
{

	protected $fillable = ['good_id','location_id','name_shelf'];
	
	public function location()
    {
        return $this->hasMany('App\Models\location');
    }

	public function good()
    {
        return $this->hasMany('App\Models\Good');
    }

    public static function boot(){
        parent::boot();

        static::creating(function($model)
        {
            $model->user_id = Auth::id();
        });
    }

}
