<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Controller;
use \App\Http\Controllers\Api\V1\UserController;
use \App\Http\Controllers\Api\V1\NoticeController;
use \App\Http\Controllers\Api\V1\UserExchangeLogController;
use \App\Http\Controllers\Api\V1\PaymentOrderController;
use \App\Http\Controllers\Api\V1\LandController;
use \App\Http\Controllers\Api\V1\VegetableTypeController;

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

/*Route::get('/alipay', [\App\Http\Controllers\Api\V1\PayDemoController::class, 'pay']);//测试支付宝支付
Route::get('/wxpay', [\App\Http\Controllers\Api\V1\PayDemoController::class, 'wxPay']);//测试微信支付
Route::post('/alipay_notify', [\App\Http\Controllers\Api\V1\PayDemoController::class, 'payNotify']);//测试微信回调
Route::post('/wx_pay_notify', [\App\Http\Controllers\Api\V1\PayDemoController::class, 'wxPayNotify']);//测试支付宝回调*/

//登录注册
Route::prefix("v1")->group(function () {
    Route::prefix("user")->group(function () {
        Route::post('/register', [UserController::class, 'registerUser']);//注册  这种方式可以
        Route::post('/login', [UserController::class, 'loginUser']);//登陆  这种方式可以
    });
});

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
        Route::post('/center', [UserController::class, 'center']);//用户中心  这种方式可以
        Route::post('/updateUserInfo', [UserController::class, 'updateUserInfo']);//更新用户头像地址等信息


        // 新增蔬菜兑换
        Route::post('/addExchangeLog', [UserExchangeLogController::class, 'addExchangeLog']);
        // 新增订单
        Route::post("/addOrder", [PaymentOrderController::class, 'addOrder']);// todo
        // 根据订单号更新状态
        Route::post("/updateOrderStatus", [PaymentOrderController::class, 'updateOrderStatus']);// todo
    });

    //土地列表
    Route::prefix("land")->group(function () {
        Route::post('/lists', [LandController::class, 'landLists']);//土地列表
    });

    //蔬菜类型列表
    Route::prefix("vegetable")->group(function () {
        Route::post('/typeLists', [VegetableTypeController::class, 'typeLists']);//蔬菜类型列表
    });
});


//其他
Route::prefix("other")->group(function () {

});
