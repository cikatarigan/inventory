<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use Yajra\DataTables\DataTables;
use validator;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if( $request->isMethod('post') ){
            $model = Role::all();
            return DataTables::of($model)->make();
        }

        return view('role.index');
    }

	public function create(Request $request)
    {
         if ($request->isMethod('POST')){

            $validator = $request->validate([
                'name'=>'required|string|max:60',
                'description' => 'required'
            ]);

            $role = New Role;
            $role->name = $request->name;
            $role->description = $request->description;
            $role->guard_name = 'web';
            $role->save();

            return response()->json([
                'success' => true,
                 'message'   => 'Role Successfully Add'
            ]);
        }
    }

    public function edit(Request $request, $id)
    {

        if ($request->isMethod('POST')){

            $role = Role::find($request->id);
            $role->permissions()->detach();

            foreach ($request->permissions as $permission) {
                    $role->givePermissionTo($permission);

            }

                return response()->json([
                    'success' => true,
                     'message'   => 'Permission Successfully Edited'
                ]);
            }

            $role = Role::find($id);

            if ($role) {

                $permission = Permission::with(['roles' => function ($query) use ($role) {
                    $query->where('id', '=', $role->id);
                }])
                    ->where('guard_name', 'web')
                    ->orderBy('name')->get();
            } else {
                $this->response->status = false;
                $this->response->message = __('admin_page.user_management.role_notfound');
            }


        return view('role.edit',['role' => $role, 'permission' => $permission]);
    }

}
