<?php

namespace App\Models;
use App\User;
use Auth;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    public static function boot(){
    	parent::boot();

    	static::creating(function($model)
    	{
    		$model->user_id = Auth::id();
    	});
    }

    public function stock_transaction()
    {
    	return $this->morphOne(StockTransaction::class, "detailable");
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }
    
    public function user()
    {
        return $this->belongsTo(user::class);
    }
  

}
