<?php

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/run-artisan-command-migration','MyController@runArtisanCommand');
Route::get('/run-artisan/{command}',function($command){
    \Artisan::call($command);
    
});

Route::get('admin/register', 'Admin\Auth\Register_Controller@showLoginForm')->name('admin_register');
Route::post('admin/register', 'Admin\Auth\Register_Controller@register');

Route::group(['middleware' => 'admin_guest', 'namespace' => 'Admin'], function () {

    Route::get('admin/login', 'Auth\Login_Controller@showLoginForm')->name('admin_login');
    Route::post('admin/login', 'Auth\Login_Controller@adminLogin');
    //Password reset routes
    Route::get('admin/password/forgot', 'Auth\ForgotPassword_Controller@showLoginForm');
    Route::post('admin/password/email', 'Auth\ForgotPassword_Controller@sendMail');
    Route::get('admin/password/reset', 'Auth\ResetPassword_Controller@showResetForm');
    Route::post('admin/password/reset', 'Auth\ResetPassword_Controller@reset');
    
});
Route::post('admin/logout', 'Admin\Auth\Login_Controller@logout');

Route::group(['middleware' => 'admin_auth', 'namespace' => 'Admin'], function () {
	Route::get('admin/', function(){
    	return view('admin.welcome');
	});
    Route::get('admin/getData', 'Dashboard_Controller@getData');
	//Route::get('admin/feeds', 'Admin/Feeds_Contoller@show');

    //feed routes

    Route::get('admin/feeds', 'Feed_Controller@index');
    Route::get('admin/feeds/approve', 'Feed_Controller@approve');
    Route::get('admin/feeds/disapprove', 'Feed_Controller@disapprove');
    Route::get('admin/feeds/showUserInfo', 'Feed_Controller@showUserInfo');
    Route::get('admin/feeds/showFeedInfo', 'Feed_Controller@showFeedInfo');

    //User Routes 
    Route::get('admin/users', 'User_Controller@index');

    //photo competition
    Route::get('admin/photoCompetition', 'PhotoCompetition_Controller@index');

    //result 
    Route::get('admin/results', 'Result_Controller@index');

    // report 
    Route::get('admin/reports', 'Report_Controller@index');
});
