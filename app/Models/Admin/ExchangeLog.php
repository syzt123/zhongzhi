<?php


namespace App\Models\Admin;

class ExchangeLog extends Base
{
    protected $table = "exchange_log";

    public function getFPriceAttribute($value)
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
