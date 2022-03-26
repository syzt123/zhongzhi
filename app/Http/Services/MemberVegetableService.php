<?php

namespace App\Http\Services;
// 用户蔬菜表
use App\Models\MemberVegetable;

class MemberVegetableService extends BaseService
{
    //添加蔬菜
    static function addMemberVegetable($data): int
    {
        return MemberVegetable::addMemberVegetable($data);
    }

    //获取蔬菜列表
    static function getMemberVegetableList($uid, $data = []): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(MemberVegetable::getMemberVegetableNumsByUId($uid), $page, $pageSize, MemberVegetable::getMemberVegetableList($uid, $data));

    }

    //删除蔬菜信息
    static function delMemberVegetableById($id, $data = []): int
    {
        return MemberVegetable::delMemberVegetable($id, $data);
    }
}
