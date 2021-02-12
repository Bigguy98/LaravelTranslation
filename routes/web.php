<?php

use Illuminate\Support\Facades\View;
use App\Http\Controllers\AdminController;
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

Auth::routes();

//User section routes
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/update-translation', 'HomeController@updateTranslation')->name('update');

//Admin authentication routes
Route::get('/admin', function(){return view('admin');});
Route::post('/admin-login', 'AdminController@adminLogin');

//User management routes
Route::get('/user', 'AdminController@getUser');


Route::get('/language-by-user/{userId}', 'AdminController@languageByUser');
Route::post('/currentUser', 'AdminController@currentUser');
Route::get('/isAuthenticate', 'AdminController@isAuthenticate');
Route::post('/language', 'AdminController@language');
Route::get('/language/list', 'AdminController@languageList');

Route::post('/user', 'AdminController@addUser');
Route::put('/user', 'AdminController@updateUser');
Route::post('/user/del', 'AdminController@deleteUser');
Route::post('/refresh-db', 'AdminController@refreshDB');
Route::post('/popover', 'AdminController@popOver');

Route::post('/hide-row', "AdminController@hideRow");
Route::post('/show-row', "AdminController@showRow");

