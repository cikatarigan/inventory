<?php

namespace App\Models;
use App\User;


class Role extends \Spatie\Permission\Models\Role
{
    protected $table = 'roles';
}
