<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make( $request->all(),[
                'email' => 'required|email',
                'password' => 'required|min:6'
            ] 
        );
        
        $credential = [
            'username'=> $request->username,
            'password'=> $request->password
        ];

        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if(auth()->attempt(array($fieldType => $request['username'], 'password' => $request['password'], 'status' => 'active'))){
                return redirect()->intended(route('home'));
    
        } else {
            $validator->after(function ($validator) {
                    $validator->errors()->add('username', 'Failed, your credential not match with our database!');
            });
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        else{
            return redirect()->back()->withInput($request->only('username', 'remember'));
        }

    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('auth.login');
    }
}
