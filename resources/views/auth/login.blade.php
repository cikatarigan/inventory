<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>{{ config('app.name') }} | Log in Admin</title>
      <!-- Tell the browser to be responsive to screen width -->
      
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <!-- CSS -->
      <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
   </head>
   <body class="hold-transition login-page">
      <div class="login-box">
         <div class="login-logo">
            <a href="{{ config('app.url') }}"> <b>{{ config('app.name') }}</b> admin</a>
         </div>
         <!-- /.login-logo -->
         <div class="card">
            <div class="card-body login-card-body">
               <p class="login-box-msg">Sign in to start your session</p>
               <form class="form-horizontal" method="POST" action="{{ route('auth.login.submit') }}">
                  {{ csrf_field() }}
                  <div class="input-group mb-3 has-feedback">
                     <input id="username" type="username" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required  autofocus placeholder="Email or username">
                      <div class="input-group-append">
                        <div class="input-group-text">
                           <span class="fas fa-user"></span>
                        </div>
                     </div>
                     @if ($errors->has('username'))
                     <span class="invalid-feedback">
                     <strong>{{ $errors->first('username') }}</strong>
                     </span>
                     @endif
                  </div>
                  <div class="input-group mb-3  has-feedback">
                     <input id="password" placeholder="password" type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" required>
                     <div class="input-group-append">
                        <div class="input-group-text">
                           <span class="fas fa-lock"></span>
                        </div>
                     </div>
                     @if ($errors->has('password'))
                     <span class="invalid-feedback">
                     <strong>{{ $errors->first('password') }}</strong>
                     </span>
                     @endif
                  </div>
                  
                  <div class="row">
                     <div class="col-8">
                        <div class="icheck-primary">
                           <input type="checkbox" id="remember">
                           <label for="remember">
                           Remember Me
                           </label>
                        </div>
                     </div>
                     <!-- /.col -->
                     <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                     </div>
                     <!-- /.col -->
                  </div>
               </form>
            </div>
            <!-- /.login-card-body -->
         </div>
      </div>
      <!-- JS -->
      <script src="{{ mix('js/app.js') }}"></script>
      
   </body>
</html>