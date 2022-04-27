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
            if ($vegetableType == null) {
                return;
            }
            $termOfValidity = array_sum([
                $vegetableType->grow_2,
                $vegetableType->grow_3,
                $vegetableType->grow_4,
                $vegetableType->grow_5,
                $vegetableType->storage_time
            ]);
            // 种植时间 + 生长过程 + 可存储时间 <= 当前时间 视为坏掉

            if (Carbon::createFromTimestamp($memberVegetable->planting_time)->lte(Carbon::now())
                <= $vegetableType->grow_2) {
                $memberVegetable->vegetable_grow = 1;
            } elseif (Carbon::createFromTimestamp($memberVegetable->planting_time)->lte(Carbon::now())
                <= $tow = bcadd($vegetableType->grow_2, $vegetableType->grow_3)) {
                $memberVegetable->vegetable_grow = 2;
            } elseif (Carbon::createFromTimestamp($memberVegetable->planting_time)->lte(Carbon::now())
                <= $three = bcadd($vegetableType->grow_4, $tow)) {
                $memberVegetable->vegetable_grow = 3;
            } elseif (Carbon::createFromTimestamp($memberVegetable->planting_time)->lte(Carbon::now())
                <= $four = bcadd($vegetableType->grow_5, $three)) {
                $memberVegetable->vegetable_grow = 4;
            } elseif (Carbon::createFromTimestamp($memberVegetable->planting_time)->lte(Carbon::now())
                <= $five = bcadd($four, $vegetableType->storage_time)) {
                $memberVegetable->vegetable_grow = 5;
            } else {
                $memberVegetable->vegetable_grow = -1;
//                $memberVegetable->v_status = 3;
            }
            $memberVegetable->save();
            $memberVegetable->makeHidden(['vegetable_type']);
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
        return self::with([])->insert($data);
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
        $lists = self::with(["vegetableLand", "user", "vegetableType"])->where("m_id", '=', $uId);
        if (isset($data["vegetable_grow"]) && $data["vegetable_grow"] > 0) {
            $lists = $lists->where('vegetable_grow', '>', 0);
            unset($data["vegetable_grow"]);
        }
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
    static function getMemberVegetableNumsByUId($uId, $data = []): int
    {
        $model = self::with([])->where("m_id", $uId);
        if (isset($data["vegetable_grow"]) && $data["vegetable_grow"] > 0) {
            $model = $model->where('vegetable_grow', '>', 0);
            unset($data["vegetable_grow"]);
        }
        if (isset($data["page"])) {
            unset($data["page"]);
        }
        if (isset($data["page_size"])) {
            unset($data["page_size"]);
        }
        if (count($data) > 0) {
            $model = $model->where($data);
        }
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

    // 获取用户蔬菜
    static function getGrowMemberVegetablesByUId($uId)
    {
        $model = self::with([])
            ->where("m_id", $uId)
//            ->where("vegetable_grow", '>', '0')
            ->where("v_status", "!=", 3);
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

    // 更新数量
    static function updateNumsMemberVegetableById($id, $uId, $nums = 1)
    {
        $model = self::with([])->where("id", $id)->where("m_id", $uId);
        return $model->decrement('nums', $nums);
    }

    // 更新当种子存在则更新数量
    static function addMemberVegetableNums($data = [], $nums = 0): int
    {
        $model = self::with([]);
        if (count($data)) {
            $rs = $model->where($data)->first();
            if ($rs != null) {
                //更新
                return $model->where($data)->increment("nums", $nums);
            }
            return 0;
        }
        return 0;
    }

}
