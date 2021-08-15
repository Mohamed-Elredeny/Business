<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//<<<<<<< HEAD
Route::group(['prefix' => LaravelLocalization::setLocale() . '/admin', 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', "web"]], function () {

    Route::group(['middleware' => ['auth','admin']],function() {
        Route::get('/dashboard',function (){
            return view('Admin.dashboard');
        })->name('dashboard');
        Route::resource('admincompanies', 'CompaniesController');
        Route::resource('users', 'UsersController');
        Route::put("users/updatePassword", "UsersController@updatePassword");
        Route::resource('admins', 'AdminController');
        Route::get('dashboard', 'MainController@index')->name('dashboard');
        Route::resource('categories','CategoriesController');
        Route::resource('reports','ReportsController');
        Route::get('reports/{report_id}','ReportsController@show')->name('reports.showinfo');
        Route::post('reports/{id}','ReportsController@userBan')->name('reports.ban');
        Route::get('admin/profile', 'ProfileController@edit')->name('admin.profile');
        Route::put('admin/profile', 'ProfileController@update')->name('admin.profileupdate');
        Route::put('admin/profile/password', 'ProfileController@password')->name('admin.profilepassword');
    });



    Route::get('/dashboard',function (){
        return view('Admin.dashboard');
    });


});
