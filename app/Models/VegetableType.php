<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//蔬菜种类表
class VegetableType extends Model
{
    protected $table = 'vegetable_type';

    // 新增
    static function addVegetableType($data): int
    {
        return self::with([""])->insertGetId($data);
    }

    // 查询
    static function getVegetableTypeList($uId, $data = []): array
    {
        $lists = self::with([])->where("m_id",'=', $uId);
        $page = 1;
        $pageSize = 10;
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
        $skipNums = ($page-1) * $pageSize;
        $lists = $lists->skip($skipNums)->limit($pageSize)->get();

        if ($lists) {
            return $lists->toArray();
        }
        return [];
    }
    // 删除
    static function delVegetableType($id, $data = []): int
    {
        $model = self::with([""])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }
}
