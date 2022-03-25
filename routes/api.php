<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\V1\UserController;

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


Route::middleware("check.token")->prefix("v1")->group(function () {
    Route::get('/demo', function () {
        /*var_dump(config("comm_code.redis_prefix.token"));
        var_dump(\Illuminate\Support\Facades\Cache::put());
        $rs = \Illuminate\Support\Facades\Cache::get("6c7a61d1c0dcd748f6901d4c210bab83");
        var_dump(json_decode($rs, true));
        echo 'fff';*/
    });//测试


    //登录注册
    Route::prefix("user")->group(function () {
        Route::post('/register', [UserController::class, 'registerUser']);//注册  这种方式可以
        Route::post('/login', [UserController::class, 'loginUser']);//登陆  这种方式可以
        Route::post('/index', [UserController::class, 'index']);//用户首页  这种方式可以
    });
    //
});


//其他
Route::prefix("other")->group(function () {

});
