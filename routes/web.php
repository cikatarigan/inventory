<?php

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use App\Http\Middleware\CheckPermission;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');    

      Route::get('barcode/{barcode}' , function($barcode){
            return response(base64_decode(\DNS1D::getBarcodePNG($barcode, 'I25')))->withHeaders(['Content-Type' =>'image/png']);
      });
            Route::get('find/locations', 'HomeController@locations');
            Route::get('find/goods/{location}', 'HomeController@goods');

            Route::get('find/users', 'HomeController@users');
            Route::get('find/borrows/{user}', 'HomeController@borrows');


            Route::get('/login', 'Auth\LoginController@showLoginForm')->name('auth.login');
		Route::post('/login', 'Auth\LoginController@login')->name('auth.login.submit');
            Route::post('/logout', 'Auth\LoginController@logout')->name('auth.logout');
        
        

        Route::middleware(['auth', CheckPermission::class])->group(function () {
            Route::get('/dashboard', 'HomeController@index')->name('home');    
            // Profile
            Route::get('/profile/{id}', 'Admin\AdminController@profile')->name('admin.profile');
	    	Route::post('/profile/setting', 'Admin\AdminController@setting')->name('admin.setting');
    		Route::post('/profile/password', 'Admin\AdminController@password')->name('admin.password');

            //User
            Route::match(['get', 'post'], 'user',	'Admin\UserController@index')->name('user.index');
            Route::post('user/add',				'Admin\UserController@create')->name('user.store');
            Route::post('user/update/{id}',			'Admin\UserController@update')->name('user.update');
            Route::post('user/delete', 		'Admin\UserController@delete')->name('user.destroy');
            Route::post('user/restore', 		'Admin\UserController@restore')->name('user.restore');
            Route::post('user/sync', 		'Admin\UserController@sync')->name('user.sync');
            Route::post('user/change-password',		'Admin\UserController@change')->name('user.change');
            //Role

            Route::match(['get', 'post'], 'role',	'Admin\RoleController@index')->name('role.index');
            Route::post('role/add',				'Admin\RoleController@create')->name('role.store');
            Route::match(['POST', 'GET'], '/role/edit/{id?}', 'Admin\RoleController@edit')->name('role.edit');

            //Permission                    
            Route::match(['get', 'post'], 'permission',	'Admin\PermissionController@index')->name('permission.index');

            //Location
            Route::match(['get', 'post'], 'location',	'Admin\locationController@index')->name('location.index');
            Route::post('location/add',				'Admin\locationController@create')->name('location.store');
            Route::post('location/update/{id}',			'Admin\locationController@update')->name('location.update');
            Route::post('location/delete/', 		'Admin\locationController@destroy')->name('location.destroy');
            Route::match(['POST', 'GET'], 'location/trash', 'Admin\locationController@trash')->name('location.trash');
            Route::post('location/restore', 		'Admin\locationController@restore')->name('location.restore');
            
            //Good
            Route::match(['get', 'post'], 'good',	'Admin\GoodController@index')->name('good.index');
            Route::match(['get','post'],'good/add', 'Admin\GoodController@create')->name('good.store');
            Route::match(['get', 'post'],'good/update/{id}', 'Admin\GoodController@update')->name('good.update');
            Route::post('good/delete', 		'Admin\GoodController@destroy')->name('good.destroy');
            Route::match(['POST', 'GET'], 'good/trash', 'Admin\GoodController@trash')->name('good.trash');
            Route::post('good/restore', 		'Admin\GoodController@restore')->name('good.restore');
            Route::post('good/location', 		'Admin\GoodController@location')->name('good.location');
            
            //StockEntry
            Route::match(['get', 'post'], 'receipt',	'Admin\StockEntryController@index')->name('stockentry.index');
            Route::get('find/shelf/{location}', 'Admin\StockEntryController@shelf');
		Route::match(['get', 'post'],'receipt/add', 'Admin\StockEntryController@create')->name('stockentry.add');

            //CheckGoods
            Route::match(['get', 'post'], 'stock/goods',    'Admin\StockController@index')->name('stock.index');
            Route::match(['get', 'post'], 'stock/goods/details/{id}',   'Admin\StockController@detail')->name('stock.detail');

            //Allotment
            Route::match(['get', 'post'], 'allotment',    'Admin\AllotmentController@index')->name('allotment.index');
            Route::match(['get', 'post'],'allotment/check', 'Admin\AllotmentController@check')->name('allotment.check');
            Route::match(['get', 'post'],'allotment/add', 'Admin\AllotmentController@create')->name('allotment.add');

            //Borrow
            Route::match(['get', 'post'], 'borrow',    'Admin\BorrowController@index')->name('borrow.index');
            Route::match(['get', 'post'],'borrow/check', 'Admin\BorrowController@check')->name('borrow.check');
            Route::match(['get', 'post'],'borrow/add', 'Admin\BorrowController@create')->name('borrow.add');

            //Allotment
            Route::match(['get', 'post'], 'return',    'Admin\ReturnController@index')->name('return.index');
            

            //Sample
            Route::match(['get', 'post'], 'sample',	'Admin\SampleController@index')->name('sample.index');
            Route::match(['get', 'post'],'sample/add', 'Admin\SampleController@add')->name('sample.store');
            Route::match(['get', 'post'],'sample/update/{id}' ,'Admin\SampleController@update')->name('sample.update');
            Route::get('sample/view/{id}', 'Admin\SampleController@view')->name('sample.view');
            Route::post('sample/destroy', 'Admin\SampleController@destroy')->name('sample.destroy');

            //Return
            Route::match(['get', 'post'], 'return',   'Admin\ReturnController@index')->name('return.index');
            Route::match(['get', 'post'],'return/add', 'Admin\ReturnController@create')->name('return.add');
            
        });	
