<?php


namespace App\Models\Admin;

class BuyLog extends Base
{
    protected $table = "buy_log";

    public function getNPriceAttribute($value)
    {
        return bcdiv($value,100,2);
    }
    public function getVPriceAttribute($value)
    {
        return bcdiv($value,100,2);
    }
    public function memberInfo(){
        return $this->belongsTo(MemberInfo::class,'m_id');
    }
}
