<?php

namespace App\Http\Controllers\Api\V1;

use \App\Http\Controllers\Controller;
use App\Http\Services\MemberInfoService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    //注册
    function registerUser(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', -1, []);
        }
        //校验
        if (!isset($request->tel)) {
            return $this->backArr('手机号必须', -1, []);
        }
        if (!$this->checkPhone($request->tel)) {
            return $this->backArr('手机号错误', -1, []);
        }
        //手机号是否被注册 todo
        /*if (!$this->checkPhone($request->tel)) {
            return $this->backArr('手机号错误', -1, []);
        }*/
        if (!isset($request->nickname)) {
            return $this->backArr('用户昵称必须', -1, []);
        }

        // 通过后进行注册
        $time = time();
        $data = [
            "status" => 1,
            "v_address" => "四川成都市",
            "tel" => $request->tel,
            "vegetable_num" => 0,
            "nickname" => $request->nickname,
            "gold" => 0,
            "password" => md5("12345678"),
            "create_time" => $time,
            "update_time" => $time,

        ];
        $bool = MemberInfoService::registerUser($data);
        if ($bool) {
            return $this->backArr('注册成功', 200, []);
        }
        return $this->backArr('注册失败', -1, []);
    }

    // 登录
    function loginUser(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', -1, []);
        }
        //校验
        if (!isset($request->tel)) {
            return $this->backArr('手机号必须', -1, []);
        }
        if (!$this->checkPhone($request->tel)) {
            return $this->backArr('手机号错误', -1, []);
        }
        //手机号是否被注册 todo
        /*if (!$this->checkPhone($request->tel)) {
            return $this->backArr('手机号错误', -1, []);
        }*/
        if (!isset($request->password)) {
            return $this->backArr('密码必须', -1, []);
        }
        $data = [
            "password" => md5($request->password),
            "tel" => $request->tel,
        ];
        $rs = MemberInfoService::LoginUser($data);
        if (count($rs)) {
            return $this->backArr('用户不存在或密码错误', -1, []);
        }
        return $this->backArr('ok', 200, $rs);
    }
}
