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
            "h5_wechat" =>new H5WechatPayCharge(),//需要审核通过
            "js_wechat"=>new JsWechatPayCharge(),//openid 必须关注公众号
            "native_wechat"=>new NativeWechatPayCharge(),
            default => null,
        };
        return $this;

    }

    //支付
    public function handlePay(Request $request): string
    {
        if ($this->payInstance == null) {
            return '';
        }
        return $this->payInstance->payOrder($request);
    }
}
