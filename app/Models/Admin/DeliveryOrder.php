<?php


namespace App\Models\Admin;
use App\Models\DeliveryOrder as Base;

class DeliveryOrder extends Base
{
    protected $table = "delivery_order";
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $casts = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];
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
