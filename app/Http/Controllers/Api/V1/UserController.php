<?php

namespace App\Http\Controllers\Api\V1;

use \App\Http\Controllers\Controller;
use App\Http\Services\MemberInfoService;
use Illuminate\Http\Request;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class UserController
 * @package App\Http\Controllers\Api\V1
 */
//用户管理
class UserController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/v1/user/register",
     *     tags={"用户管理",},
     *     summary="用户注册",
     *     description="用户注册",
     *     @OA\Parameter(name="tel", in="query", @OA\Schema(type="string"),description="手机号"),
     *     @OA\Parameter(name="nickname", in="query", @OA\Schema(type="string"),description="用户昵称"),
     *     @OA\Parameter(name="head_img", in="query", @OA\Schema(type="string"),description="用户头像"),
     *     @OA\Response(response=200, description="  {code: 200, msg:string, data:[]}  "),
     *    )
     */
    function registerUser(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }
        //校验
        if (!isset($request->tel)) {
            return $this->backArr('手机号必须', config("comm_code.code.fail"), []);
        }
        if (!$this->checkPhone($request->tel)) {
            return $this->backArr('手机号格式错误', config("comm_code.code.fail"), []);
        }
        //手机号是否被注册
        $rs = MemberInfoService::LoginUser(["tel" => $request->tel]);
        if (count($rs)) {
            return $this->backArr('手机号已被注册，请登录！', config("comm_code.code.fail"), []);
        }
        if (!isset($request->nickname)) {
            return $this->backArr('用户昵称必须', config("comm_code.code.fail"), []);
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
            return $this->backArr('注册成功', config("comm_code.code.ok"), []);
        }
        return $this->backArr('注册失败', config("comm_code.code.fail"), []);
    }

    /**
     * @OA\Post (
     *     path="/api/v1/user/login",
     *     tags={"用户管理"},
     *     summary="用户登陆",
     *     description="用户登陆",
     *     @OA\Parameter(name="tel", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="  {code: 200, msg:string, data:[]}  "),
     *    )
     */
    function loginUser(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr("请求方式必须为post", config("comm_code.code.fail"), []);
        }
        //校验
        if (!isset($request->tel)) {
            return $this->backArr('手机号必须', config("comm_code.code.fail"), []);
        }
        if (!$this->checkPhone($request->tel)) {
            return $this->backArr('手机号错误', config("comm_code.code.fail"), []);
        }

        if (!isset($request->password)) {
            return $this->backArr('密码必须', config("comm_code.code.fail"), []);
        }
        $data = [
            "password" => md5($request->password),
            "tel" => $request->tel,
        ];
        $rs = MemberInfoService::LoginUser($data);
        if (!count($rs)) {
            return $this->backArr('用户不存在或密码错误', config("comm_code.code.fail"), []);
        }
        $rsJson = json_encode($rs);
        // 保存缓存
        try {
            $rs["token"] = $this->createTokenRules($rsJson);
        } catch (InvalidArgumentException $e) {
            return $this->backArr('保存缓存失败，原因：' . $e->getMessage(), $e->getCode(), $rs);
        }
        return $this->backArr('ok', config("comm_code.code.ok"), $rs);
    }

    /**
     * @OA\Post (
     *     path="/api/v1/user/index",
     *     tags={"用户管理"},
     *     summary="用户个人中心",
     *     description="用户个人中心",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Response(response=200, description="  {code: 200, msg:string, data:[]}  "),
     *    )
     */
    function index(Request $request): array
    {
        $rs = $this->getUserInfo($request->header("token"));
        return $this->backArr('ok', config("comm_code.code.ok"), $rs);
    }
}
