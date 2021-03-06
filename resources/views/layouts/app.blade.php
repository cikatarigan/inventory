<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      {{--
      <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
      --}}
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>{{ config('app.name') }} | AdminPanel</title>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
      <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
      @yield('style')
      <style>
         td.details-control {
         background: url('{{asset('images/details_open.png')}}') no-repeat center center;
         cursor: pointer;
         }
         tr.shown td.details-control {
         background: url('{{asset('images/details_close.png')}}') no-repeat center center;
         }
      </style>
   </head>
   <body class="sidebar-mini" style="height: auto;">
      <div id="app">
      <div class="wrapper">
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-dark">
         <!-- Left navbar links -->
         <ul class="navbar-nav">
         <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
         </li>
         </ul>
         <ul class="navbar-nav ml-auto">
            @role('admin')
            <li class="nav-item">
               <a class="nav-link" href="{{route('scan')}}" role="button">
               Scan Barcode <i class="fas fa-th-large"></i>
               </a>
            </li>
            @endrole
            <li class="nav-item dropdown">
               <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
               <i class="fas fa-user"></i>
               </a>
               <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
               <div class="dropdown-divider"></div>
               <a href="{{ route('auth.logout') }}" class="dropdown-item"
                  onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
               <i class="fas fa-sign-out-alt mr-2"> Sign Out</i>
               </a>
               <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
               </form>
            </li>
         </ul>
      </nav>
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="/" class="brand-link text-center">
      <span class="brand-text font-weight-light" style="font-size: 1rem;">INVENTORY PKT</span>
      </a>
      <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
         <div class="image">
            @if(Auth::user()->image == null)
            <img src="{{asset('images/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
            @else
            <img src="{{asset('storage/'.Auth::user()->image)}}" class="img-circle elevation-2" alt="User Image">
            @endif
         </div>
         <div class="info">
            <a href="/profile/{{ Auth::user()->id }}" class="d-block" style="text-transform: capitalize;">{{Auth::user()->name}}</a>
         </div>
      </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">Menu</li>
                <li class="nav-item">
                <a href="{{route('home')}}" class="nav-link">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <p>Dashboard</p>
                </a>
                </li>
                @if(Auth::user()->hasPermissionTo('sample.index','web'))
                <li class="nav-item">
                <a href="{{route('sample.index')}}" class="nav-link">
                <i class="nav-icon fas fa-vial "></i>
                    <p>Samples</p>
                </a>
                </li>
                @endif

            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-database"></i>
                    <p>
                    Master Data
                    <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                @if(Auth::user()->hasPermissionTo('location.index','web'))
                <li class="nav-item">
                <a href="{{route('location.index')}}" class="nav-link">
                <i class="nav-icon fas fa-map-marked-alt "></i>
                    <p>Location</p>
                </a>
                </li>
                @endif
                @if(Auth::user()->hasPermissionTo('good.index','web'))
                <li class="nav-item">
                <a href="{{route('good.index')}}" class="nav-link">
                <i class="nav-icon fas fa-box-open "></i>
                    <p>Goods</p>
                </a>
                </li>
                @endif
                @if(Auth::user()->hasPermissionTo('unit.index','web'))
                <li class="nav-item">
                <a href="{{route('unit.index')}}" class="nav-link">
                <i class="nav-icon fas fa-weight-hanging "></i>
                    <p>Units</p>
                </a>
                </li>
                @endif
                </ul>
            </li>

                @if(Auth::user()->hasPermissionTo('stockentry.index','web'))
                <li class="nav-item">
                <a href="{{route('stockentry.index')}}" class="nav-link">
                <i class="nav-icon fas fa-receipt"></i>
                    <p>Stock Entry</p>
                </a>
                </li>
                @endif
                @if(Auth::user()->hasPermissionTo('stockentry.index','web'))
                <li class="nav-item">
                <a href="{{route('stock.index')}}" class="nav-link">
                <i class="nav-icon fas fa-boxes"></i>
                    <p>Check Goods</p>
                </a>
                </li>
                @endif
                @role('admin')
                <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-clipboard-list"></i>
                    <p>
                    Log Data
                    <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                @if(Auth::user()->hasPermissionTo('borrow.index','web'))
                <li class="nav-item">
                <a href="{{route('borrow.index')}}" class="nav-link">
                <i class="nav-icon fas fa-hand-holding"></i>
                    <p>Peminjaman</p>
                </a>
                </li>
                @endif
                @if(Auth::user()->hasPermissionTo('return.index','web'))
                <li class="nav-item">
                <a href="{{route('return.index')}}" class="nav-link">
                <i class="nav-icon fas fa-undo-alt"></i>
                    <p>Pengembalian</p>
                </a>
                </li>
                @endif
            @if(Auth::user()->hasPermissionTo('allotment.index','web'))
                <li class="nav-item">
                <a href="{{route('allotment.index')}}" class="nav-link">
                <i class="nav-icon fab fa-hive"></i>
                    <p>Pemberian</p>
                </a>
                </li>
                @endif
            </ul>
            </li>
            @endrole

                @if(Auth::user()->hasPermissionTo('expired.index','web'))
                <li class="nav-item">
                <a href="{{route('expired.index')}}" class="nav-link">
                <i class="nav-icon fas fa-exclamation-triangle"></i>
                    <p>Expired</p>
                </a>
                </li>
                @endif
            @if(Auth::user()->hasPermissionTo('user.index','web'))
                <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                    User Management
                    <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                    <a href="{{route('user.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengguna</p>
                    </a>
                    </li>
                    @if(Auth::user()->hasPermissionTo('role.index','web'))
                    <li class="nav-item">
                    <a href="{{route('role.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Role</p>
                    </a>
                    </li>
                    @endif
                    @if(Auth::user()->hasPermissionTo('permission.index','web'))
                    <li class="nav-item">
                    <a href="{{route('permission.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Permission</p>
                    </a>
                    </li>
                    @endif
                </ul>
                </li>
                @endif
            </ul>
        </nav>
               <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
    </aside>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper" style="min-height: 1200.88px;">
            @yield('content')
         </div>
         <!-- /.content-wrapper -->
         <!-- Main Footer -->
         <footer class="main-footer">
            <strong>Copyright ?? <?php echo date("Y"); ?> <a href="#">pktgroup.com</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
               <b>Version</b> 1.0.0</div>
         </footer>
         <div id="sidebar-overlay"></div>
      </div>
    </div>


   </body>
   <!-- Scripts -->
   <script src="{{ asset('js/app.js') }}" ></script>
   <script src="{{asset('js/instascan.min.js')}}"></script>
      @yield('script')
    <script>
      toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      }
   </script>
   </body>
</html>
