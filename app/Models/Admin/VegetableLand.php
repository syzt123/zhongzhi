<?php


namespace App\Models\Admin;


class VegetableLand extends Base
{
    protected $table = "vegetable_land";
    public function getLStatusAttribute($value)
    {
        $arr = ["未使用", '已使用' , "其他"];
        return $arr[$value];
    }
}
