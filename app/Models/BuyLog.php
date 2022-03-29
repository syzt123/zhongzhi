<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//购买蔬菜的记录表
class BuyLog extends Model
{
    protected $table = 'buy_log';

    // 新增
    static function addUserBuyLog($data): int
    {
        return self::with([])->insertGetId($data);
    }

    // 查询
    static function getUserBuyLogList($uId, $data = []): array
    {
        $lists = self::with([])->where("m_id", '=', $uId);

        $page = 1;
        $pageSize = 10;
        $sort = 'desc';// desc asc
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
            unset($data["page"]);
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
            unset($data["page_size"]);
        }
        if (isset($data["page_size"])) {
            $sort = $data["sort"];
            unset($data["sort"]);
        }
        if (count($data)) {
            $lists = $lists->where($data);
        }
        $skipNums = ($page - 1) * $pageSize;
        $lists = $lists->skip($skipNums)->limit($pageSize)->orderBy("id", $sort)->get();
        if ($lists) {
            return $lists->toArray();
        }
        return [];
    }

    // 删除
    static function delUserBuyLog($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

    // 总数
    static function getBuyLogNumsByUId($uId): int
    {
        $model = self::with([])->where("m_id", $uId);
        return $model->count();
    }
}
