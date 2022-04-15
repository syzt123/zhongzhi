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

Route::get('/', [\App\Http\Controllers\Admin\LoginController::class,'index'])->middleware('admin');
Route::post('/',[\App\Http\Controllers\Admin\LoginController::class,'login']);
Route::group(['prefix'=>'admin'],function(){
    Route::get('/loginout',[\App\Http\Controllers\Admin\LoginController::class,'loginout'])->middleware('admin');
    Route::get('/',[Index::class,"index"])->middleware('admin');
    Route::group(['prefix'=>'land'],function(){
        Route::get('/',[Land::class,"index"])->middleware('admin');
        Route::get('/data/{page?}/{limit?}',[Land::class,"data"])->middleware('admin');
        Route::get('add',[AddLand::class,'index'])->middleware('admin');
        Route::post('add/submit',[AddLand::class,'submit'])->middleware('admin');
        Route::get('edit/{id}',[EditLand::class,'index'])->middleware('admin');
        Route::put('edit/submit',[EditLand::class,'submit'])->middleware('admin');
        Route::delete('del/{id}',[DelLand::class,'index'])->middleware('admin');
    });
    Route::group(['prefix'=>'user'],function(){
        Route::get('/user',[User::class,"index"])->middleware('admin');
        Route::post('/user/edit/submit',[EditUser::class,"submit"])->middleware('admin');
        Route::get('/user/edit/{id}',[EditUser::class,"index"])->middleware('admin');
        Route::delete('/user/del/{id}',[DelUser::class,"index"])->middleware('admin');
        Route::get('/user/data/{page?}/{limit?}',[User::class,"data"])->middleware('admin');
        Route::get('/buy_log',[BuyLog::class,"index"])->middleware('admin');
        Route::get('/buy_log/{page?}/{limit?}',[BuyLog::class,"data"])->middleware('admin');
        Route::get('/exchange_log',[ExchangeLog::class,"index"])->middleware('admin');
        Route::get('/exchange_log/{page?}/{limit?}',[ExchangeLog::class,"data"])->middleware('admin');
        Route::get('/user/add',[AddUser::class,"index"])->middleware('admin');
        Route::post('/user/add/submit',[AddUser::class,"submit"])->middleware('admin');
    });
    Route::group(['prefix'=>'vegetable'],function(){
        Route::get('/',[Vegetable::class,"index"])->middleware('admin');
        Route::get('/data/{page?}/{limit?}',[Vegetable::class,"data"])->middleware('admin');
        Route::post('/',[VegetableAdd::class,"submit"])->middleware('admin');
        Route::delete('/{id}',[DelVegetable::class,"index"])->middleware('admin');
        Route::get('/edit/{id}',[EditVegetable::class,"index"])->middleware('admin');
        Route::post('/edit',[EditVegetable::class,"submit"])->middleware('admin');
        Route::get('/add/',[VegetableAdd::class,"index"])->middleware('admin');
        Route::post('/add/upload',[VegetableAdd::class,"upload"])->middleware('admin');
    });
    Route::group(['prefix'=>'system'],function(){
        Route::get('/notice',[Notice::class,"index"])->middleware('admin');
        Route::get('/notice/add',[NoticeAdd::class,"index"])->middleware('admin');
        Route::post('/notice/add',[NoticeAdd::class,"submit"])->middleware('admin');
        Route::get('/notice/data/{page?}/{limit?}',[Notice::class,"data"])->middleware('admin');
    });
    Route::group(['prefix'=>'logistics'],function(){
        Route::get('/order',[Order::class,"index"])->middleware('admin');
        Route::get('/order/data/{page?}/{limit?}',[Order::class,"data"])->middleware('admin');
        Route::get('/distribution',[Distribution::class,"index"])->middleware('admin');
        Route::put('/distribution',[EditDistribution::class,"index"])->middleware('admin');
        Route::get('/distribution/data/{page?}/{limit?}',[Distribution::class,"data"])->middleware('admin');
    });


});
//Route::any("/redis",[\App\Http\Controllers\IndexController::class,"index"]);
