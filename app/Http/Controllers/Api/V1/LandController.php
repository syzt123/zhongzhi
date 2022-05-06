<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Ys\YsController;
use \App\Http\Controllers\Controller;
use App\Http\Services\VegetableLandService;
use Illuminate\Http\Request;

/**
 * Class LandController
 * @package App\Http\Controllers\Api\V1
 */
//土地管理
class LandController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/v1/land/lists",
     *     tags={"土地管理",},
     *     summary="获取土地列表",
     *     description="获取土地列表(2022/04/29已完成)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="int"),description="当前页 默认1"),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="int"),description="每页显示条数 默认10"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                @OA\Property(property="page", type="array", description="分页信息",
     *                  @OA\Items(
     *                      @OA\Property(property="page", type="int", description="当前页"),
     *                      @OA\Property(property="page_size", type="int", description="每页大小"),
     *                      @OA\Property(property="count", type="int", description="总条数"),
     *                      @OA\Property(property="total_page", type="int", description="总页数"),
     *                  ),
     *               ),
     *
     *                @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="monitor", type="string", description="土地监控地址 已废弃"),
     *                      @OA\Property(property="v_num", type="int", description="可以种植蔬菜数量"),
     *                      @OA\Property(property="tx_video_url", type="string", description="腾讯云点播url地址 2022/04/26新增"),
     *                      @OA\Property(property="tx_video_id", type="string", description="腾讯云点播唯一id 2022/04/26新增"),
     *                      @OA\Property(property="create_time", type="int", description="创建时间戳"),
     *                  ),
     *               ),
     *
     *            ),
     *
     *         ),
     *    *     ),
     * )
     */
    function landLists(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }
        $data = [];
        if (isset($request->page) && (int)$request->page > 0) {
            $data["page"] = $request->page;
        }
        if (isset($request->page_size) && (int)$request->page_size > 0) {
            $data["page_size"] = $request->page_size;
        }
        $lists = VegetableLandService::getVegetableLandList($data);

        return $this->backArr('土地列表ok', config("comm_code.code.ok"), $lists);

    }

    // 根据订单号更新订单状态等
    function updateOrderStatus(Request $request): array
    {

    }

    /**
     * @OA\Get (
     *     path="/api/v1/land/detail/{land_id}",
     *     tags={"土地管理",},
     *     summary="根据土地id获取土地详情",
     *     description="获取土地列表(2022/05/07已完成)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="land_id", in="query", @OA\Schema(type="int"),description="土地land_id 必须"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="int", description="主键id"),
     *                 @OA\Property(property="monitor", type="string", description="土地监控地址 已废弃"),
     *                 @OA\Property(property="v_num", type="int", description="可以种植蔬菜数量"),
     *                 @OA\Property(property="tx_video_url", type="string", description="腾讯云点播url地址 2022/04/26新增"),
     *                 @OA\Property(property="tx_video_id", type="string", description="腾讯云点播唯一id 2022/04/26新增"),
     *                 @OA\Property(property="create_time", type="int", description="创建时间戳"),
     *             ),
     *         ),
     *     ),
     * )
     */
    function getDetailByLandId(Request $request): array
    {
        if (!isset($request->land_id)) {
            return $this->backArr('land_id必须', config("comm_code.code.fail"), []);
        }
        $info = VegetableLandService::findVegetableLandInfoById($request->land_id);
        if (count($info) == 0) {
            return $this->backArr('土地详情未查询到，请重试！', config("comm_code.code.fail"), []);
        }
        return $this->backArr('土地详情ok', config("comm_code.code.ok"), $info);

    }
}
