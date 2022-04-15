<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//用户头像
class HeadImg extends Model
{
    protected $table = 'head_img';
    protected $dateFormat = 'U';// 时间格式类型
    protected $fillable = ["m_id", "head"];// 维护更新字段

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';


    //获取用户信息
    static function getUserHeadImg($uid): array
    {
        $rs = self::with([])->where("m_id", $uid)->first();
        if ($rs != null) {
            return $rs->toArray();
        }
        return [];
    }

    // 根据用户id更新头像
    static function updateHeadImg($uId, $data = []): int
    {
        $model = self::with([])->where("m_id", '=', $uId);
        if ($model->first() == null && count($data)) {
            // 新增
            $time = time();
            $data["m_id"] = $uId;
            $data["create_time"] = $time;
            $data["update_time"] = $time;

            return $model->insertGetId($data);
        }
        if (count($data)) {
            return $model->update($data);

        }
        return 0;
    }
}
