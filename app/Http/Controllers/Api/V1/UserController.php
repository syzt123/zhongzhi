<?php

namespace App\Http\Controllers\Api\V1;

use \App\Http\Controllers\Controller;
use App\Http\Services\BuyLogService;
use App\Http\Services\DeliveryOrderService;
use App\Http\Services\ExchangeLogService;
use App\Http\Services\MemberInfoService;
use App\Http\Services\MemberVegetableService;
use App\Http\Services\PaymentOrderService;
use Illuminate\Http\Request;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class UserController
 * @package App\Http\Controllers\Api\V1
 */
/**
 * @OA\Info (title="接口文档",version="V1",description="指尖种菜接口文档")
 */
//用户管理
class UserController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/v1/user/register",
     *     tags={"用户管理",},
     *     summary="用户注册",
     *     description="用户注册(2022/03/22日完)",
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
     *     description="用户登陆(2022/03/22日完)",
     *     @OA\Parameter(name="tel", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password", in="query", @OA\Schema(type="string"),description="密码 默认为：12345678" ),
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
            $token = $this->createTokenRules($rsJson);
        } catch (InvalidArgumentException $e) {
            return $this->backArr('保存缓存失败，原因：' . $e->getMessage(), $e->getCode(), []);
        }
        return $this->backArr('ok', config("comm_code.code.ok"), ["token" => $token]);
    }

    /**
     * @OA\Post (
     *     path="/api/v1/user/center",
     *     tags={"用户管理"},
     *     summary="用户个人中心",
     *     description="用户个人中心(2022/03/24日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Parameter (name="page", in="query", @OA\Schema (type="int"),description="第几页 默认第一页"),
     *     @OA\Parameter (name="page_size", in="query", @OA\Schema (type="int"),description="每页多少数据 默认10"),
     *     @OA\Response(response=200, description="  {code: 200, msg:string, data:[]}  "),
     *    )
     * @param Request $request
     * @return array
     */
    function center(Request $request): array
    {
        $rs = $this->getUserInfo($request->header("token"));
        if (empty($rs)) {
            return $this->backArr('fail', config("comm_code.code.fail"), []);
        }
        // 用户id
        $userId = $rs["id"];
        // 头像
        $headInfo = MemberInfoService::getUserImgInfo($userId);
        $rs["user_head_img"] = isset($headInfo["head"]) ? $headInfo["head"] : '';
        // 购买记录
        $page = isset($request->page) ? (int)$request->page : 1;
        $pageSize = isset($request->page_size) ? (int)$request->page_size : 10;
        $rs["buy_log"] = BuyLogService::getUserBuyLog($rs["id"], [
            "page" => $page,
            "page_size" => $pageSize,
        ]);
        // 支付订单记录
        $rs["payment_order"] = PaymentOrderService::getPaymentOrderList($userId, [
            "page" => $page,
            "page_size" => $pageSize,
        ]);
        // 兑换记录
        $rs["exchange_log"] = ExchangeLogService::getExchangeLogList($userId, [
            "page" => $page,
            "page_size" => $pageSize,
        ]);
        //物流订单
        $rs["delivery_order"] = DeliveryOrderService::getDeliveryOrderList($userId, [
            "page" => $page,
            "page_size" => $pageSize,
        ]);
        //用户蔬菜
        $rs["vegetable_info"] = MemberVegetableService::getMemberVegetableList($userId, [
            "page" => $page,
            "page_size" => $pageSize,
        ]);
        return $this->backArr('ok', config("comm_code.code.ok"), $rs);
    }

    // 更新用户信息 todo 头像或用户地址

    /**
     * @OA\Post (
     *     path="/api/v1/user/updateUserInfo",
     *     tags={"用户管理"},
     *     summary="更新用户信息",
     *     description="更新用户信息(2022/03/30日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Parameter (name="head_img", in="query", @OA\Schema (type="string"),description="用户头像地址 *非必须字段"),
     *     @OA\Parameter (name="user_address", in="query", @OA\Schema (type="string"),description="用户收获地址 *非必须字段"),
     *     @OA\Response(response=200, description="{code: 200, msg:string, data:[]}"),
     *    )
     * @param Request $request
     * @return array
     */
    function updateUserInfo(Request $request): array
    {
        $userInfo = $this->getUserInfo($request->header("token"));
        if (empty($userInfo)) {
            return $this->backArr('fail', config("comm_code.code.fail"), []);
        }
        $data = [];
        if (isset($request->head_img) && trim($request->head_img) != '') {
            // 更新用户头像
            $data["head_img"] = trim($request->head_img);
        }
        if (isset($request->user_address) && trim($request->user_address) != '') {
            // 更新用户头像
            $data["user_address"] = trim($request->user_address);
        }
        try {
            $bool = MemberInfoService::updateUserInfo($userInfo["id"], $data);
            if ($bool) {
                return $this->backArr('更新成功', config("comm_code.code.ok"), []);
            }
        }catch (\Exception $e){
            return $this->backArr('更新失败,原因：'.$e->getMessage(), config("comm_code.code.fail"), []);

        }
    }
}
