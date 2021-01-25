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
View::addExtension('html', 'php');

Route::get('/', function () {
    return View::make('index');
});

Route::post('/login', 'MainController@login');

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
Route::post('save-collors', "MainController@saveCollors");
Route::post('/update-translate', 'MainController@updateTranslate');

