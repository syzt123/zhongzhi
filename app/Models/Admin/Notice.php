<?php


namespace App\Models\Admin;
use App\Models\Notice as Base;

class Notice extends Base
{
    protected $table = "notice";
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $casts = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];
}
