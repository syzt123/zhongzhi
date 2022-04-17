<?php

namespace App\Http\Services\PayMethod;
// 微信支付策略 H5
use Illuminate\Http\Request;
use \Yurun\PaySDK\Weixin\H5\Params\Pay\Request as yuRunRequest;
use \Yurun\PaySDK\Weixin\JSAPI\Params\Pay\Request as jsRequest;
use \Yurun\PaySDK\Weixin\Native\Params\Pay\Request as nativeRequest;
use \Yurun\PaySDK\Weixin\Params\PublicParams;
use \Yurun\PaySDK\Weixin\SDK;

class H5WechatPayCharge implements PayChargeStrategy
{
    // 微信 h5
    public function payOrder(Request $request): array
    {
        // 支付 并通知回调
        // 公共配置
        $params = new PublicParams();
        $params->appID = config("comm_code.wx_config.appID");
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
        //$obj->scene_info = new SceneInfo();
        //$obj->scene_info->type = 'Wap'; // 可选值：IOS、Android、Wap
        // 下面参数根据type不同而不同
        //$obj->scene_info->wap_url = 'https://baidu.com';
        //$obj->scene_info->wap_name = 'test';

        try {
            // 调用接口
            $result = $pay->execute($obj);
            if ($pay->checkResult()) {
                // 跳转支付界面
                return ["code" => 200, "data" => ["url" => $result['mweb_url']], "message" => ""];
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
