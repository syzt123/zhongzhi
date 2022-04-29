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
     *     @OA\Parameter(name="pay_type", in="query", @OA\Schema(type="string"),description="支付类型 ali：支付宝支付 h5_wechat:微信h5支付支付 js_wechat:（微信公众号支付 需要获取oendId） native_wechat：（Native支付是指商户系统按微信支付协议生成支付二维码，用户再用微信“扫一扫”完成支付的模式。该模式适用于PC网站、实体店单品或订单、媒体广告支付等场景） app_wechat:微信app支付方式"),
     *     @OA\Parameter(name="openid", in="query", @OA\Schema(type="string"),description="当支付类型为：js_wechat时 必须。其他类型支付不传"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 @OA\Property(property="url", type="string", description="生成的付款地址 直接浏览器打开地址"),
     *            ),
     *             @OA\Examples(example="success1", value={"url": "https://openapi.alipay.com/gateway.do?format=json&charset=utf-8&sign_type=RSA2&version=1.0&method=alipay.trade.wap.pay&return_url=https%3A%2F%2Fpay.zjzc88.com%2Fadmin&notify_url=https%3A%2F%2Fpay.zjzc88.com%2Fapi%2Falipay_notify&app_id=2017022805954738&biz_content=%7B%22out_trade_no%22%3A%22202204172359223679069331%22%2C%22product_code%22%3A%22QUICK_WAP_WAY%22%2C%22total_amount%22%3A%220.01%22%2C%22subject%22%3A%22%5Cu6d4b%5Cu8bd5%5Cu83b2%5Cu82b1%5Cu767d%2A1_%5Cu79cd%5Cu5b50%22%2C%22goods_type%22%3A1%7D&timestamp=2022-04-17+23%3A59%3A22&sign=LvGUp7b6n%2BMh%2F0xrj1rtEOVgpRF%2BJ%2FTkN%2F66j3VuxARL1xS%2F8JWv4xKhz1S%2FACu5tiZSgBowTGc2jiRgj4C3Q3Qwjr9v37SiiQUpxZ2y2XdTY1yH26mPP0USdBS0t1%2BfXEgEGcjFPZ94u0yRDlJRWfpTkmYz7ojtAq93HrILEebpGCNflxHj7NFoAfiKccPWPLkdAGr1ZprSseSQwUuL6jMzGTUhXe00KK2PdFB5iyomqinja5BT0cz5zm4Ug%2FuPu02djiiUxGWIIAusjmLpoVFRwuO3jFZyG%2B4JRThO7UH26CPbINFVXes55As89FMqIuuHclxVmCKGHxh8k5SafA%3D%3D","message":"打开连接进行支付即可","code":200}, summary="支付宝支付成功"),
     *             @OA\Examples(example="success2", value={"url": "https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id=wx2016121516420242444321ca0631331346&package=1405458241","message":"打开连接进行支付即可","code":200}, summary="h5微信支付成功"),
     *             @OA\Examples(example="success3", value={"url": "见message内容 WeixinJSBridge 调用支付 参考地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6","message":{
    "appId": "wx06c743308613d1ce",
    "timeStamp": "1650209649",
    "nonceStr": "21841ae2adfaf8c60e18b9b3f896d34d",
    "package": "prepay_id=wx17233409403226802fbcd3c321d01c0000",
    "signType": "MD5",
    "paySign": "AAF5FAA575669EF9E4F7894235D3C329"
    },"code":200}, summary="js微信支付成功"),
     *             @OA\Examples(example="success4", value={"url": "weixin://wxpay/bizpayurl?pr=jGbiXY0zz","message":"生成二维码进行支付","code":200}, summary="native微信支付成功"),
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

        if ($request->pay_type === 'js_wechat') {
            if (!isset($request->openid)) {
                return $this->backArr('openid必须存在', config("comm_code.code.fail"), []);
            }
        }
        $totalPrice = 0.00;
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
                "f_price" => 0,//兑换的金额
                "v_ids" => $request->v_ids ?? '[]',
                "status" => 2,// 1已支付，2未支付\r\n（默认为2）
                "order_id" => $createOrderId,
                "wechat_no" => '',// 微信或者支付宝的订单号
                "pay_price" => 0,// 实际支付的价格
                "create_time" => $time,
                "update_time" => $time,
                "pay_type" => $request->pay_type,
            ];
            $orderId = PaymentOrderService::addPaymentOrder($data);
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
                }
            }

            $buyData = [
                "m_id" => $userInfo["id"],
                "v_price" => 0,//兑换的金额
                "v_num" => $totalNums,
                "n_price" => number_format($totalPrice, 2),//总金额
                "payment_order_id" => $orderId,// 订单表id
                "create_time" => $time,
            ];
            // 购买记录
            $buyBool = BuyLogService::addUserBuyLog($buyData);

            // 用户蔬菜自增
            //MemberInfoService::increaseVegetableNums($userInfo["id"]);
            // 调用支付
            // 设置订单号
            $request->out_trade_no = $createOrderId;
            $request->total_amount = number_format($totalPrice, 2);
            $request->subject = $nameStr . '种子';

            $payInstance = new ChargeContent();
            $payMethod = $request->pay_type ?? 'ali';
            if ($request->pay_type != 'ali') {
                $request->total_amount = number_format($totalPrice, 2) * 100;
            }

            $payInstance = $payInstance->initInstance($payMethod);
            $payRs = $payInstance->handlePay($request);
            if ($payRs["code"] == -1) {
                return $this->backArr($payRs["message"], config("comm_code.code.fail"), ["url" => []]);
            }

            DB::commit();

            if ($buyBool) {
                $url = $payRs["data"]["url"];
                if ($request->pay_type === 'js_wechat') {
                    $url = json_decode($url);
                }
                return $this->backArr('新增订单成功,请前往付款！', config("comm_code.code.ok"), ["url" => $url]);
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
