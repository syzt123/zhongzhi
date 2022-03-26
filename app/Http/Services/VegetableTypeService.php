<?php

namespace App\Http\Services;
// 蔬菜地类型
use App\Models\MemberInfo;
use App\Models\VegetableType;

class VegetableTypeService extends BaseService
{
    //蔬菜地类型
    static function addVegetableType($data): int
    {
        return VegetableType::addVegetableType($data);
    }

    //获取蔬菜地类型列表
    static function getVegetableTypeList($uid, $data = []): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(VegetableType::getVegetableTypeNumsByUId($uid), $page, $pageSize, VegetableType::getVegetableTypeList($uid, $data));

    }

    //删除蔬菜地类型信息
    static function delVegetableTypeById($id, $data = []): int
    {
        return VegetableType::delVegetableType($id, $data);
    }
}
