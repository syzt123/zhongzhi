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
use \App\Http\Controllers\Api\V1\PlantController;
use \App\Http\Controllers\Api\V1\DeliveryOrderController;
use App\Http\Controllers\Api\V1\HarvestController;
use App\Http\Controllers\Api\V1\ExchangeController;
use \App\Http\Controllers\Api\V1\PayDemoController;
use \App\Http\Controllers\Api\V1\Ys\YsController;
use \App\Http\Controllers\Api\V1\TencentVodController;
use \App\Http\Controllers\Api\V1\PlatformController;

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

Route::post('/alipay_notify', [PayDemoController::class, 'payNotify']);//测试微信回调
Route::post('/wx_pay_notify', [PayDemoController::class, 'wxPayNotify']);//测试支付宝回调

//登录注册
Route::prefix("v1")->group(function () {
    Route::prefix("user")->group(function () {
        Route::post('/register', [UserController::class, 'registerUser']);//注册  这种方式可以
        Route::post('/login', [UserController::class, 'loginUser']);//登陆  这种方式可以
    });
    Route::prefix("ys")->group(function () {
        Route::get('/getLiveAddress', [YsController::class, 'getLiveAddress']);//萤石  这种方式可以
    });
    Route::prefix("vod")->group(function () {
        Route::post('/getSign', [TencentVodController::class, 'getSign']);//腾讯云点播签名
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
        // 新增订单 购买种子
        Route::post("/addOrder", [PaymentOrderController::class, 'addOrder']);// todo
        // 根据订单号更新状态 默认完成
        //Route::post("/updateOrderStatus", [PaymentOrderController::class, 'updateOrderStatus']);// todo


        // 新增物流邮寄
        Route::post('/addDelivery', [DeliveryOrderController::class, 'addDeliveryOrder']);
        // 物流详情
        Route::get('/getDetailDeliveryByOrderId/{order_id}', [DeliveryOrderController::class, 'getDetailDeliveryByOrderId']);//根据用户id 种子id 订单详细
        // 用户更新物流状态 设置为已完成。进行中由后台进行设置 确认收货
        Route::post('/updateDeliveryComplete', [DeliveryOrderController::class, 'updateDeliveryComplete']);//根据用户id 种子id 订单详细
        // 蔬菜兑换蔬菜币  蔬菜币兑换蔬菜（发物流） 存疑(有问题)
        //Route::post('/updateUserInfo', [UserController::class, 'updateUserInfo']);//根据用户id 种子id 订单详细

        // 用户的所有物流订单信息 根据状态过滤
        Route::post('/getDeliveryList', [DeliveryOrderController::class, 'getDeliveryList']);//根据用户id 种子id 订单详细

        //获取用户蔬菜分类列表
        Route::post('/userVegetableClassList', [UserController::class, 'userVegetableClassList']);//获取用户蔬菜分类列表
        //获取用户的蔬菜列表 包括各种阶段蔬菜
        Route::post('/userVegetableList', [UserController::class, 'userVegetableList']);//获取用户的蔬菜列表
        //获取用户的蔬菜列表 包括各种阶段蔬菜
        Route::post('/userVegetableExcludeTypeList', [UserController::class, 'userVegetableExcludeTypeList']);//获取用户的蔬菜列表 不包含分类

        //获取用户的蔬菜详情
        Route::post('/userDetailVegetable', [UserController::class, 'userDetailVegetable']);//获取用户的蔬菜详情

        //获取用户的兑换蔬菜列表
        Route::post('/userExchangeLogList', [UserExchangeLogController::class, 'userExchangeLogList']);//获取用户兑换的蔬菜列表
        //获取用户的兑换蔬菜详情
        Route::get('/userDetailExchangeLog/{id}', [UserExchangeLogController::class, 'userDetailExchangeLog']);//获取用户兑换的蔬菜详情


    });

    //平台数据
    Route::prefix("platform")->group(function () {
        //获取平台的蔬菜列表 仓库 无用户id
        Route::post('/vegetableList', [PlatformController::class, 'vegetableList']);//获取用户的蔬菜列表 不包含分类

    });

    //土地列表
    Route::prefix("land")->group(function () {
        Route::post('/lists', [LandController::class, 'landLists']);//土地列表
    });

    //蔬菜类型列表
    Route::prefix("vegetable")->group(function () {
        Route::post('/typeLists', [VegetableTypeController::class, 'typeLists']);//蔬菜类型列表
        Route::post('/lists', [VegetableTypeController::class, 'Lists']);//蔬菜列表
        Route::get('/seed', [PlantController::class, 'seed']);//可种植蔬菜类型列表
        Route::get('/planted', [PlantController::class, 'planted']);//已种植的蔬菜类型列表
    });

    // 采收模块
    Route::prefix("harvest")->group(function () {
        Route::post('/warehousing', [HarvestController::class, 'warehousing']);//入库
        Route::post('/distribution', [HarvestController::class, 'distribution']);//物流配送
    });
    // 蔬菜兑换
    Route::prefix("exchange")->group(function () {
        Route::post('vegetable', [ExchangeController::class, 'vegetable']);//兑换蔬菜
        Route::post('lists', [ExchangeController::class, 'lists']);//可兑换蔬菜列表
        Route::post('coin', [ExchangeController::class, 'vegetableToCoin']);//可兑换蔬菜列表
    });
});




