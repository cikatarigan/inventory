<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowItem extends Model
{
    public function borrow()
    {
        return $this->belongsTo(Borrow::class, 'borrow_id', 'id');
    }
}
