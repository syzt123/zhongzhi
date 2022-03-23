<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IndexController as Index;
use App\Http\Controllers\Admin\Land\IndexController as Land;
use App\Http\Controllers\Admin\User\IndexController as User;
use App\Http\Controllers\Admin\User\BuyLogController as BuyLog;
use App\Http\Controllers\Admin\User\ExchangeLogController as ExchangeLog;
use App\Http\Controllers\Admin\Vegetable\IndexController as Vegetable;
use App\Http\Controllers\Admin\System\NoticeController as Notice;
use App\Http\Controllers\Admin\Logistics\OrderController as Order;
use App\Http\Controllers\Admin\Logistics\DistributionController as Distribution;
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

//Route::get('/', function () {
//
//});
Route::group(['prefix'=>'admin'],function(){
    Route::get('/',[Index::class,"index"]);
    Route::group(['prefix'=>'land'],function(){
        Route::get('/',[Land::class,"index"]);
        Route::get('/data/{page?}/{limit?}',[Land::class,"data"]);
    });
    Route::group(['prefix'=>'user'],function(){
        Route::get('/',[User::class,"index"]);
        Route::get('/data/{page?}/{limit?}',[User::class,"data"]);
        Route::get('/buy_log',[BuyLog::class,"index"]);
        Route::get('/buy_log/{page?}/{limit?}',[BuyLog::class,"data"]);
        Route::get('/exchange_log',[ExchangeLog::class,"index"]);
        Route::get('/exchange_log/{page?}/{limit?}',[BuyLog::class,"data"]);
    });
    Route::group(['prefix'=>'vegetable'],function(){
        Route::get('/',[Vegetable::class,"index"]);
        Route::get('/data/{page?}/{limit?}',[Vegetable::class,"data"]);
    });
    Route::group(['prefix'=>'system'],function(){
        Route::get('/notice',[Notice::class,"index"]);
        Route::get('/notice/data/{page?}/{limit?}',[Notice::class,"data"]);
    });
    Route::group(['prefix'=>'logistics'],function(){
        Route::get('/order',[Order::class,"index"]);
        Route::get('/order/data/{page?}/{limit?}',[Order::class,"data"]);
        Route::get('/distribution',[Distribution::class,"index"]);
        Route::get('/distribution/data/{page?}/{limit?}',[Distribution::class,"data"]);
    });


});
//Route::any("/redis",[\App\Http\Controllers\IndexController::class,"index"]);
