<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\user;
use DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $roles = Role::pluck('name','name')->all();
        

        if( $request->isMethod('post') ){
            $model =User::with('roles');
            return DataTables::of($model)->make();
        }

        return view('user.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
          if ($request->isMethod('POST')){

            $validator = $request->validate([
                'name'=>'required|string|max:60',
                'username'=>'required|max:60',
                'email'=>'required|email|unique:users', 
                'password'=>'required|min:6', 
            ]);

            
            $user = New User;
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->status = 'active';
            $user->save();

            $user->assignRole($request->input('roles'));

            return response()->json([
                'success' => true,
                 'message'   => 'User Successfully Add'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    

    public function update(Request $request, $id)
    {
       if ($request->isMethod('POST')){
         $this->validate($request, [
            'roles' => 'required'
        ]);

                $user = User::find($id);
                $roles = Role::pluck('name','name')->all();
                $userRole = $user->roles->pluck('name','name')->all();
                DB::table('model_has_roles')->where('model_id',$id)->delete();
                $user->assignRole($request->input('roles'));

                return response()->json([
                        'success'=>true,
                        'message'   => 'User Successfully Updated'
                ]);
        }
        return view('user.index',compact('user','roles','userRole'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $user = User::find($request->id);
        $user->status = 'banned';
        $user->save();
            return response()->json([
                'success'=>true,
                'message'   => 'User Successfully Banned'
            ]);
    }

    public function restore(Request $request)
    {
        $user = User::find($request->id);
        $user->status = 'active';
        $user->save();
            return response()->json([
                'success'=>true,
                'message'   => 'User Successfully Restore'
            ]);
    }

    public function change(Request $request)
    {
        $user = User::find($request->id);

        $validator = $request->validate([
            'password'         => 'required|min:6',
            'new_password_confirm' => 'required_with:password|same:password|min:6',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();
            return response()->json([
                'success'=>true,
                'message'   => 'User Successfully Change'
            ]);
    }
}
