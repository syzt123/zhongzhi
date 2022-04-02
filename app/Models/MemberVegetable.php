<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

//用户蔬菜表
class MemberVegetable extends Model
{
    protected $table = 'member_vegetable';
    protected $dateFormat = 'U';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    // 注册模型事件
    protected static function booted()
    {
        // 用户每次查看自己的蔬菜时看看是否坏掉
        static::retrieved(function ($memberVegetable) {
            $vegetableType = $memberVegetable->vegetableType;
            $termOfValidity = array_sum([
                $vegetableType->grow_2,
                $vegetableType->grow_3,
                $vegetableType->grow_4,
                $vegetableType->grow_5,
                $vegetableType->storage_time
            ]);
            // 种植时间 + 生长过程 + 可存储时间 <= 当前时间 视为坏掉
            if (Carbon::createFromTimestamp($memberVegetable->planting_time)->addDays($termOfValidity)->lte(Carbon::now())) {
                $memberVegetable->vegetable_grow = -1;
                $memberVegetable->v_status = 3;
                $memberVegetable->save();
            }

        });
    }

    public function vegetableType()
    {
        return $this->belongsTo(VegetableType::class);
    }
    // 蔬菜的主人
    public function memberInfo()
    {
        return $this->belongsTo(MemberInfo::class, 'm_id');
    }


    // 新增
    static function addMemberVegetable($data): int
    {
        return self::with([])->insertGetId($data);
    }

    function vegetableLand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(VegetableLand::class, 'land');
    }

    function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MemberInfo::class, 'm_id');
    }

    // 查询
    static function getMemberVegetableList($uId, $data = []): array
    {
        $lists = self::with(["vegetableLand", "user"])->where("m_id", '=', $uId);
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
    static function delMemberVegetable($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

    // 总数
    static function getMemberVegetableNumsByUId($uId): int
    {
        $model = self::with([])->where("m_id", $uId);
        return $model->count();
    }

    // 获取用户指定蔬菜
    static function getMemberVegetableByUId($uId, $vegetable_id)
    {
        $model = self::with([])
            ->where("m_id", $uId)
            ->where("v_type", $vegetable_id);
        return $model->first();
    }

    // 获取用户蔬菜
    static function getMemberVegetablesByUId($uId)
    {
        $model = self::with([])
            ->where("m_id", $uId);
        return $model->get();
    }

    // 更新
    static function updateMemberVegetableById($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            return $model->update($data);
        }
        return 0;
    }

}
