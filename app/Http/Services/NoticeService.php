<?php

namespace App\Http\Services;
// 公告信息
use App\Models\MemberInfo;
use App\Models\Notice;

class NoticeService extends BaseService
{
    //添加公告
    static function addNotice($data): int
    {
        return Notice::addNotice($data);
    }

    //获取公告
    static function getNoticeInfo(): array
    {
        return Notice::getNoticeInfo();
    }

    //删除公告信息
    static function delNoticeById($id, $data = []): int
    {
        return Notice::delNotice($id, $data);
    }
}
