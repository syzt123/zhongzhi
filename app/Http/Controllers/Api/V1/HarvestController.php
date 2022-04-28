<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Services\DeliveryOrderService;
use App\Http\Services\MemberInfoService;
use App\Http\Services\MemberVegetableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class HarvestController extends Controller
{
    /**
     * @OA\Post  (
     *     path="/api/v1/harvest/warehousing",
     *     tags={"收获模块",},
     *     summary="入库",
     *     description="作物成熟后入库",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="vegetable_id", in="query", @OA\Schema(type="string"),description="需要入库的蔬菜id" ),
     *     @OA\Response(
     *         response=400,
     *         description="{code: 0, msg:您的蔬菜未在成熟期无法入库！ || 该蔬菜可能不是您的哦, data:[]}",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 1, msg:您的蔬菜已成功入库，将在xx天后坏掉，请及时处理！, data:[]}",
     *     ),
     * )
     */
    public function warehousing(Request $request)
    {
        $user = $this->getUserInfo($request->header('token'));
        $memberVegetable = MemberVegetableService::memberVegetableStatus($user['id'], $request->vegetable_id);

        if (!$memberVegetable) {
            return $this->error('该蔬菜可能不是您的哦');
        } elseif ($memberVegetable->vegetable_grow !== 3) {
            return $this->error('您的蔬菜未在成熟期无法入库！');
        } elseif ($memberVegetable->vegetable_grow <= 0) {
            $memberVegetable->v_status = 3;
            $memberVegetable->save();
            return $this->error('您的蔬菜已坏掉无法入库！');
        } else {
            $memberVegetable->vegetable_grow = 0;
            $memberVegetable->v_status = 2;
            $res = $memberVegetable->save();
            if ($res) {
                return $this->success([], "您的蔬菜已成功入库，将在{$memberVegetable->vegetableType->storage_time}天后坏掉，请及时处理！");
            } else {
                return $this->error('入库失败！');
            }
        }
    }

    /**
     * @OA\Post  (
     *     path="/api/v1/harvest/distribution",
     *     tags={"收获模块",},
     *     summary="配送",
     *     description="作物成熟后配送",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="vegetable_id", in="query", @OA\Schema(type="string"),description="需要入库的蔬菜id" ),
     *     @OA\Response(
     *         response=400,
     *         description="{code: 0, msg:请完善您的收货地址！ || 提交失败 || 该蔬菜可能不是您的哦  || 蔬菜产量不足无法配送, data:[]}",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 1, msg:您的配送需求已提交，将在48小时内为你送达！, data:[]}",
     *     ),
     * )
     */
    public function distribution(Request $request)
    {
        $user = $this->getUserInfo($request->header('token'));

        $memberVegetable = MemberVegetableService::memberVegetableStatus($user['id'], $request->vegetable_id);
        $memberInfo = $memberVegetable->memberInfo;
        if (!$memberVegetable) {
            return $this->error('该蔬菜可能不是您的哦');
        } elseif (!$memberInfo->v_address) {
            return $this->error('请完善您的收货地址！');
        } else {
            if ($memberVegetable->yield == 0) {
                return $this->error('蔬菜产量不足无法配送！');
            }
            $data = [
                'm_id' => $memberInfo->id,
                'm_v_id' => $request->vegetable_id,
                'f_price' => 0,
                'r_id' => 1,
                'status' => 1,
                'order_id' => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT) . $memberInfo->id,
                'payment_order_id' => 0,
                'des_address' => $memberInfo->v_address,
                'create_time' => time(),
                'update_time' => time()
            ];
            $res = DeliveryOrderService::addDeliveryOrder($data);
            if ($res) {
                $memberVegetable->yield = 0;
                $memberVegetable->save();
                return $this->success([], '您的配送需求已提交，将在48小时内为你送达！');
            } else {
                return $this->error('提交失败');
            }
        }
    }
}
