<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//蔬菜种类表
class VegetableType extends Model
{
    protected $table = 'vegetable_type';
    protected $dateFormat = 'U';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    // 新增
    static function addVegetableType($data): int
    {
        return self::with([])->insertGetId($data);
    }

    // 查询
    static function getVegetableTypeList($data = []): array
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
    static function delVegetableType($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

    // 查询
    static function findVegetableTypeInfoById($id, $data = []): array
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
    static function getVegetableTypeNums($data = []): int
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
}
