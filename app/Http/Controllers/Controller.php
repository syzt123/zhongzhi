<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //返回json格式
    public function backjson($msg, $code = 201, $data = [])
    {
        $arr = [
            'msg' => $msg,
            'code' => $code,
            'data' => $data,
        ];
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    //返回数组
    public function backArr($msg, $code = 201, $data = [])
    {
        $arr = [
            'msg' => $msg,
            'code' => $code,
            'data' => $data,
        ];
        return $arr;
    }

    //手机号验证
    public function checkPhone($phone = '')
    {
        $phone = preg_match_all("/^1[123456789]\d{9}$/", $phone);
        return $phone;
    }
}
