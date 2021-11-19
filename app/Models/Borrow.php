<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;

class Borrow extends Model
{
    const STILL_BORROW	= "Still Borrow";
    const EXPIRED	= "Expired";
    const DONE = "Done";

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

	public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function borrow_item()
    {
        return $this->hasMany(BorrowItem::class,'borrow_id','id');
    }

}
