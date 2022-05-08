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
use App\Models\VegetableResources;
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
     *     @OA\Parameter(name="m_v_ids", in="query", @OA\Schema(type="string"),description="要兑换的蔬菜ids ,id为用户蔬菜的主键id",example={"[{id:1,nums:10},{id2:2,nums:10}]"}),
     *     @OA\Parameter(name="f_price", in="query", @OA\Schema(type="string"),description="蔬菜兑换时的蔬菜币 默认0"),
     *     @OA\Parameter(name="u_name", in="query", @OA\Schema(type="string"),description="用户收货人名字 默认读用户收货人名称 不必传"),
     *     @OA\Parameter(name="u_tel", in="query", @OA\Schema(type="string"),description="用户收货人电话 默认读用户收货人电话 不必传"),
     *     @OA\Parameter(name="des_address", in="query", @OA\Schema(type="string"),description="用户收货地址 默认读用户收货地址 不必传"),
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
        //不读缓存数据
        $userInfo = MemberInfoService::getUserInfo($userInfo["id"]);

        if (!isset($userInfo["v_name"])) {
            return $this->backArr('收货人名称必须', config("comm_code.code.fail"), []);
        }
        if ($userInfo["v_name"] == '') {
            return $this->backArr('收货人名称必须为空', config("comm_code.code.fail"), []);
        }
        if (!isset($userInfo["v_tel"])) {
            return $this->backArr('收货人电话必须', config("comm_code.code.fail"), []);
        }
        if ($userInfo["v_tel"] == '') {
            return $this->backArr('收货人电话不能为空或格式错误', config("comm_code.code.fail"), []);
        }

        if (!isset($request->m_v_ids)) {//
            return $this->backArr('用户想兑换蔬菜m_v_ids必须', config("comm_code.code.fail"), []);
        }
        $rqIdsArr = json_decode($request->m_v_ids);
        if (!is_array($rqIdsArr)) {//[{"id":1,nums:10},{"id":2,nums:10}]
            return $this->backArr('m_v_ids必须是数组格式', config("comm_code.code.fail"), []);
        }
        if (!count($rqIdsArr)) {//
            return $this->backArr('m_v_ids必须长度大于0', config("comm_code.code.fail"), []);
        }
        //校验m_v_ids是否存在

        $totalPrice = 0;
        DB::beginTransaction();
        $name = '';//蔬菜名
        $weight = 0;
        $allExchangeGold = 0;//总蔬菜对货币
        $vegetableImg = '';
        try {
            foreach ($rqIdsArr as $v) {
                if (!isset($v->id)) {
                    return $this->backArr('每个蔬菜id必须存在', config("comm_code.code.fail"), []);
                }
                if ($v->id <= 0) {
                    return $this->backArr('每个蔬菜id必须大于0', config("comm_code.code.fail"), []);
                }
                if (!isset($v->nums)) {
                    return $this->backArr('每个蔬菜数量必须存在且大于o', config("comm_code.code.fail"), []);
                }
                if ($v->nums <= 0) {
                    return $this->backArr('每个蔬菜数量必须大于0', config("comm_code.code.fail"), []);
                }
                if (isset($v->id)) {
                    $mVData = [
                        "id" => $v->id,
                    ];
                    $selfVegetable = $mVInfo = MemberVegetableService::getMemberVegetableList($userInfo["id"], $mVData);//用户自己的蔬菜
                    $platformVegetable = [];//平台蔬菜列表
                    if (!count($mVInfo["list"])) {
                        $platformVegetable = $mVInfo = MemberVegetableService::getMemberVegetableList(null, $mVData);//平台蔬菜
                    }
                    if (!count($mVInfo["list"])) {
                        return $this->backArr('用户想兑换蔬不存在，请重试！', config("comm_code.code.fail"), []);
                    }

                    if (count($mVInfo["list"]) == 1 && $mVInfo["list"][0]["v_status"] != 2) {
                        return $this->backArr('存在蔬菜还未成熟/已坏，暂时不能兑换！', config("comm_code.code.fail"), []);
                    }
                    if ($v->nums > $mVInfo["list"][0]["nums"]) {
                        return $this->backArr('能兑换的蔬菜数不能大于总库存/购买的蔬菜数量，请重试！', config("comm_code.code.fail"), []);
                    }

                    $totalPrice += $mVInfo["list"][0]["v_price"] * $v->nums;
                    $useGold = ($mVInfo["list"][0]["yield"] / $mVInfo["list"][0]["nums"]) * $v->nums;
                    // 同时更新用户蔬菜数量 产量
                    if (count($platformVegetable) == 0) {
                        MemberVegetableService::updateNumsMemberVegetable($mVInfo["list"][0]["id"], $userInfo["id"], $v->nums, $useGold);
                    }else{
                        // 平台蔬菜
                        MemberVegetableService::updateNumsMemberVegetable($mVInfo["list"][0]["id"], null, $v->nums, $useGold);
                    }


                    $name .= $mVInfo["list"][0]["v_name"] . '*' . (string)$v->nums . '_';
                    $weight += (($mVInfo["list"][0]["yield"] / $mVInfo["list"][0]["nums"]) * $v->nums);

                    // 获取蔬菜信息 拿到兑换比例
                    $vegetableInfo = VegetableTypeService::findVegetableTypeInfoById($mVInfo["list"][0]["v_type"]);

                    // 获取需要兑换的蔬菜币 数量*单价
                    //$allExchangeGold += (($mVInfo["list"][0]["yield"] / $mVInfo["list"][0]["nums"]) * $v->nums) / $vegetableInfo["exchange_quality"];
                    $allExchangeGold += $mVInfo["list"][0]["f_price"] * $v->nums;// 新算法逻辑
                    // 获取蔬菜图片地址
                    $tmpImg = VegetableResources::with([])->where("vegetable_type_id", '=', $vegetableInfo["id"])->where("vegetable_type_id", '=', 3)->first();
                    if ($tmpImg) {
                        $vegetableImg = $tmpImg->toArray()["vegetable_resources"];
                    }
                }

            }

            if ($allExchangeGold > $userInfo["gold"]) {
                return $this->backArr('您的蔬菜币不足以兑换当前所选蔬菜，请重试！', config("comm_code.code.fail"), []);
            }

            if (!isset($userInfo["v_address"])) {
                return $this->backArr('收货地址必须', config("comm_code.code.fail"), []);
            }
            if ($userInfo["v_address"] == '') {
                return $this->backArr('收货地址不能为空', config("comm_code.code.fail"), []);
            }


            $time = time();

            // 更新用户蔬菜币
            MemberInfoService::decreaseUserGoldNums($userInfo["id"], $allExchangeGold);
            // 更新缓存
            $this->updateUserCache($request->header("token"));
            // 新增物流表
            $data = [
                "m_id" => $userInfo["id"],
                "r_id" => 1,//1 微信支付 2 支付宝 3其他
                "f_price" => $allExchangeGold ?? 0,//兑换的蔬菜币
                "m_v_ids" => $request->m_v_ids ?? '[]',
                "order_id" => $this->getUniqueOrderNums(),//
                "create_time" => $time,
                "update_time" => $time,
                "status" => 1,//默认待配送
                "u_tel" => $userInfo["v_tel"],
                "u_name" => $userInfo["v_name"],
                "des_address" => $userInfo["v_address"],
                "total_price" => $totalPrice ?? 0,
                "v_img" => $vegetableImg,
                "v_name" => substr($name, 0, -1),
                "weight" => number_format($weight / 1000, 2) ?? 1,
            ];
            $bool = DeliveryOrderService::addDeliveryOrder($data);

            DB::commit();
            if ($bool) {
                return $this->backArr('新增物流订单成功', config("comm_code.code.ok"), []);
            }
            return $this->backArr('新增物流订单失败', config("comm_code.code.fail"), []);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->backArr('新增物流订单失败2' . $e->getMessage(), config("comm_code.code.fail"), []);
        }


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
            $mVBool = DeliveryOrderService::updateDeliveryOrder($info["id"], ["status" => 3]);//配送完成
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
     *                      @OA\Property(property="v_status", type="string", description="蔬菜状态 1：生长中 2：仓库中 3：已坏掉"),
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
     *                      @OA\Property(property="v_status", type="string", description="蔬菜状态 1：生长中 2：仓库中 3：已坏掉"),
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

