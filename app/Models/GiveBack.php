<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;

class GiveBack extends Model
{
     public static function boot(){
        parent::boot();

        static::creating(function($model)
        {
            $model->handle_by = Auth::id();
        });
    }

	public function stock_transaction()
    {
        return $this->morphOne(StockTransaction::class, "detailable");
    }


    public function good_location(){
    	return $this->belongsTo('App\Models\GoodLocation', 'location_id', 'id');
    }

	public function good()
    {
        return $this->belongsTo(Good::class);
    }

	public function User()
    {
        return $this->belongsTo(User::class);
    }

}
