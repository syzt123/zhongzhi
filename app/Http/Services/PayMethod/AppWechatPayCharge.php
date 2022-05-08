<?php

namespace App\Http\Services\PayMethod;
// 微信支付策略 App
use Illuminate\Http\Request;
use \Yurun\PaySDK\Weixin\APP\Params\Pay\Request as yuRunRequest;
use \Yurun\PaySDK\Weixin\Params\PublicParams;
use \Yurun\PaySDK\Weixin\SDK;

class AppWechatPayCharge implements PayChargeStrategy
{
    // 微信 app
    public function payOrder(Request $request): array
    {
        // 支付 并通知回调
        // 公共配置
        $params = new PublicParams();
        $params->appID = config("comm_code.wx_config.androidAppID");
        $params->mch_id = config("comm_code.wx_config.mch_id");
        $params->key = config("comm_code.wx_config.key");

        // SDK实例化，传入公共配置
        $pay = new SDK($params);
        // 支付接口
        $obj = new yuRunRequest();
        $obj->body = $request->subject; // 商品描述
        $obj->out_trade_no = $request->out_trade_no; // 订单号
        $obj->total_fee = $request->total_amount ?? 1; // 订单总金额，单位为：分
        $obj->spbill_create_ip = $request->getClientIp() ?? '127.0.0.1'; // 客户端ip，必须传正确的用户ip，否则会报错
        $obj->notify_url = config("comm_code.wx_config.notify_url"); // 异步通知地址

        try {
            // 调用接口
            $result = $pay->execute($obj);
            if ($pay->checkResult()) {
                $clientRequest = new \Yurun\PaySDK\Weixin\APP\Params\Client\Request();
                $clientRequest->prepayid = $result['prepay_id'];
                $pay->prepareExecute($clientRequest, $url, $data);
                // 跳转支付界面
                return ["code" => 200, "data" => ["url" => $data], "message" => ""];
            }
        } catch (\Exception $e) {
            return ["code" => -1, "data" => ["url" => ''], "message" => $pay->getError()];
        }
    }


    //
    public function notifyHandle(Request $request)
    {
        echo json_encode(["code" => 'SUCCESS', "msg" => 'ok']);
    }
}
