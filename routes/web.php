<?php

use App\Player;
use App\Play;
use App\Score;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\Illumina;
use App\User;
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

Route::get('{key}/token', ['middleware' => 'checkgettimeurl', function ($key) {
    return csrf_token();
}]);
Route::get('/token',  function () {
    return csrf_token();
});


Route::get('/test', ['middleware' => ['checkgetquery'], function (Request $request) {
    return $request->all();
}]);


Route::put('/user/login', 'UserController@login');
Route::get('/user/exists/{key}', 'UserController@exists');
Route::post('/user/verifyemail/{key}',  'UserController@verify');
Route::put('/user/logout',  'UserController@logout');
Route::resource('user', 'UserController');

Route::post('/test', function (Request $request) {
    $request["response_message"] = "Hello";
    return $request;
});
