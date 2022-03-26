<?php

namespace App\Http\Services;
// 物流信息
use App\Models\DeliveryOrder;

class DeliveryOrderService extends BaseService
{
    //新增物流
    static function addDeliveryOrder($data): int
    {
        return DeliveryOrder::addDeliveryOrder($data);
    }

    //获取用户物流信息
    static function getDeliveryOrderList($uid, $data=[]): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(DeliveryOrder::getDeliveryOrderNumsByUId($uid), $page, $pageSize, DeliveryOrder::getDeliveryOrderList($uid, $data));

    }

    //删除物流信息
    static function delDeliveryOrder($id, $data=[]): int
    {
        return DeliveryOrder::delDeliveryOrder($id, $data);
    }
}
