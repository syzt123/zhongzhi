<?php

namespace App\Http\Services;
// 蔬菜地信息
use App\Models\VegetableLand;

class VegetableLandService extends BaseService
{
    //添加蔬菜地
    static function addVegetableLand($data): int
    {
        return VegetableLand::addVegetableLand($data);
    }

    //获取蔬菜地列表
    static function getVegetableLandList($data = []): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(VegetableLand::getVegetableLandNumsByUId(), $page, $pageSize, VegetableLand::getVegetableLandList($data));

    }

    //删除蔬菜地信息
    static function delVegetableLandById($id, $data = []): int
    {
        return VegetableLand::delVegetableLand($id, $data);
    }
    //根据id查询蔬菜地信息
    static function findVegetableLandInfoById($id, $data = []): array
    {
        return VegetableLand::findVegetableLandInfoById($id, $data);
    }
}
