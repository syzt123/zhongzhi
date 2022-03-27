<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Ys\YsController;
use \App\Http\Controllers\Controller;
use App\Http\Services\NoticeService;
use Illuminate\Http\Request;

/**
 * Class NoticeController
 * @package App\Http\Controllers\Api\V1
 */
//公告管理
class NoticeController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/v1/notice/info",
     *     tags={"系统公告",},
     *     summary="系统公告信息",
     *     description="系统公告信息",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Response(response=200, description="  {code: 200, msg:string, data:[//dddd /// ttt]}  "),
     *    )
     */
    function info(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }
        var_dump(YsController::getLiveAddress());
        $info = NoticeService::getNoticeInfo();
        return $this->backArr('系统公告', config("comm_code.code.ok"), $info);
    }
}
