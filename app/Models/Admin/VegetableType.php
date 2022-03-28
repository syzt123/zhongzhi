<?php


namespace App\Models\Admin;
use App\Models\VegetableType as Base;

class VegetableType extends Base
{
    protected $table = "vegetable_type";
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $casts = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];
    public function getVPriceAttribute($value)
    {
        return bcdiv($value,100,2);
    }
    public function getFPriceAttribute($value)
    {
        return bcdiv($value,100,2);
    }
    public function getStatusAttribute($value)
    {
        $arr = ["未知","可种植" ,"不可种植"];
        return $arr[$value];
    }


}
