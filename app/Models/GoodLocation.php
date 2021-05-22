<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
