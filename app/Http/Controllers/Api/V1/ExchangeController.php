<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Services\MemberVegetableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ExchangeController extends Controller
{
    public function index(Request $request)
    {
        $user = Redis::get(config("comm_code.redis_prefix.token") . $request->header('token'));
        if (!$user) {
            return $this->error('未找到用户信息');
        }
        $memberVegetables = MemberVegetableService::getMemberVegetables($user->id)->groupBy('v_status');
        return $this->success(compact('user','memberVegetables'));
    }
}
