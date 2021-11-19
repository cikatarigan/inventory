<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index($id){
           return view('home.profile')->with('user', User::find($id));

    }

    public function setting(Request $request)
    {
        $id = intval($request->id);

        $validator = $request->validate([
            'name'  => 'nullable|string|max:191',
            'image'=>'nullable|mimes:jpeg,jpg,png',
        ]);


        $file = $request->file('image');
        $user = User::find($id);
        if(!empty($file)){
            Storage::delete($user->image);
            $user->image = $file->store('user');
            $image = Image::make(public_path('storage/'.$user->image))->resize(160,160)->save();
        }
        if(!empty($request->name)){
            $user->name  = $request->name;
        }
        $user->save();



        return response()->json([
            'success' => true,
        ]);
    }

    public function password(Request $request)
    {
        $id = intval($request->id);
        $user = User::find($id);

        if( !(Hash::check($request->current_password, $user->password)) ){
            return response()->json([
                'success' => false,
                'message' => 'Your current password does not matches with the password you provided. Please try again.',
            ]);
        }

        $validator = $request->validate([
            'new_password'         => 'required|min:6',
            'new_password_confirm' => 'required_with:new_password|same:new_password|min:6',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
        ]);
    }
}
