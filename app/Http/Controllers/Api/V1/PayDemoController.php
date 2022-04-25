<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\MemberInfoService;
use App\Http\Services\MemberVegetableService;
use App\Http\Services\PaymentOrderService;
use App\Http\Services\VegetableTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayDemoController extends Controller
{
    //公共处理 未用
    static function commHandle(): int
    {
        $orderId = $_POST["out_trade_no"];
        $aliOrderId = $_POST["trade_no"];
        $totalMoney = (float)$_POST["total_amount"];
        // 判断是否为当前订单
        if (isset($_POST["trade_status"]) && $_POST["trade_status"] === 'TRADE_SUCCESS') {
            //业务处理 修改订单为已完成
            $orderInfo = PaymentOrderService::getOrderInfoByOrderId($orderId, []);
            if (isset($orderInfo["status"]) && $orderInfo["status"] != config("comm_code.pay_order.pay_ok")) {
                $bool = PaymentOrderService::updateOrderStatusInfoByOrderId($orderId, [
                    "status" => config("comm_code.pay_order.pay_ok"),
                    "wechat_no" => $aliOrderId,
                    "pay_price" => $totalMoney,
                ]);
                if ($bool) {
                    return $bool;
                }
            }
        }
        return 0;
    }

    // 支付宝h5 回调
    function pay(Request $request)
    {
        /*// 设置订单号
        $request->out_trade_no = self::getUniqueOrderNums();
        // 公共配置
        $payInstance = new ChargeContent();
        $payInstance = $payInstance->initInstance('ali');
        $payUrl = $payInstance->handlePay($request);
        // 跳转新页面
        header("location:" . $payUrl);
        var_dump($payUrl);*/
    }

    //支付回调
    function payNotify()
    {
        // $_POST接收的数据为json
        $data = file_get_contents("php://input");// 为字符串

        if ($data != false) {
            Log::info('支付宝支付回调数据', ['data' => $data, "post" => json_encode($_POST)]);
            $orderId = $_POST["out_trade_no"];
            $aliOrderId = $_POST["trade_no"];
            $totalMoney = (float)$_POST["total_amount"];
            // 判断是否为当前订单
            if (isset($_POST["trade_status"]) && $_POST["trade_status"] === 'TRADE_SUCCESS') {
                //业务处理 修改订单为已完成
                $orderInfo = PaymentOrderService::getOrderInfoByOrderId($orderId, []);
                if (isset($orderInfo["status"]) && $orderInfo["status"] != config("comm_code.pay_order.pay_ok")) {
                    DB::beginTransaction();
                    try {

                        $bool = PaymentOrderService::updateOrderStatusInfoByOrderId($orderId, [
                            "status" => config("comm_code.pay_order.pay_ok"),
                            "third_order_no" => $aliOrderId,
                            "pay_price" => $totalMoney,
                        ]);
                        //新增用户蔬菜种子
                        $rqIdsArr = json_decode($orderInfo["v_ids"]);
                        $time = time();
                        $addVegetableData = [];
                        $totalNums = 0;
                        $nameStr = '';
                        $totalPrice = 0.00;

                        foreach ($rqIdsArr as $v) {
                            if (isset($v->id) && $v->id > 0) {
                                $vegetableTypeData = VegetableTypeService::findVegetableTypeInfoById($v->id);
                                if (count($vegetableTypeData) == 0) {
                                    continue;
                                }
                                if (isset($v->nums) && $v->nums <= 0) {
                                    continue;
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
                                    "m_id" => $orderInfo["m_id"],
                                    "v_price" => $vegetableTypeData["v_price"] ?? 0,
                                    "f_price" => 0,
                                    "pay_price" => $vegetableTypeData["v_price"] * $v->nums,
                                    "v_type" => $vegetableTypeData["id"],
                                    "nums" => $v->nums,
                                    "planting_time" => $time,
                                    "v_status" => 1,//已种植
                                    "create_time" => $time,
                                    "vegetable_grow" => 1,//大于0说明生长中
                                    "payment_order_id" => $orderInfo["id"],
                                    "v_name" => $vegetableTypeData["v_type"],//名字
                                    "vegetable_type_id" => count($vegetableTypeData["vegetable_kinds"]) > 0 ? $vegetableTypeData["vegetable_kinds"]["id"] : 1,
                                    "yield" => $vegetableTypeData["estimated_output"] * $v->nums ?? 100,
                                ];
                            }
                        }

                        MemberVegetableService::addMemberVegetable($addVegetableData);

                        // 用户蔬菜自增
                        MemberInfoService::increaseVegetableNums($orderInfo["m_id"], $totalNums);
                        DB::commit();

                        if ($bool) {
                            Log::info('支付宝回调处理完成', ['third_order_no' => $aliOrderId]);
                            echo "success";//echo "fail";
                            exit();
                        }
                    } catch (\Exception $e) {
                        Log::info('支付宝回调处理完成失败', ['third_order_no' => $aliOrderId, "reason" => $e->getMessage()]);
                        echo "fail";//echo "fail";
                        DB::rollBack();
                        exit();
                    }
                }
            }
        }


        // 回复的内容
        echo "fail";//echo "success";
        exit();
    }

    //微信支付 h5支付
    function wxPay()
    {
        // 公共配置
        /*$params = new \Yurun\PaySDK\Weixin\Params\PublicParams();
        $params->appID = $GLOBALS['PAY_CONFIG']['appid'];
        $params->mch_id = $GLOBALS['PAY_CONFIG']['mch_id'];
        $params->key = $GLOBALS['PAY_CONFIG']['key'];

        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\Weixin\SDK($params);
        // 支付接口
        $obj = new yuRunRequest();
        $obj->body = 'test'; // 商品描述
        $obj->out_trade_no = 'test' . mt_rand(10000000, 99999999); // 订单号
        $obj->total_fee = 1; // 订单总金额，单位为：分
        $obj->spbill_create_ip = '127.0.0.1'; // 客户端ip，必须传正确的用户ip，否则会报错
        $obj->notify_url = $GLOBALS['PAY_CONFIG']['pay_notify_url']; // 异步通知地址
        $obj->scene_info = new \Yurun\PaySDK\Weixin\H5\Params\SceneInfo();
        $obj->scene_info->type = 'Wap'; // 可选值：IOS、Android、Wap
        // 下面参数根据type不同而不同
        $obj->scene_info->wap_url = 'https://baidu.com';
        $obj->scene_info->wap_name = 'test';

        // 调用接口
        $result = $pay->execute($obj);
        if ($pay->checkResult()) {
            // 跳转支付界面
            header('Location: ' . $result['mweb_url']);
        } else {
            var_dump($pay->getErrorCode() . ':' . $pay->getError());
        }
        exit;*/
    }

    //微信回调
    function wxPayNotify()
    {
        // 接收微信推送的数据
        // https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml
        $data = file_get_contents('php://input');

        if ($data != false) {
            $wxArr = $this->xmlToArr($data);
            $orderId = $wxArr["out_trade_no"];
            $wxOrderId = $wxArr["transaction_id"];
            $totalMoney = number_format((int)$wxArr["total_fee"] / 100, 2);
            Log::info('微信支付回调数据', ['data' => $data, "post" => json_encode($wxArr)]);
            if (count($wxArr) > 0 && $wxArr["out_trade_no"] != '') {
                //业务处理 修改订单为已完成
                $orderInfo = PaymentOrderService::getOrderInfoByOrderId($wxArr["out_trade_no"], []);
                if (isset($orderInfo["status"]) && $orderInfo["status"] != config("comm_code.pay_order.pay_ok")) {

                    DB::beginTransaction();
                    try {

                        $bool = PaymentOrderService::updateOrderStatusInfoByOrderId($orderId, [
                            "status" => config("comm_code.pay_order.pay_ok"),
                            "third_order_no" => $wxOrderId,
                            "pay_price" => $totalMoney,
                        ]);
                        //新增用户蔬菜种子
                        $rqIdsArr = json_decode($orderInfo["v_ids"]);
                        $time = time();
                        $addVegetableData = [];
                        $totalNums = 0;
                        $totalPrice = 0.00;
                        foreach ($rqIdsArr as $v) {
                            if (isset($v->id) && $v->id > 0) {
                                $vegetableTypeData = VegetableTypeService::findVegetableTypeInfoById($v->id);
                                if (count($vegetableTypeData) == 0) {
                                    continue;
                                }
                                if (isset($v->nums) && $v->nums <= 0) {
                                    continue;
                                }
                                // 单个商品总价
                                $singleVegetablePrice = $v->nums * $vegetableTypeData["v_price"];
                                $totalPrice += $singleVegetablePrice;
                                $totalNums += $v->nums;
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
                                    "m_id" => $orderInfo["m_id"],
                                    "v_price" => $vegetableTypeData["v_price"] ?? 0,
                                    "f_price" => 0,
                                    "pay_price" => $vegetableTypeData["v_price"] * $v->nums,
                                    "v_type" => $vegetableTypeData["id"],
                                    "nums" => $v->nums,
                                    "planting_time" => $time,
                                    "v_status" => 1,//已种植
                                    "vegetable_grow" => 1,//大于0说明生长中
                                    "create_time" => $time,
                                    "payment_order_id" => $orderInfo["id"],
                                    "v_name" => $vegetableTypeData["v_type"],//名字
                                    "vegetable_type_id" => count($vegetableTypeData["vegetable_kinds"]) > 0 ? $vegetableTypeData["vegetable_kinds"]["id"] : 1,
                                    "yield" => $vegetableTypeData["estimated_output"] * $v->nums ?? 100,
                                ];
                            }

                        }

                        MemberVegetableService::addMemberVegetable($addVegetableData);
                        // 用户蔬菜自增
                        MemberInfoService::increaseVegetableNums($orderInfo["m_id"], $totalNums);

                        DB::commit();

                        if ($bool) {
                            Log::info("微信支付回调处理成功：", ["transaction_id" => $wxArr["transaction_id"]]);
                            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                            exit();
                        }
                    } catch (\Exception $e) {
                        Log::info('微信回调处理完成失败', ['third_order_no' => $wxOrderId, "reason" => $e->getMessage()]);
                        echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>';
                        DB::rollBack();
                        exit();
                    }

                }
            }
        }

        echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>';
        exit();
    }
}
