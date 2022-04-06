<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\PaymentOrderService;
use Illuminate\Http\Request;

class PayDemoController extends Controller
{
    //公共处理
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

    // 支付宝
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
            file_put_contents("ces.txt", $data . PHP_EOL, FILE_APPEND);

            file_put_contents("ces.txt", $_POST["notify_type"] . PHP_EOL, FILE_APPEND);
            file_put_contents("ces.txt", json_encode($_POST) . PHP_EOL, FILE_APPEND);

        }

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
                    echo "success";//echo "fail";
                }
            }
        }
        // 回复的内容
        echo "fail";//echo "success";
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
        // 业务处理
        $bool = self::commHandle();
        if ($bool){
            echo json_encode(["code" => 'SUCCESS', "msg" => 'ok']);
            exit();
        }
        echo json_encode(["code" => 'FAIL', "msg" => 'ok']);
        exit();
    }
}
