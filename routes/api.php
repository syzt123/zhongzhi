<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Controller;
use \App\Http\Controllers\Api\V1\UserController;
use \App\Http\Controllers\Api\V1\NoticeController;
use \App\Http\Controllers\Api\V1\CommUploadController;

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
    return ["code" => 200, "msg" => '欢迎', "data" => []];
});//登陆


Route::middleware("check.token")->prefix("v1")->group(function () {
    Route::get('/demo', function () {
        /*var_dump(config("comm_code.redis_prefix.token"));
        var_dump(\Illuminate\Support\Facades\Cache::put());
        $rs = \Illuminate\Support\Facades\Cache::get("6c7a61d1c0dcd748f6901d4c210bab83");
        var_dump(json_decode($rs, true));
        echo 'fff';*/
    });//测试
    // 公共上传 最好支持多上传
    Route::post('/com_uploads', [Controller::class, 'uploadImges']);
    // 公告
    Route::prefix("notice")->group(function () {
        Route::post('/info', [NoticeController::class, 'info']);//公告信息
    });


    //登录注册
    Route::prefix("user")->group(function () {
        Route::post('/register', [UserController::class, 'registerUser']);//注册  这种方式可以
        Route::post('/login', [UserController::class, 'loginUser']);//登陆  这种方式可以
        Route::post('/center', [UserController::class, 'center']);//用户中心  这种方式可以
    });
    //
});


//其他
Route::prefix("other")->group(function () {

});
