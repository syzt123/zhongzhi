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

    //请求方式
    static function methodCurl($url = '', $method = 'post', $contentType = 1, $data = []): array
    {
        $data = json_encode($data);
        if ($contentType == 1) {
            $headerArray = array("Content-Type:application/x-www-form-urlencoded");
        } else {
            $headerArray = array("Content-type:application/json", "charset='utf-8'");
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($method == 'PUT') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT"); //设置请求方式
        }
        if ($method == 'PATCH') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
        }
        if ($method == 'DELETE') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HEADER, 0);//不抓取头部信息。只返回数据
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output, true);
    }
}
