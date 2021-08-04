<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    public function stockentry()
    {
    	return $this->hasMany(StockEntry::class);
    }

    public function locationshelf()
    {
        return $this->hasMany(LocationShelf::class);
    }


}
