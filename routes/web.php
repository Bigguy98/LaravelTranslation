<?php

use Illuminate\Support\Facades\View;
use App\Http\Controllers\MainController;
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

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/update-translation', 'HomeController@updateTranslation')->name('update');



Route::get('/language-by-user/{userId}', 'MainController@languageByUser');
Route::post('/currentUser', 'MainController@currentUser');

Route::get('/isAuthenticate', 'MainController@isAuthenticate');
Route::post('/language', 'MainController@language');
Route::get('/language/list', 'MainController@languageList');
Route::get('/user', 'MainController@getUser');
Route::post('/user', 'MainController@addUser');
Route::put('/user', 'MainController@updateUser');
Route::post('/user/del', 'MainController@deleteUser');
Route::post('/refresh-db', 'MainController@refreshDB');

Route::post('/popover', 'MainController@popOver');
Route::post('/save-collors', "MainController@saveCollors");

Route::post('/hide-row', "MainController@hideRow");
Route::post('/show-row', "MainController@showRow");

