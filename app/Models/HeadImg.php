<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//用户头像
class HeadImg extends Model
{
    protected $table = 'head_img';

    //获取用户信息
    static function getUserHeadImg($uid): array
    {
        $rs = self::with([])->where("m_id", $uid)->first();
        if ($rs != null) {
            return $rs->toArray();
        }
        return [];
    }

    // 更新
    static function updateHeadImg($uId, $data = []): int
    {
        $model = self::with([])->where("m_id", $uId);
        if (count($data)) {
            return $model->updateOrInsert($data);
        }
        return 0;
    }
}
