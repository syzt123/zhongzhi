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
     *     description="用户领取种子新增订单(2022/03/29开发中)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="land_id", in="query", @OA\Schema(type="int"),description="土地位置编号land_id"),
     *     @OA\Parameter(name="v_type_id", in="query", @OA\Schema(type="int"),description="蔬菜种类id 如白菜种类"),
     *     @OA\Parameter(name="v_status", in="query", @OA\Schema(type="int"),description="蔬菜当前形态 1生长期 2 成熟仓库中"),
     *     @OA\Parameter(name="f_price", in="query", @OA\Schema(type="string"),description="蔬菜领取时的单价"),
     *     @OA\Parameter(name="f_num", in="query", @OA\Schema(type="string"),description="蔬菜领取时的数量"),
     *     @OA\Parameter(name="pay_type", in="query", @OA\Schema(type="string"),description="支付类型 ali：支付宝支付 wechat:微信支付 默认支付宝"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *     ),
     *    )
     */
    function addOrder(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }

        if (!isset($request->land_id)) {
            return $this->backArr('蔬菜所在的土地位置编号land_id必须', config("comm_code.code.fail"), []);
        }
        //校验land_id是否存在
        $landInfo = VegetableLandService::findVegetableLandInfoById($request->land_id);
        if (count($landInfo) == 0) {
            return $this->backArr('输入的土地id不存在，请重试！', config("comm_code.code.fail"), []);
        }
        if (!isset($request->v_type_id)) {
            return $this->backArr('蔬菜所属的种类v_type_id必须', config("comm_code.code.fail"), []);
        }
        //校验v_type_id是否存在
        $vegetableTypeData = VegetableTypeService::findVegetableTypeInfoById($request->v_type_id);
        if (count($vegetableTypeData) == 0) {
            return $this->backArr('输入的蔬菜种类v_type_id不存在，请重试！', config("comm_code.code.fail"), []);
        }

        if (!isset($request->v_status)) {
            return $this->backArr('蔬菜当前的环境必须v_type必须，如1生长中 2仓库中', config("comm_code.code.fail"), []);
        }
        //生长中 仓库中
        if (!in_array((int)$request->v_status, [1, 2])) {
            return $this->backArr('蔬菜当前的环境必须v_type必须为1或2', config("comm_code.code.fail"), []);
        }
        if (!isset($request->f_price)) {
            return $this->backArr('兑换的单价f_price必须', config("comm_code.code.fail"), []);
        }
        if ((int)$request->f_price < 0) {
            return $this->backArr('兑换的单价f_price必须为正数', config("comm_code.code.fail"), []);
        }
        if (!isset($request->f_num)) {
            return $this->backArr('兑换的数量f_num必须', config("comm_code.code.fail"), []);
        }
        if ((int)$request->f_num < 0) {
            return $this->backArr('兑换的单价f_num必须为正数', config("comm_code.code.fail"), []);
        }
        if (!isset($request->pay_type)) {
            return $this->backArr('请选择支付方式', config("comm_code.code.fail"), []);
        }
        if (!in_array($request->pay_type, ["ali", "wechat"])) {
            return $this->backArr('支付方式不存在', config("comm_code.code.fail"), []);
        }
        $time = time();
        $userInfo = $this->getUserInfo($request->header("token"));

        // todo 调用支付
        try {
            // 开启事务
            DB::beginTransaction();
            $createOrderId = $this->getUniqueOrderNums();
            // 新增订单
            $data = [
                "m_id" => $userInfo["id"],
                "r_id" => 1,//1 微信支付 2 支付宝 3其他
                "f_price" => $request->f_price,//兑换的金额
                "status" => 1,
                "order_id" => $createOrderId,
                "wechat_no" => '',// 微信或者支付宝的订单号
                "pay_price" => 0,// 实际支付的价格
                "create_time" => $time,
                "update_time" => $time,
            ];
            $orderId = PaymentOrderService::addPaymentOrder($data);
            // 新增领取的种子表 todo member_vegetable
            $vegetableData = [
                "m_id" => $userInfo["id"],
                "land" => $request->land_id,
                "v_monitor" => $landInfo["monitor"] . "&uid=" . (string)$userInfo["id"],
                "v_price" => 0,
                "f_price" => $request->f_price,
                "v_type" => $vegetableTypeData["id"],
                "planting_time" => $time,
                "v_status" => $request->v_status,
                "create_time" => $time,
                "payment_order_id" => $orderId,
            ];
            MemberVegetableService::addMemberVegetable($vegetableData);

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

            $v_price = isset($request->f_price) ? $request->f_price : 0;
            $v_num = isset($request->f_num) ? $request->f_num : 0;
            $n_price = $v_price * $v_num;
            $buyData = [
                "m_id" => $userInfo["id"],
                "v_price" => $v_price,//兑换的金额
                "v_num" => $v_num,
                "n_price" => $n_price,//总金额
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
            $request->total_amount = $n_price;
            $request->subject = $vegetableTypeData["v_type"] . '种子';

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
