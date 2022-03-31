<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IndexController as Index;
use App\Http\Controllers\Admin\Land\IndexController as Land;
use App\Http\Controllers\Admin\User\User\IndexController as User;
use App\Http\Controllers\Admin\User\BuyLogController as BuyLog;
use App\Http\Controllers\Admin\User\ExchangeLogController as ExchangeLog;
use App\Http\Controllers\Admin\Vegetable\IndexController as Vegetable;
use App\Http\Controllers\Admin\System\Notice\IndexController as Notice;
use App\Http\Controllers\Admin\Logistics\OrderController as Order;
use App\Http\Controllers\Admin\Logistics\DistributionController as Distribution;
use App\Http\Controllers\Admin\Land\AddController as AddLand;
use App\Http\Controllers\Admin\Land\EditController as EditLand;
use App\Http\Controllers\Admin\Land\DelController as DelLand;
use App\Http\Controllers\Admin\User\User\AddController as AddUser;
use App\Http\Controllers\Admin\User\User\EditMemberInfoController as EditUser;
use App\Http\Controllers\Admin\User\User\DeleteMemberInfoController as DelUser;
use App\Http\Controllers\Admin\Vegetable\AddController as VegetableAdd;
use App\Http\Controllers\Admin\Vegetable\EditController as EditVegetable;
use App\Http\Controllers\Admin\Vegetable\DeleteController as DelVegetable;
use App\Http\Controllers\Admin\System\Notice\AddController as NoticeAdd;
use App\Http\Controllers\Admin\Logistics\EditDistributionController as EditDistribution;
use Illuminate\Support\Facades\Redis;
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
    Redis::get('key');
    return \Illuminate\Support\Facades\DB::table('member_info')->get();
});
Route::group(['prefix'=>'admin'],function(){
    Route::get('/',[Index::class,"index"]);
    Route::group(['prefix'=>'land'],function(){
        Route::get('/',[Land::class,"index"]);
        Route::get('/data/{page?}/{limit?}',[Land::class,"data"]);
        Route::get('add',[AddLand::class,'index']);
        Route::post('add/submit',[AddLand::class,'submit']);
        Route::get('edit/{id}',[EditLand::class,'index']);
        Route::put('edit/submit',[EditLand::class,'submit']);
        Route::delete('del/{id}',[DelLand::class,'index']);
    });
    Route::group(['prefix'=>'user'],function(){
        Route::get('/user',[User::class,"index"]);
        Route::post('/user/edit/submit',[EditUser::class,"submit"]);
        Route::get('/user/edit/{id}',[EditUser::class,"index"]);
        Route::delete('/user/del/{id}',[DelUser::class,"index"]);
        Route::get('/user/data/{page?}/{limit?}',[User::class,"data"]);
        Route::get('/buy_log',[BuyLog::class,"index"]);
        Route::get('/buy_log/{page?}/{limit?}',[BuyLog::class,"data"]);
        Route::get('/exchange_log',[ExchangeLog::class,"index"]);
        Route::get('/exchange_log/{page?}/{limit?}',[ExchangeLog::class,"data"]);
        Route::get('/user/add',[AddUser::class,"index"]);
        Route::post('/user/add/submit',[AddUser::class,"submit"]);
    });
    Route::group(['prefix'=>'vegetable'],function(){
        Route::get('/',[Vegetable::class,"index"]);
        Route::get('/data/{page?}/{limit?}',[Vegetable::class,"data"]);
        Route::post('/',[VegetableAdd::class,"submit"]);
        Route::delete('/{id}',[DelVegetable::class,"index"]);
        Route::get('/edit/{id}',[EditVegetable::class,"index"]);
        Route::post('/edit',[EditVegetable::class,"submit"]);
        Route::get('/add/',[VegetableAdd::class,"index"]);
    });
    Route::group(['prefix'=>'system'],function(){
        Route::get('/notice',[Notice::class,"index"]);
        Route::get('/notice/add',[NoticeAdd::class,"index"]);
        Route::post('/notice/add',[NoticeAdd::class,"submit"]);
        Route::get('/notice/data/{page?}/{limit?}',[Notice::class,"data"]);
    });
    Route::group(['prefix'=>'logistics'],function(){
        Route::get('/order',[Order::class,"index"]);
        Route::get('/order/data/{page?}/{limit?}',[Order::class,"data"]);
        Route::get('/distribution',[Distribution::class,"index"]);
        Route::put('/distribution',[EditDistribution::class,"index"]);
        Route::get('/distribution/data/{page?}/{limit?}',[Distribution::class,"data"]);
    });


});
//Route::any("/redis",[\App\Http\Controllers\IndexController::class,"index"]);
