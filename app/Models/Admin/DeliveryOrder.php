<?php


namespace App\Models\Admin;


class DeliveryOrder extends Base
{
    protected $table = "delivery_order";
    public function getStatusAttribute($value)
    {
        $arr = ["未知","待配送" ,"配送中","配送完成"];
        return $arr[$value];
    }
    public function getFPriceAttribute($value)
    {
        return bcdiv($value,100,2);
    }
}
