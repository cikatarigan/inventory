<?php

namespace App\Models;
use App\User;
use Auth;
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

    public function location_shelf(){
        return $this->belongsTo(LocationShelf::class, 'location_shelf_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo(user::class);
    }

    public function allotment_item()
    {
        return $this->hasMany(AllotmentItem::class,'entry_id','id');
    }
  

}
