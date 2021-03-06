<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//物流订单表
class DeliveryOrder extends Model
{
    protected $table = 'delivery_order';
    protected $dateFormat = 'U';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    // 新增
    static function addDeliveryOrder($data): int
    {
        return self::with([])->insertGetId($data);
    }

    // 关联用户蔬菜表
    function memberVegetable()
    {
        return $this->belongsTo(MemberVegetable::class, 'm_v_id');
    }

    // 查询
    static function getDeliveryOrderList($uId, $data = []): array
    {
        $lists = self::with(["memberVegetable", 'memberVegetable.vegetableType'])->where("m_id", '=', $uId);

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
    static function delDeliveryOrder($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

    // 总数
    static function getDeliveryOrderNumsByUId($uId): int
    {
        $model = self::with([])->where("id", $uId);
        return $model->count();
    }

    // 更新
    static function updateDeliveryOrder($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            return $model->update($data);
        }
        return 0;
    }
}
