<?php


namespace App\Models\Admin;


class PaymentOrder extends Base
{
    protected $table = "payment_order";
    public function getStatusAttribute($value)
    {
        $arr = ["未知","已支付" ,"未支付"];
        return $arr[$value];
    }
    public function getFPriceAttribute($value)
    {
        return bcdiv($value,100,2);
    }
}
