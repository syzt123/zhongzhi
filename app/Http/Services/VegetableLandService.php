<?php

namespace App\Http\Services;
// 蔬菜地信息
use App\Models\VegetableLand;
use App\Models\Admin\VegetableLand as Admin;
use Illuminate\Support\Facades\Request;

class VegetableLandService extends BaseService
{
    //添加蔬菜地
    static function addVegetableLand($data): int
    {
        return VegetableLand::addVegetableLand($data);
    }

    //获取蔬菜地列表
    static function getVegetableLandList($uid, $data = []): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(VegetableLand::getVegetableLandNumsByUId($uid), $page, $pageSize, VegetableLand::getVegetableLandList($uid, $data));

    }

    //删除蔬菜地信息
    static function delVegetableLandById($id, $data = []): int
    {
        return VegetableLand::delVegetableLand($id, $data);
    }

    // 编辑土地




}
