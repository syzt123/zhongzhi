<?php


namespace App\Models\Admin;


class VegetableType extends Base
{
    protected $table = "vegetable_type";
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
