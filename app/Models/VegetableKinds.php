<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//蔬菜大分类类型表
class VegetableKinds extends Model
{
    protected $table = 'vegetable_kinds';
    protected $dateFormat = 'U';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    // 新增
    static function addVegetableKinds($data): int
    {
        return self::with([])->insertGetId($data);
    }

    // 查询
    static function getVegetableKindsList($data = []): array
    {
        $lists = self::with([]);
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
    static function delVegetableKinds($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

    // 查询
    static function findVegetableKindsInfoById($id, $data = []): array
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        $rs = $model->first();
        if ($rs != null) {
            return $rs->toArray();
        }
        return [];
    }

    // 获取种类总数
    static function getVegetableKindsNums($data = []): int
    {
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            unset($data["page"]);
        }
        if (isset($data["page_size"])) {
            unset($data["page_size"]);
        }
        $model = self::with([]);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->count();
    }

    public function vegetableResources()
    {
        return $this->hasMany(VegetableResources::class);
    }

    // 蔬菜种子
    static function getVegetableTypeSeed()
    {
        $model = self::with([]);
        $vegetables = $model
            ->where('status', '=', 1)
            ->get();
        foreach ($vegetables as $vegetable) {
            $vegetable->vegetable_img = $vegetable
                ->vegetableResources()
                ->where('vegetable_resources.vegetable_grow', '=', 1)
                ->where('vegetable_resources.vegetable_resources_type', '=', 1)
                ->value('vegetable_resources');
        }
        return $vegetables;
    }

    function vegetable_list()
    {
        return $this->hasMany(MemberVegetable::class, 'vegetable_type_id', 'id');
    }

    // 根据用户id分类下的所有蔬菜 分页信息
    static function getVegetableTypeList($data = []): array
    {
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

        $skipNums = ($page - 1) * $pageSize;
        $model = self::with([
            /*"vegetable_list" => function ($q) use ($userId, $sort, $pageSize, $skipNums) {
                $q->where("member_vegetable.m_id", $userId)
                    ->skip($skipNums)->limit($pageSize)->orderBy("id", $sort);
            }*/
        ]);
        $vegetableTypes = $model->skip($skipNums)->limit($pageSize)->orderBy("id", $sort)->get();
        if ($vegetableTypes != null) {
            return $vegetableTypes->toArray();
        }
        return [];
    }

}
