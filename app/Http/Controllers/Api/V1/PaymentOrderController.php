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
use App\Http\Services\PayMethod\ChargeContent;
use App\Http\Services\VegetableLandService;
use App\Http\Services\VegetableTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class PaymentOrderController
 * @package App\Http\Controllers\Api\V1
 */
//订单支付
class PaymentOrderController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/v1/user/addOrder",
     *     tags={"用户管理",},
     *     summary="用户领取种子新增订单",
     *     description="用户领取种子新增订单(2022/04/14已完成 支持多种蔬菜种子领取)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="v_ids", in="query", @OA\Schema(type="string"),description="蔬菜主键ids 如白菜",example={"[{id:1,nums:10},{id2:2,nums:10}]"}),
     *     @OA\Parameter(name="pay_type", in="query", @OA\Schema(type="string"),description="支付类型 ali：支付宝支付 h5_wechat:微信h5支付支付 js_wechat:（微信公众号支付 需要获取oendId） native_wechat：（Native支付是指商户系统按微信支付协议生成支付二维码，用户再用微信“扫一扫”完成支付的模式。该模式适用于PC网站、实体店单品或订单、媒体广告支付等场景）"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 @OA\Property(property="url", type="string", description="生成的付款地址 直接浏览器打开地址"),
     *            ),
     *          ),
     *      ),
     *    )
     */
    function addOrder(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }

        /*if (!isset($request->land_id)) {
            return $this->backArr('蔬菜所在的土地位置编号land_id必须', config("comm_code.code.fail"), []);
        }
        //校验land_id是否存在
        $landInfo = VegetableLandService::findVegetableLandInfoById($request->land_id);
        if (count($landInfo) == 0) {
            return $this->backArr('输入的土地id不存在，请重试！', config("comm_code.code.fail"), []);
        }*/
        if (!isset($request->v_ids)) {//[{"id":1,"nums":10}]
            return $this->backArr('蔬菜所属的种类v_ids必须', config("comm_code.code.fail"), []);
        }
        //校验v_ids是否存在

        $rqIdsArr = json_decode($request->v_ids);
        if (!is_array($rqIdsArr)) {//[{"id":1,nums:10},{"id":2,nums:10}]
            return $this->backArr('m_v_ids必须是数组格式', config("comm_code.code.fail"), []);
        }
        if (!count($rqIdsArr)) {//
            return $this->backArr('m_v_ids必须长度大于0', config("comm_code.code.fail"), []);
        }

        if (!isset($request->pay_type)) {
            return $this->backArr('请选择支付方式', config("comm_code.code.fail"), []);
        }
        if (!$this->isHasInPayType($request->pay_type)) {
            return $this->backArr('支付方式不存在', config("comm_code.code.fail"), []);
        }

        $totalPrice = 0;
        $userInfo = $this->getUserInfo($request->header("token"));
        $time = time();


        // 调用支付
        try {
            // 开启事务
            DB::beginTransaction();
            $createOrderId = $this->getUniqueOrderNums();
            // 新增订单
            $data = [
                "m_id" => $userInfo["id"],
                "r_id" => 1,//1 微信支付 2 支付宝 3其他 废弃
                "f_price" => $totalPrice,//兑换的金额
                "v_ids" => $request->v_ids ?? '[]',
                "status" => 1,
                "order_id" => $createOrderId,
                "wechat_no" => '',// 微信或者支付宝的订单号
                "pay_price" => 0,// 实际支付的价格
                "create_time" => $time,
                "update_time" => $time,
                "pay_type" => $request->pay_type,
            ];
            $orderId = PaymentOrderService::addPaymentOrder($data);
            // 新增领取的种子表 member_vegetable 多个种子
            $addVegetableData = [];
            $totalNums = 0;
            $nameStr = '';
            foreach ($rqIdsArr as $v) {
                if (isset($v->id) && $v->id > 0) {
                    $vegetableTypeData = VegetableTypeService::findVegetableTypeInfoById($v->id);
                    if (count($vegetableTypeData) == 0) {
                        return $this->backArr('输入的蔬菜有不存在，请重试！', config("comm_code.code.fail"), []);
                    }
                    if (isset($v->nums) && $v->nums <= 0) {
                        return $this->backArr('输入的蔬菜数量要必须大于0，请重试！', config("comm_code.code.fail"), []);
                    }
                    // 单个商品总价
                    $singleVegetablePrice = $v->nums * $vegetableTypeData["v_price"];
                    $totalPrice += $singleVegetablePrice;
                    $totalNums += $v->nums;
                    $nameStr .= $vegetableTypeData["v_type"] . "*" . (string)$v->nums . '_';
                    // 如果之前用户蔬菜表存在蔬菜种子或其他(未种植 只增加数量 即更新) todo 有问题，订单会被覆盖
                    /*$whereData = [
                        "m_id" => $userInfo["id"],
                        "v_type" => $vegetableTypeData["id"],
                        "v_status" => 0,
                        "vegetable_type_id" => count($vegetableTypeData["vegetable_kinds"]) > 0 ? $vegetableTypeData["vegetable_kinds"]["id"] : 1,
                    ];
                    $addRs = MemberVegetableService::addMemberVegetableNums($whereData, $v->nums);
                    if ($addRs) {
                        continue;// 跳过这次循环
                    }*/

                    // 否则新增数据
                    $addVegetableData[] = [
                        "m_id" => $userInfo["id"],
                        "v_price" => $vegetableTypeData["v_price"] ?? 0,
                        "f_price" => 0,
                        "pay_price" => $vegetableTypeData["v_price"] * $v->nums,
                        "v_type" => $vegetableTypeData["id"],
                        "nums" => $v->nums,
                        "planting_time" => $time,
                        "v_status" => 0,
                        "create_time" => $time,
                        "payment_order_id" => $orderId,
                        "v_name" => $vegetableTypeData["v_type"],//名字
                        "vegetable_type_id" => count($vegetableTypeData["vegetable_kinds"]) > 0 ? $vegetableTypeData["vegetable_kinds"]["id"] : 1,
                    ];
                }

            }

            MemberVegetableService::addMemberVegetable($addVegetableData);

            /*$deliveryData = [
                "m_id" => $userInfo["id"],
                "r_id" => 1,//1 微信支付 2 支付宝 3其他
                "f_price" => $request->f_price,//兑换的金额
                "status" => 1,
                "order_id" => $this->getUniqueOrderNums(),
                "payment_order_id" => $orderId,// 订单表id
                "create_time" => $time,
                "update_time" => $time,
            ];
            // 新增物流
            DeliveryOrderService::addDeliveryOrder($deliveryData);*/

            $buyData = [
                "m_id" => $userInfo["id"],
                "v_price" => 0,//兑换的金额
                "v_num" => $totalNums,
                "n_price" => $totalPrice,//总金额
                "payment_order_id" => $orderId,// 订单表id
                "create_time" => $time,
            ];
            // 购买记录
            $buyBool = BuyLogService::addUserBuyLog($buyData);

            // 用户蔬菜自增
            MemberInfoService::increaseVegetableNums($userInfo["id"]);
            // 调用支付
            // 设置订单号
            $request->out_trade_no = $createOrderId;
            $request->total_amount = $totalPrice;
            $request->subject = $nameStr . '种子';

            $payInstance = new ChargeContent();
            $payMethod = $request->pay_type ?? 'ali';

            $payInstance = $payInstance->initInstance($payMethod);
            $payUrl = $payInstance->handlePay($request);

            DB::commit();

            if ($buyBool) {
                return $this->backArr('新增订单成功,请前往付款！', config("comm_code.code.ok"), ["url" => $payUrl]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->backArr('新增订单失败,原因：' . $exception->getMessage(), config("comm_code.code.fail"), []);
        }
    }

    // 根据订单号更新订单状态等
    function updateOrderStatus(Request $request): array
    {

    }

}
