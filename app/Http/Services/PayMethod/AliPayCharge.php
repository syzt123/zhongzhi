<?php

namespace App\Http\Services\PayMethod;
// 支付宝支付策略
use Illuminate\Http\Request;
use Yurun\PaySDK\AlipayApp\Params\PublicParams;
use Yurun\PaySDK\AlipayApp\SDK;
use Yurun\PaySDK\AlipayApp\App\Params\Pay\Request as yuRunRequest;

class AliPayCharge implements PayChargeStrategy
{
    // 支付宝 app支付
    public function payOrder(Request $request): array
    {
        // 支付 并通知回调
        // 公共配置
        $params = new PublicParams();
        $params->appID = config("comm_code.ali_config.appID");
        $params->sign_type = config("comm_code.ali_config.sign_type"); // 默认就是RSA2
        $params->appPrivateKey = config("comm_code.ali_config.appPrivateKey");
        $params->appPrivateKeyFile = config("comm_code.ali_config.appPrivateKeyFile"); // 证书文件，如果设置则这个优先使用
        $params->apiDomain = config("comm_code.ali_config.apiDomain"); // 设为沙箱环境，如正式环境请把这行注释

        // SDK实例化，传入公共配置
        $pay = new SDK($params);

        // 支付接口
        $obj = new yuRunRequest;
        $obj->notify_url = config("comm_code.ali_config.notify_url"); // 支付后通知地址（作为支付成功回调，这个可靠）
        //$obj->return_url = config("comm_code.ali_config.return_url"); // 支付后跳转返回地址
        $obj->businessParams->out_trade_no = $request->out_trade_no ?? 'test' . mt_rand(10000000, 99999999); // 商户订单号
        $obj->businessParams->total_amount = $request->total_amount ?? 0.01; // 价格元
        $obj->businessParams->subject = $request->subject ?? '小米手机9黑色陶瓷尊享版'; // 商品标题
        $obj->businessParams->passback_params = urlencode(json_encode(["pay_type"=>$request->pay_type]));
        // 跳转到支付页面
        //$pay->redirectExecute($obj);

        // 获取跳转url
        $pay->prepareExecute($obj, $url, $data);
        return ["code" => 200, "data" => ["url" => http_build_query($data)], "message" => ""];
    }

    public function notifyHandle(Request $request)
    {
        // $_POST接收的数据为json
        $data = file_get_contents("php://input");// 为字符串
        if ($data != false) {
            file_put_contents("ces.txt", $data . PHP_EOL, FILE_APPEND);

            file_put_contents("ces.txt", $_POST["notify_type"] . PHP_EOL, FILE_APPEND);
            file_put_contents("ces.txt", json_encode($_POST) . PHP_EOL, FILE_APPEND);

        }
        // 回复的内容
        echo "success";//echo "fail";
    }
}
