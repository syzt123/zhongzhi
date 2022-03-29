<?php

namespace App\Http\Controllers\Api\V1;

use \App\Http\Controllers\Controller;
use App\Http\Services\VegetableTypeService;
use Illuminate\Http\Request;

/**
 * Class LandController
 * @package App\Http\Controllers\Api\V1
 */
//蔬菜类型管理
class VegetableTypeController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/v1/vegetable/typeLists",
     *     tags={"蔬菜种类列表",},
     *     summary="蔬菜种类列表",
     *     description="蔬菜种类列表(2022/03/31开发中)",
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
     *             @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_type", type="string", description="蔬菜的名字"),
     *                      @OA\Property(property="status", type="int", description="蔬菜种类的状态1可种植 2不可种植"),
     *                      @OA\Property(property="v_price", type="int", description="蔬菜认领的价格"),
     *                      @OA\Property(property="f_price", type="int", description="蔬菜币"),
     *                      @OA\Property(property="grow_1", type="int", description="种子时期"),
     *                      @OA\Property(property="grow_2", type="int", description="幼苗时期"),
     *                      @OA\Property(property="grow_3", type="int", description="生长"),
     *                      @OA\Property(property="grow_4", type="int", description="成年"),
     *                      @OA\Property(property="grow_5", type="int", description="成熟"),
     *                      @OA\Property(property="storage_time", type="int", description="可以存放的时间"),
     *                      @OA\Property(property="create_time", type="int", description="创建时间"),
     *                      @OA\Property(property="update_time", type="int", description="更新时间"),
     *                  ),
     *               ),
     *
     *            ),
     *
     *         ),
     *     ),
     * )
     */
    function typeLists(Request $request): array
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
        $lists = VegetableTypeService::getVegetableTypeList($data);

        return $this->backArr('蔬菜种类列表ok', config("comm_code.code.ok"), $lists);

    }

}
