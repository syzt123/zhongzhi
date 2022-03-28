<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Ys\YsController;
use \App\Http\Controllers\Controller;
use App\Http\Services\NoticeService;
use Illuminate\Http\Request;

/**
 * Class IndexController
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
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[ //dddd /// ttt]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(property="id", type="integer", description="系统公告Id"),
     *                @OA\Property(property="notice", type="string", description="公告内容"),
     *                @OA\Property(property="create_time", type="string", description="公告时间"),
     *             ),
     *         )
     *     ),
     *    )
     * @OA\Schema(
     *   schema="TestApi",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="id", type="integer", description="用户ID"),
     *       @OA\Property(property="notice", type="string", description="email"),
     *       @OA\Property(property="create_time", type="string", description="email"),
     *     )
     *   }
     * )
     */
    function info(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }
        // 测试直播地址
        // var_dump(YsController::getLiveAddress());
        $info = NoticeService::getNoticeInfo();
        return $this->backArr('系统公告', config("comm_code.code.ok"), $info);
    }
}
