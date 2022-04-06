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
        /*switch ($payMethod) {
            case 'ali':
                $this->payInstance = new AliPayCharge();
                break;
            case 'wechat':
                $this->payInstance = new WechatPayCharge();
                break;
            default:
                $this->payInstance = null;
                break;
        }*/
        $this->payInstance = match ($payMethod) {
            'ali' => new AliPayCharge(),
            'wechat' => new WechatPayCharge(),
            default => null,
        };
        return $this;

    }

    //支付
    public function handlePay(Request $request): string
    {
        //var_dump($this);exit();
        if ($this->payInstance == null) {
            return '';
        }
        return $this->payInstance->payOrder($request);
    }
}
