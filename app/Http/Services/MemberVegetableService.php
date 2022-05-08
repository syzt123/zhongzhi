<?php

namespace App\Http\Services;
// 用户蔬菜表
use App\Models\MemberVegetable;
use App\Models\VegetableKinds;

class MemberVegetableService extends BaseService
{
    //添加蔬菜
    static function addMemberVegetable($data): int
    {
        return MemberVegetable::addMemberVegetable($data);
    }

    //获取蔬菜分类列表
    static function getMemberVegetableClassList($data): array
    {
        //$rs = VegetableKinds::getVegetableTypeListByUserId($uid);
        //获取每个分类下的分页数据
        //return $rs;
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(VegetableKinds::getVegetableKindsNums($data), $page, $pageSize, VegetableKinds::getVegetableTypeList($data));

    }

    //获取蔬菜分类详情
    static function getMemberVegetableClassInfoBuId($id): array
    {
        return VegetableKinds::findVegetableKindsInfoById($id);

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
        return self::getPageDataList(MemberVegetable::getMemberVegetableNumsByUId($uid, $data), $page, $pageSize, MemberVegetable::getMemberVegetableList($uid, $data));
    }

    //删除蔬菜信息
    static function delMemberVegetableById($id, $data = []): int
    {
        return MemberVegetable::delMemberVegetable($id, $data);
    }

    // 用户蔬菜状态
    static function memberVegetableStatus($uId, $vegetable_id)
    {
        return MemberVegetable::getMemberVegetableByUId2($uId, $vegetable_id);
    }

    // 获取用户的蔬菜
    static function getMemberVegetables($uId)
    {
        return MemberVegetable::getMemberVegetablesByUId($uId);
    }

    static function getGrowMemberVegetablesByUId($uid)
    {
        return MemberVegetable::getGrowMemberVegetablesByUId($uid);
    }


    // 更新
    static function updateMemberVegetable($id, $data = []): int
    {
        return MemberVegetable::updateMemberVegetableById($id, $data);

    }

    /**
     * 更新数量
     * @param $id
     * @param $uId
     * @param int $nums 减少的数 默认减一
     * @param int $yieldNum
     * @return int
     */
    static function updateNumsMemberVegetable($id, $uId, $nums = 1, $yieldNum = 0): int
    {
        return MemberVegetable::updateNumsMemberVegetableById($id, $uId, $nums, $yieldNum);

    }

    //更新当种子存在则更新数量
    static function addMemberVegetableNums($data = [], $nums = 0): int
    {
        return MemberVegetable::addMemberVegetableNums($data, $nums);
    }
}
