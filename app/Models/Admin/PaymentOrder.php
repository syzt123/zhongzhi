<?php


namespace App\Models\Admin;
use App\Models\PaymentOrder as Base;

class PaymentOrder extends Base
{
    protected $table = "payment_order";
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $casts = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];
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
