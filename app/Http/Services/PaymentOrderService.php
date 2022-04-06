<?php

namespace App\Http\Services;
// 支付订单
use App\Models\PaymentOrder;

class PaymentOrderService extends BaseService
{
    //添加支付订单
    static function addPaymentOrder($data): int
    {
        return PaymentOrder::addPaymentOrder($data);
    }

    //获取支付订单列表
    static function getPaymentOrderList($uid, $data = []): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(PaymentOrder::getPaymentOrderNumsByUId($uid), $page, $pageSize, PaymentOrder::getPaymentOrderList($uid, $data));

    }

    //删除支付订单信息
    static function delPaymentOrderById($id, $data = []): int
    {
        return PaymentOrder::delPaymentOrder($id, $data);
    }

    // 更新订单状态等信息
    static function updateOrderStatusInfoByOrderId($orderId, $data = []): int
    {
        return PaymentOrder::updatePaymentOrder($orderId, $data);

    }

    // 根据订单ID查询订单信息
    static function getOrderInfoByOrderId($orderId, $data = []): array
    {
        return PaymentOrder::getOrderInfoByOrderId($orderId, $data);

    }
}
