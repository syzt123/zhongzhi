<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//用户蔬菜表
class MemberVegetable extends Model
{
    protected $table = 'member_vegetable';

    // 新增
    static function addMemberVegetable($data): int
    {
        return self::with([""])->insertGetId($data);
    }

    // 查询
    static function getMemberVegetableList($uId, $data = []): array
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
    static function delMemberVegetable($id, $data = []): int
    {
        $model = self::with([""])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

    // 总数
    static function getMemberVegetableNumsByUId($uId): int
    {
        $model = self::with([""])->where("m_id", $uId);
        return $model->count();
    }
}
