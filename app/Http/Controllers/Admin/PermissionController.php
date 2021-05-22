<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if( $request->isMethod('post') ){
            $model = Permission::all();
            return DataTables::of($model)->make();
        }

        return view('permission.index');
    }

}

