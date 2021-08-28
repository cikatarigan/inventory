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

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
	public function good()
    {
        return $this->belongsTo(Good::class);
    }

	public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function location_shelf(){
        return $this->belongsTo(LocationShelf::class, 'location_shelf_id', 'id');
    }

}
