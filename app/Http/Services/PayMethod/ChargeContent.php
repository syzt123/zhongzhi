<?php

namespace App\Http\Services\PayMethod;
// 支付选择策略
use Illuminate\Http\Request;

class ChargeContent
{
    private $payInstance = null;

    // 实例化
    public function initInstance($payMethod = 'ali'): self
    {
        $this->payInstance = match ($payMethod) {
            'ali' => new AliPayCharge(),
            //'wechat' => new WechatPayCharge(),
            "app_wechat" => new AppWechatPayCharge(),//需要审核通过
            "h5_wechat" => new H5WechatPayCharge(),//需要审核通过
            "js_wechat" => new JsWechatPayCharge(),//openid 必须关注公众号
            "native_wechat" => new NativeWechatPayCharge(),
            default => null,
        };
        return $this;

    }

    //支付
    public function handlePay(Request $request): array
    {
        if ($this->payInstance == null) {
            return ["code" => -1, "data" => ["url" => ''], "message" => ''];
        }
        return $this->payInstance->payOrder($request);
    }
}
