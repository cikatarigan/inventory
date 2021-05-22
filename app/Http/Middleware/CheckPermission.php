<?php

namespace App\Http\Middleware;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Illuminate\Support\Facades\Route;
use Closure;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $route = Route::currentRouteName();

        try{
            if (!$user->hasPermissionTo($route, 'web')){
                return $this->generate_response($request, new PermissionDoesNotExist(__('validation.permission.authorize')));
            }
        }catch (PermissionDoesNotExist $pdne){      
        }

        return $next($request);
        
    }

    public function generate_response($request ){
        if($request->ajax()) {
        return response()->json(
            [
                'data'=>'',
                'message'=>'Permintaan tidak dapat dilanjutkan. Mohon periksa hak akses anda.',
                'status'=>false,
                'code'=>401
            ], 401);
        }
        abort(401, "Not Authorized!");
    }
}
