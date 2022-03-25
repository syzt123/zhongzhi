<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //返回json格式
    public function backjson($msg, $code = 200, $data = []): string
    {
        $arr = [
            'msg' => $msg,
            'code' => $code,
            'data' => $data,
        ];
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    //返回数组
    public function backArr($msg, $code = 200, $data = []): array
    {
        return [
            'msg' => $msg,
            'code' => $code,
            'data' => $data,
        ];
    }

    //手机号验证
    public function checkPhone($phone = ''): bool
    {
        $phone = preg_match_all("/^1[123456789]\d{9}$/", $phone);
        return $phone;
    }

    //获取用户信息
    public function getUserInfo($token = ''): array
    {
        //读取缓存
        $jsonData = json_decode(Redis::get(config("comm_code.redis_prefix.token") . $token), true);
        if (isset($jsonData["password"])) {
            unset($jsonData["password"]);
        }
        return $jsonData ?? [];
    }


    //生成token规则
    public function createTokenRules($string = ''): string
    {
        $key = config("comm_code.redis_prefix.token") . md5($string);
        $days = 30 * 24 * 60 * 60;
        $rs = Redis::setex($key, $days, $string);
        if ($rs) {
            return md5($string);
        }
        return '';
    }
}
