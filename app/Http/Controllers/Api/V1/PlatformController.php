<?php

namespace App\Http\Controllers\Api\V1;

use \App\Http\Controllers\Controller;
use App\Http\Services\BuyLogService;
use App\Http\Services\DeliveryOrderService;
use App\Http\Services\ExchangeLogService;
use App\Http\Services\MemberInfoService;
use App\Http\Services\MemberVegetableService;
use App\Http\Services\PaymentOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Psr\SimpleCache\InvalidArgumentException;


//平台数据管理管理
class PlatformController extends Controller
{
    /**
     *
     * @OA\Post (
     *     path="/api/v1/platform/vegetableList",
     *     tags={"平台数据管理"},
     *     summary="平台已成熟入库蔬菜列表（不含分类）",
     *     description="平台已收货蔬菜列表(2022/04/28日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Parameter(name="class_id", in="query", @OA\Schema(type="string"),description="分类id 非必须字段"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="page_size",
     *                     type="int",
     *                     description="每页数据量 默认10条",
     *                 ),
     *                 @OA\Property(
     *                     property="page",
     *                     type="int",
     *                     description="第几页 默认第1页",
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="int",
     *                     description="1：生长中 2.仓库中 3已坏掉 4:已完成送货 默认全部数据",
     *                 ),
     *                 example={"page_size": 15, "page": 1}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                @OA\Property(property="page", type="array", description="分页信息",
     *                   @OA\Items(
     *                      @OA\Property(property="page", type="int", description="当前页"),
     *                      @OA\Property(property="page_size", type="int", description="每页大小"),
     *                      @OA\Property(property="count", type="int", description="总条数"),
     *                      @OA\Property(property="total_page", type="int", description="总页数"),
     *                  ),
     *               ),
     *
     *               @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_monitor", type="string", description="监控地址"),
     *                      @OA\Property(property="v_price", type="int", description="蔬菜认领的价格"),
     *                      @OA\Property(property="f_price", type="int", description="蔬菜币"),
     *                      @OA\Property(property="yield", type="int", description="产量"),
     *                      @OA\Property(property="vegetable_grow", type="int", description="生长状况"),
     *                      @OA\Property(property="planting_time", type="int", description="用户领取种子的时间"),
     *                      @OA\Property(property="v_status", type="int", description="1：生长中\r\n2：仓库中\r\n3：已坏掉 4:已完成送货"),
     *                      @OA\Property(property="grow_5", type="int", description="成熟"),
     *                      @OA\Property(property="storage_time", type="int", description="可以存放的时间"),
     *                      @OA\Property(property="create_time", type="int", description="创建时间"),
     *                      @OA\Property(property="update_time", type="int", description="更新时间"),
     *                      @OA\Property(property="vegetable_type", type="array", description="蔬菜名信息",
     *                          @OA\Items(
     *                              @OA\Property(property="id", type="int", description="主键id"),
     *                              @OA\Property(property="v_type", type="int", description="蔬菜名字"),
     *                          ),
     *                      ),
     *                  ),
     *               ),
     *            ),
     *
     *         ),
     *     ),
     * )
     */
    function vegetableList(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }
        $userInfo = $this->getUserInfo($request->header("token"));
        $data = [];

        if (isset($request->page_size) && (int)$request->page_size > 0) {
            $data["page_size"] = $request->page_size;
        }
        if (isset($request->class_id)&&(int)$request->class_id > 0) {
            $data["vegetable_type_id"] = (int)$request->class_id;
        }

        $data["vegetable_grow"] = 1;//已入库 不含分类
        $data["v_status"] = 2;//已入库 不含分类
        $lists = MemberVegetableService::getMemberVegetableList(null, $data);
        return $this->backArr('获取列表成功', config("comm_code.code.ok"), $lists);
    }
}
