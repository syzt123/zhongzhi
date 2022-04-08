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
            "v_address" => "",
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
     *     @OA\Response(response=200, description="  {code: 200, msg:string, data:[]}  ",
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(property="token", type="string", description="token值，根据该值来获取数据。需要放在header头中"),
     *             ),
     *          ),
     *       ),
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
     *     @OA\Response(response=200, description="  {code: 200, msg:string, data:[]}  ",
     *       @OA\MediaType(
     *        mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(property="id", type="int", description="自增id"),
     *                @OA\Property(property="tel", type="string", description="用户手机号"),
     *                @OA\Property(property="nickname", type="string", description="用户昵称"),
     *                @OA\Property(property="gold", type="int", description="用户金币数"),
     *                @OA\Property(property="user_head_img", type="string", description="用户头像url"),
     *                @OA\Property(property="v_address", type="string", description="用户收货地址"),
     *                @OA\Property(property="vegetable_num", type="string", description="用户蔬菜总数"),
     *             ),
     *          ),
     *       ),
     *    ),
     * )
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


    /**
     * @OA\Post (
     *     path="/api/v1/user/updateUserInfo",
     *     tags={"用户管理"},
     *     summary="更新用户信息",
     *     description="更新用户信息头像或用户地址/用户昵称(2022/03/30日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Parameter (name="head_img", in="query", @OA\Schema (type="string"),description="用户头像地址 *非必须字段"),
     *     @OA\Parameter (name="user_address", in="query", @OA\Schema (type="string"),description="用户收获地址 *非必须字段"),
     *     @OA\Parameter (name="nickname", in="query", @OA\Schema (type="string"),description="用户昵称 *非必须字段"),
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
            // 更新用户收货地址
            $data["user_address"] = trim($request->user_address);
        }
        if (isset($request->nickname) && trim($request->nickname) != '') {
            // 更新用户昵称
            $data["nickname"] = trim($request->nickname);
        }
        try {
            $bool = MemberInfoService::updateUserInfo($userInfo["id"], $data);
            if ($bool) {
                return $this->backArr('更新成功', config("comm_code.code.ok"), []);
            }
        } catch (\Exception $e) {
            return $this->backArr('更新失败,原因：' . $e->getMessage(), config("comm_code.code.fail"), []);

        }
    }

    /**
     * @OA\Post (
     *     path="/api/v1/user/userVegetableList",
     *     tags={"用户管理"},
     *     summary="用户个人蔬菜列表",
     *     description="用户个人领取种植/仓库中/已收货蔬菜列表/(2022/04/05日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="int"),description="当前页 默认1"),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="int"),description="每页显示条数 默认10"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="int"),description="1：生长中 2.仓库中 3已坏掉 4:已完成送货 默认全部数据"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                @OA\Property(property="page", type="array", description="分页信息",
     *                   @OA\Items(
     *                      @OA\Property(property="page", type="int", description="当前页"),
     *                      @OA\Property(property="page_size", type="int", description="每页大小"),
     *                      @OA\Property(property="count", type="int", description="总条数"),
     *                      @OA\Property(property="total_page", type="int", description="总页数"),
     *                  ),
     *               ),
     *
     *               @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_monitor", type="string", description="监控地址"),
     *                      @OA\Property(property="v_price", type="int", description="蔬菜认领的价格"),
     *                      @OA\Property(property="f_price", type="int", description="蔬菜币"),
     *                      @OA\Property(property="yield", type="int", description="产量"),
     *                      @OA\Property(property="vegetable_grow", type="int", description="生长状况"),
     *                      @OA\Property(property="planting_time", type="int", description="用户领取种子的时间"),
     *                      @OA\Property(property="v_status", type="int", description="1：生长中\r\n2：仓库中\r\n3：已坏掉 4:已完成送货"),
     *                      @OA\Property(property="grow_5", type="int", description="成熟"),
     *                      @OA\Property(property="storage_time", type="int", description="可以存放的时间"),
     *                      @OA\Property(property="create_time", type="int", description="创建时间"),
     *                      @OA\Property(property="update_time", type="int", description="更新时间"),
     *                  ),
     *               ),
     *            ),
     *
     *         ),
     *     ),
     * )
     */
    function userVegetableList(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }
        $userInfo = $this->getUserInfo($request->header("token"));
        $data = [];
        if (isset($request->page) && (int)$request->page > 0) {
            $data["page"] = $request->page;
        }
        if (isset($request->page_size) && (int)$request->page_size > 0) {
            $data["page_size"] = $request->page_size;
        }
        if (isset($request->status) && (int)$request->status > 0) {
            $data["v_status"] = $request->status;
        }
        $lists = MemberVegetableService::getMemberVegetableList($userInfo["id"], $data);
        return $this->backArr('获取列表成功', config("comm_code.code.ok"), $lists);

    }

    /**
     * @OA\Get (
     *     path="/api/v1/user/userDetailVegetable",
     *     tags={"用户管理"},
     *     summary="用户个人蔬菜详情",
     *     description="用户个人蔬菜详情(2022/04/05日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Parameter(name="id", in="query", @OA\Schema(type="int"),description="主键id"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                @OA\Property(property="page", type="array", description="分页信息",
     *                   @OA\Items(
     *                      @OA\Property(property="page", type="int", description="当前页"),
     *                      @OA\Property(property="page_size", type="int", description="每页大小"),
     *                      @OA\Property(property="count", type="int", description="总条数"),
     *                      @OA\Property(property="total_page", type="int", description="总页数"),
     *                  ),
     *               ),
     *
     *               @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_type", type="string", description="蔬菜的名字"),
     *                      @OA\Property(property="status", type="int", description="蔬菜种类的状态1可种植 2不可种植"),
     *                      @OA\Property(property="v_price", type="int", description="蔬菜认领的价格"),
     *                      @OA\Property(property="f_price", type="int", description="蔬菜币"),
     *                      @OA\Property(property="grow_1", type="int", description="种子时期"),
     *                      @OA\Property(property="grow_2", type="int", description="幼苗时期"),
     *                      @OA\Property(property="grow_3", type="int", description="生长"),
     *                      @OA\Property(property="grow_4", type="int", description="成年"),
     *                      @OA\Property(property="grow_5", type="int", description="成熟"),
     *                      @OA\Property(property="storage_time", type="int", description="可以存放的时间"),
     *                      @OA\Property(property="create_time", type="int", description="创建时间"),
     *                      @OA\Property(property="update_time", type="int", description="更新时间"),
     *                  ),
     *               ),
     *            ),
     *
     *         ),
     *     ),
     * )
     */
    function userDetailVegetable(Request $request)
    {
        $userInfo = $this->getUserInfo($request->header("token"));
        $data = [];
        if (!isset($request->id)) {
            return $this->backArr('id必须', config("comm_code.code.fail"), []);
        }
        if ($request->id <= 0) {
            return $this->backArr('id必须大于0', config("comm_code.code.fail"), []);
        } else {
            $data["id"] = $request->id;
        }
        $lists = MemberVegetableService::getMemberVegetableList($userInfo["id"], $data);
        if (count($lists["list"]) <= 0) {
            return $this->backArr('详情不存在，请重试！', config("comm_code.code.fail"), []);
        }
        return $this->backArr('获取详情成功', config("comm_code.code.ok"), $lists["list"][0]);
    }
}
