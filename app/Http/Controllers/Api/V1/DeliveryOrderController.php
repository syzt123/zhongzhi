<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Ys\YsController;
use \App\Http\Controllers\Controller;
use App\Http\Services\BuyLogService;
use App\Http\Services\DeliveryOrderService;
use App\Http\Services\MemberInfoService;
use App\Http\Services\MemberVegetableService;
use App\Http\Services\NoticeService;
use App\Http\Services\PaymentOrderService;
use App\Http\Services\VegetableLandService;
use App\Http\Services\VegetableTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class PaymentOrderController
 * @package App\Http\Controllers\Api\V1
 */
//新增物流配送
class DeliveryOrderController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/v1/user/addDelivery",
     *     tags={"蔬菜兑换/物流订单管理",},
     *     summary="新增物流订单",
     *     description="新增物流订单(2022/03/31已完成)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="m_v_id", in="query", @OA\Schema(type="int"),description="要兑换的蔬菜id"),
     *     @OA\Parameter(name="f_price", in="query", @OA\Schema(type="string"),description="蔬菜兑换时的蔬菜币"),
     *     @OA\Parameter(name="des_address", in="query", @OA\Schema(type="string"),description="用户收货地址"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *     ),
     *    )
     */
    function addDeliveryOrder(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }
        $userInfo = $this->getUserInfo($request->header("token"));

        if (!isset($request->m_v_id)) {//
            return $this->backArr('用户想兑换蔬菜m_v_id必须', config("comm_code.code.fail"), []);
        }
        //校验m_v_id是否存在
        $mVData = [
            "id" => $request->m_v_id,
        ];

        $mVInfo = MemberVegetableService::getMemberVegetableList($userInfo["id"], $mVData);
        if (!count($mVInfo["list"])) {
            return $this->backArr('用户想兑换蔬不存在，请重试！', config("comm_code.code.fail"), []);
        }

        if (count($mVInfo["list"]) == 1 && $mVInfo["list"][0]["v_status"] != 2) {
            return $this->backArr('该蔬菜还未成熟，暂时不能兑换！', config("comm_code.code.fail"), []);
        }

        if (!isset($request->f_price)) {
            return $this->backArr('兑换的单价f_price必须', config("comm_code.code.fail"), []);
        }
        if ((int)$request->f_price < 0) {
            return $this->backArr('兑换的单价f_price必须为正数', config("comm_code.code.fail"), []);
        }
        if (!isset($request->des_address)) {
            return $this->backArr('收货地址必须', config("comm_code.code.fail"), []);
        }
        if ($request->des_address == '') {
            return $this->backArr('收货地址不能为空', config("comm_code.code.fail"), []);
        }
        $time = time();

        // 新增物流表
        $data = [
            "m_id" => $userInfo["id"],
            "r_id" => 1,//1 微信支付 2 支付宝 3其他
            "f_price" => $request->f_price,//兑换的金额
            "m_v_id" => (int)$request->m_v_id,
            "order_id" => $this->getUniqueOrderNums(),//
            "create_time" => $time,
            "update_time" => $time,
            "status" => 1,//默认待配送
            "des_address" => $request->des_address,
        ];
        $bool = DeliveryOrderService::addDeliveryOrder($data);

        if ($bool) {
            return $this->backArr('新增物流订单成功', config("comm_code.code.ok"), []);
        }
        return $this->backArr('新增订单失败', config("comm_code.code.fail"), []);

    }

    /**
     * 根据订单号更新订单状态为已完成
     * @OA\Post (
     *     path="/api/v1/user/updateDeliveryComplete",
     *     tags={"蔬菜兑换/物流订单管理",},
     *     summary="根据订单号更新订单状态为已完成",
     *     description="根据订单号更新订单状态为已完成(2022/03/31已完成)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="order_id", in="query", @OA\Schema(type="string"),description="物流详情的订单id必须"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *
     *     ),
     *    )
     */
    function updateDeliveryComplete(Request $request): array
    {
        if (!isset($request->order_id)) {//
            return $this->backArr('物流详情的订单id必须', config("comm_code.code.fail"), []);
        }
        $userInfo = $this->getUserInfo($request->header("token"));
        // 查询id是否存在
        $lists = DeliveryOrderService::getDeliveryOrderList($userInfo["id"], ["order_id" => $request->order_id]);
        if (count($lists["list"]) == 0) {
            return $this->backArr('该订单号不存在，请重试！', config("comm_code.code.fail"), []);
        }
        $info = $lists["list"][0];
        if (isset($info["status"]) && $info["status"] > 2) {
            return $this->backArr('该物流订单号已完成,勿在完成订单！', config("comm_code.code.fail"), []);
        }
        if (isset($info["status"]) && $info["status"] != 2) {
            return $this->backArr('该物流订单号尚未处于配送中，不能进行完成订单！', config("comm_code.code.fail"), []);
        }

        try {
            DB::beginTransaction();
            // 1.订单需要为2时才能进行完成
            // 2.更新用户蔬菜状态为4 已收货到家
            DeliveryOrderService::updateDeliveryOrder($info["id"], ["status" => 3]);//配送完成
            $mVBool = MemberVegetableService::updateMemberVegetable($info["m_v_id"], ["v_status" => 4]);// 表示该蔬菜已被用户收货
            MemberInfoService::decreaseVegetableNums($userInfo["id"]);
            DB::commit();
            if ($mVBool) {
                return $this->backArr('已完成该订单，感谢您的使用！', config("comm_code.code.ok"), []);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->backArr('该订单完成失败原因：' . $e->getMessage(), config("comm_code.code.fail"), []);
        }

    }

    /**
     * 根据订单号查看物流详情
     * @OA\Post (
     *     path="/api/v1/user/getDetailDeliveryByOrderId/{order_id}",
     *     tags={"蔬菜兑换/物流订单管理",},
     *     summary="根据订单号查看物流详情",
     *     description="根据订单号查看物流详情(2022/03/31已完成)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="order_id", in="query", @OA\Schema(type="string"),description="物流详情的订单id必须"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *                @OA\Property(property="id", type="integer", description="物流自增Id"),
     *                @OA\Property(property="order_id", type="string", description="物流订单号"),
     *                @OA\Property(property="des_address", type="string", description="用户物流收货地址"),
     *                @OA\Property(property="create_time", type="string", description="创建时间"),
     *                @OA\Property(property="update_time", type="string", description="更新时间"),
     *                @OA\Property(property="member_vegetable", type="array", description="用户蔬菜信息",
     *                   @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_monitor", type="string", description="用户蔬菜在土地的直播地址"),
     *                      @OA\Property(property="name", type="string", description="蔬菜名"),
     *                      @OA\Property(property="planting_time", type="string", description="种植时间"),
     *                      @OA\Property(property="v_status", type="string", description="蔬菜状态 1：生长中 2：仓库中 3：已坏掉 4:已完成送货"),
     *                   ),
     *                ),
     *             ),
     *          ),
     *       ),
     *    )
     */
    function getDetailDeliveryByOrderId(Request $request)
    {
        if (!isset($request->order_id)) {//
            return $this->backArr('物流详情的订单id必须', config("comm_code.code.fail"), []);
        }
        $userInfo = $this->getUserInfo($request->header("token"));

        // 查询id是否存在
        $lists = DeliveryOrderService::getDeliveryOrderList($userInfo["id"], ["order_id" => $request->order_id]);
        if (count($lists["list"]) == 0) {
            return $this->backArr('该订单号不存在，请重试！', config("comm_code.code.fail"), []);
        }
        $info = $lists["list"][0];
        if (isset($info["member_vegetable"]["vegetable_type"])) {
            $info["member_vegetable"]["name"] = $info["member_vegetable"]["vegetable_type"]["v_type"] ?? '';
            unset($info["member_vegetable"]["vegetable_type"]);
        } else {
            $info["member_vegetable"]["name"] = '';
        }

        return $this->backArr('物流详情ok', config("comm_code.code.ok"), $info);
    }

    /**
     * 根据状态等筛选不同的物流列表
     * @OA\Post (
     *     path="/api/v1/user/getDeliveryList",
     *     tags={"蔬菜兑换/物流订单管理",},
     *     summary="根据状态等筛选不同的物流列表",
     *     description="根据状态等筛选不同的物流列表(2022/03/31已完成)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="int"),description="物流状态值 1 待配送 2 配送中 3 已完成 非必须"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                @OA\Property(property="page", type="array", description="分页信息",
     *                  @OA\Items(
     *                      @OA\Property(property="page", type="int", description="当前页"),
     *                      @OA\Property(property="page_size", type="int", description="每页大小"),
     *                      @OA\Property(property="count", type="int", description="总条数"),
     *                      @OA\Property(property="total_page", type="int", description="总页数"),
     *                  ),
     *               ),
     *
     *                @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                        @OA\Property(property="id", type="integer", description="物流自增Id"),
     *                @OA\Property(property="order_id", type="string", description="物流订单号"),
     *                @OA\Property(property="des_address", type="string", description="用户物流收货地址"),
     *                @OA\Property(property="create_time", type="string", description="创建时间"),
     *                @OA\Property(property="update_time", type="string", description="更新时间"),
     *                @OA\Property(property="member_vegetable", type="array", description="用户蔬菜信息",
     *                   @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_monitor", type="string", description="用户蔬菜在土地的直播地址"),
     *                      @OA\Property(property="name", type="string", description="蔬菜名"),
     *                      @OA\Property(property="planting_time", type="string", description="种植时间"),
     *                      @OA\Property(property="v_status", type="string", description="蔬菜状态 1：生长中 2：仓库中 3：已坏掉 4:已完成送货"),
     *                     ),
     *                   ),
     *                 ),
     *               ),
     *            ),
     *         ),
     *     ),
     * )
     */
    function getDeliveryList(Request $request): array
    {
        $userInfo = $this->getUserInfo($request->header("token"));
        $data = [];
        if (isset($request->status) && in_array($request->status, [1, 2, 3])) {
            $data["status"] = $request->status;
        }
        $lists = DeliveryOrderService::getDeliveryOrderList($userInfo["id"], $data);
        return $this->backArr('物流列表ok', config("comm_code.code.ok"), $lists);
    }
}

