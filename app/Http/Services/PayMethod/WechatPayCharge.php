<?php

namespace App\Http\Services\PayMethod;
// 微信支付策略
use Illuminate\Http\Request;
use \Yurun\PaySDK\Weixin\H5\Params\Pay\Request as yuRunRequest;
use \Yurun\PaySDK\Weixin\JSAPI\Params\Pay\Request as jsRequest;
use \Yurun\PaySDK\Weixin\Native\Params\Pay\Request as nativeRequest;
use \Yurun\PaySDK\Weixin\Params\PublicParams;
use \Yurun\PaySDK\Weixin\SDK;

class WechatPayCharge implements PayChargeStrategy
{
    // 微信 h5
    public function demoPayOrder(Request $request): string
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
        $obj->body = 'test'; // 商品描述
        $obj->out_trade_no = 'test' . mt_rand(10000000, 99999999); // 订单号
        $obj->total_fee = 1; // 订单总金额，单位为：分
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
                //header('Location: ' . $result['mweb_url']);
                return $result['mweb_url'];
            }
        } catch (\Exception $e) {
            //var_dump($pay->getErrorCode() . ':' . $pay->getError());
            return $pay->getError();
        }
    }

    //js
    public function jsPayOrder(Request $request): string
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
        $obj = new jsRequest();
        $obj->body = 'test'; // 商品描述
        $obj->out_trade_no = 'test' . mt_rand(10000000, 99999999); // 订单号
        $obj->total_fee = 1; // 订单总金额，单位为：分
        $obj->spbill_create_ip = $request->getClientIp() ?? '127.0.0.1'; // 客户端ip，必须传正确的用户ip，否则会报错
        $obj->notify_url = config("comm_code.wx_config.notify_url"); // 异步通知地址
        $obj->openid = 'opWUlwsi_2Yy9ScbM9EdSJCxY-QA'; // 必须设置openid
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
                $request = new \Yurun\PaySDK\Weixin\JSAPI\Params\JSParams\Request();
                $request->prepay_id = $result['prepay_id'];
                $jsapiParams = $pay->execute($request);
                // 最后需要将数据传给js，使用WeixinJSBridge进行支付
                return json_encode($jsapiParams);
            }
        } catch (\Exception $e) {
            return $pay->getError();
        }
    }

    // native
    /*public function nativePayOrder(Request $request): string
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
        $obj = new nativeRequest();
        $obj->body = 'test'; // 商品描述
        $obj->out_trade_no = 'test' . mt_rand(10000000, 99999999); // 订单号
        $obj->total_fee = 1; // 订单总金额，单位为：分
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
            return $result['code_url'];
        } catch (\Exception $e) {
            //var_dump($pay->getErrorCode() . ':' . $pay->getError());
            return $pay->getError();
        }
    }*/
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
        $obj = new nativeRequest();
        $obj->body = 'test'; // 商品描述
        $obj->out_trade_no = 'test' . mt_rand(10000000, 99999999); // 订单号
        $obj->total_fee = 1; // 订单总金额，单位为：分
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
            return $result['code_url'];
        } catch (\Exception $e) {
            dd($pay->getErrorCode() . ':' . $pay->getError());
            return $pay->getError();
        }
    }

    public function notifyHandle(Request $request)
    {
        echo json_encode(["code" => 'SUCCESS', "msg" => 'ok']);
    }
}
