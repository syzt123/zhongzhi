<?php

namespace App\Http\Services;
// 兑换信息
use App\Models\ExchangeLog;
use Illuminate\Support\Facades\Request;

class ExchangeLogService extends BaseService
{
    //添加兑换
    static function addExchangeLog($data): int
    {
        return ExchangeLog::addExchangeLog($data);
    }

    //获取兑换列表
    static function getExchangeLogList($uid, $data = []): array
    {
        $page = 1;
        $pageSize = 10;
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
        }
        return self::getPageDataList(ExchangeLog::getExchangeLogNumsByUId($uid), $page, $pageSize, ExchangeLog::getExchangeLogList($uid, $data));

    }

    //删除兑换信息
    static function delExchangeLogById($id, $data = []): int
    {
        return ExchangeLog::delExchangeLog($id, $data);
    }


}
