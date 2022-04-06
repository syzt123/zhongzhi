<?php

namespace App\Http\Services;
// 蔬菜地类型
use App\Models\MemberInfo;
use App\Models\VegetableType;

class VegetableTypeService extends BaseService
{
    //蔬菜地类型
    static function addVegetableType($data)
    {
        return VegetableType::addVegetableType($data);
    }

    //获取蔬菜地类型列表
    static function getVegetableTypeList($data = []): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(VegetableType::getVegetableTypeNums($data), $page, $pageSize, VegetableType::getVegetableTypeList($data));

    }

    //删除蔬菜地类型信息
    static function delVegetableTypeById($id, $data = []): int
    {
        return VegetableType::delVegetableType($id, $data);
    }

    static function findVegetableTypeInfoById($id, $data = []): array
    {
        return VegetableType::findVegetableTypeInfoById($id, $data);
    }

    // 蔬菜种子
    static function getSeed()
    {
        $vegetables = VegetableType::getVegetableTypeSeed();
        return $vegetables->groupBy('recommend');
    }
}
