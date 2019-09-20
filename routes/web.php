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

Route::get('{key}/token', ['middleware' => ['checkgetquery', 'checkgettimeurl'], function ($key) {
    echo csrf_token();
}]);


Route::get('/test', ['middleware' => ['checkgetquery'], function (Request $request) {
    return $request->all();
}]);

Route::post('/user/login', function (Request $request) {
    $user = User::where("username", $request["username"])->first();

    if ($user == null) {
        return "Username doesn't exist";
    }

    $password = $user->password;
    if (Illumina::CompareIlluminaHashes($password, $request["password"])) {
        $user->logged_in = true;
        $user->save();
        return $user;
        
    } else {
        return "Username/password incorrect";
    }
});


Route::resource('user', 'UserController');
