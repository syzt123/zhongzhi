<?php

namespace App\Http\Services;
// 购买记录
use App\Models\BuyLog;
use Illuminate\Support\Facades\Request;

class BuyLogService extends BaseService
{
    //新增
    static function addUserBuyLog($data): int
    {
        return BuyLog::addUserBuyLog($data);
    }

    //根据用户id查询
    static function getUserBuyLog($uId, $data = []): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(BuyLog::getBuyLogNumsByUId($uId), $page, $pageSize, BuyLog::getUserBuyLogList($uId, $data));
    }

    //删除用户的购买记录
    static function delUserBuyLog($id, $data = []): int
    {
        return BuyLog::delUserBuyLog($id, $data);
    }

}
