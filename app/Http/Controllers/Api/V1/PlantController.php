<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Services\MemberInfoService;
use App\Http\Services\MemberVegetableService;
use App\Http\Services\VegetableTypeService;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    /**
     * @OA\Get  (
     *     path="/api/v1/vegetable/seed",
     *     tags={"种植模块",},
     *     summary="可种植的植物",
     *     description="可种植的植物种子,接口将根据是否推荐返回两个数组",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
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
     *                      @OA\Property(property="recommend", type="int", description="是否推荐"),
     *                      @OA\Property(property="mature_rate", type="string", description="成熟周期"),
     *                      @OA\Property(property="exchange_quality", type="int", description="兑换量"),
     *                      @OA\Property(property="vegetable_img", type="string", description="当前时间段的图片"),
     *                  ),
     *               ),
     *
     *            ),
     *
     *         ),
     *     ),
     * )
     */
    public function seed()
    {
        return VegetableTypeService::getSeed();
    }
    /**
     * @OA\Get  (
     *     path="/api/v1/vegetable/planted",
     *     tags={"种植模块",},
     *     summary="已种植的植物",
     *     description="",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *             @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_type", type="string", description="蔬菜的名字"),
     *                      @OA\Property(property="status", type="int", description="蔬菜种类的状态1可种植 2不可种植"),
     *                      @OA\Property(property="v_price", type="int", description="蔬菜认领的价格"),
     *                      @OA\Property(property="f_price", type="int", description="蔬菜币"),
     *                      @OA\Property(property="vegetable_type.grow_1", type="int", description="种子时期"),
     *                      @OA\Property(property="vegetable_type.grow_2", type="int", description="幼苗时期"),
     *                      @OA\Property(property="vegetable_type.grow_3", type="int", description="生长"),
     *                      @OA\Property(property="vegetable_type.grow_4", type="int", description="成年"),
     *                      @OA\Property(property="vegetable_type.grow_5", type="int", description="成熟"),
     *                      @OA\Property(property="vegetable_type.storage_time", type="int", description="可以存放的时间"),
     *                      @OA\Property(property="create_time", type="int", description="创建时间"),
     *                      @OA\Property(property="update_time", type="int", description="更新时间"),
     *                      @OA\Property(property="recommend", type="int", description="是否推荐"),
     *                      @OA\Property(property="mature_rate", type="string", description="成熟周期"),
     *                      @OA\Property(property="exchange_quality", type="int", description="兑换量"),
     *                      @OA\Property(property="vegetable_img", type="string", description="当前时间段的图片"),
     *                  ),
     *               ),
     *
     *            ),
     *
     *         ),
     *     ),
     * )
     */
    public function planted(Request $request)
    {
        $user = $this->getUserInfo($request->header('token'));
        if (!$user) {
            return $this->error('未找到用户信息');
        }else{
            $memberVegetables = MemberVegetableService::getGrowMemberVegetablesByUId($user['id']);// 这颗蔬菜入库操作之后已种植就查不到了(甲方前端要求)
            foreach ($memberVegetables as $memberVegetable)
            {
                $memberVegetable->makeHidden(['vegetable_type'])->toArray();
                $memberVegetable->vegetable_img = $memberVegetable
                    ->vegetableType
                    ->vegetableResources()
                    ->where('vegetable_resources.vegetable_grow', '=', $memberVegetable->vegetable_grow)
                    ->where('vegetable_resources.vegetable_resources_type', '=', 1)
                    ->value('vegetable_resources');
            }
            return $this->success($memberVegetables);
        }

    }
}
