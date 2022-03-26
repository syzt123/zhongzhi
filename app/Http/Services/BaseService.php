<?php


namespace App\Http\Services;


class BaseService
{
    // 获取分页数据
    /**
     * @param int $count 总条数
     * @param int $page 当前页
     * @param int $pageSize 当前数量
     * @return array
     */
    static function getPageDataList($count = 0, $page = 1, $pageSize = 10, $data = []): array
    {
        return [
            "page" => ["count" => $count,
                "page" => $page,
                "page_size" => $pageSize,
                "total_page" => ceil($count / $pageSize)],
            "list" => $data,
        ];
    }
}
