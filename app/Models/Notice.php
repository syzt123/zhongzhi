<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//公告栏
class Notice extends Model
{
    protected $table = 'notice';

    // 新增
    static function addNotice($data): int
    {
        return self::with([""])->insertGetId($data);
    }

    // 查询
    static function getNoticeInfo(): array
    {
        $info = self::with([])->first();
        if ($info != null) {
            return $info->toArray();
        }
        return [];
    }

    // 删除
    static function delNotice($id = 1, $data = []): int
    {
        $model = self::with([""])->where("id", '=', $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

}
