<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//兑换记录表
class ExchangeLog extends Model
{
    protected $table = 'exchange_log';
    protected $dateFormat = 'U';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    // 新增
    static function addExchangeLog($data): int
    {
        return self::with([])->insertGetId($data);
    }

    function memberVegetable()
    {
        return $this->belongsTo(MemberVegetable::class, "m_v_id");
    }

    function user()
    {
        return $this->belongsTo(MemberInfo::class, "m_id");
    }

    // 查询
    static function getExchangeLogList($uId, $data = []): array
    {
        $lists = self::with(["user", "memberVegetable"])->where("m_id", '=', $uId);

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
    static function delExchangeLog($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

    // 总数
    static function getExchangeLogNumsByUId($uId): int
    {
        $model = self::with([])->where("m_id", $uId);
        return $model->count();
    }
}
