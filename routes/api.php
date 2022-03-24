<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\V1\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::get('/', function () {
    echo 'fff';
});//登陆

Route::prefix("user")->group(function () {

    Route::post('/register', [LoginController::class, 'registerUser']);//登陆  这种方式可以
    Route::post('/login', [LoginController::class, 'loginUser']);//登陆  这种方式可以

});

Route::get('/cc', [LoginController::class, 'login']);//登陆  这种方式可以
//Route::middleware([''])->group(function () {
//
//    Route::controller(LoginController::class)->group(function () {
//        Route::get('/demo', 'login');//登陆
//    });
//
//});
