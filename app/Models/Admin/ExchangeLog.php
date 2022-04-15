<?php


namespace App\Models\Admin;
use App\Models\ExchangeLog as Base;

class ExchangeLog extends Base
{
    protected $table = "exchange_log";
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $casts = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];
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
