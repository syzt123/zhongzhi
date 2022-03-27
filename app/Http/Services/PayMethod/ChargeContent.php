<?php

namespace App\Http\Services;
// 支付选择策略
class ChargeContent
{
    private $payInstance = null;

    // 实例化
    public function initInstance($payMethod = 'ali'): void
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

    }

    //支付
    public function handlePay()
    {
        if ($this->payInstance == null) {
            return;
        }
        return $this->payInstance->payOrder();
    }
}
