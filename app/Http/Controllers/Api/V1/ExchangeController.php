<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Services\DeliveryOrderService;
use App\Http\Services\MemberInfoService;
use App\Models\ExchangeLog;
use App\Models\MemberInfo;
use App\Models\MemberVegetable;
use App\Models\VegetableType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Sodium\compare;
use Illuminate\Support\Facades\Redis;

class ExchangeController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/v1/exchange/vegetable",
     *     tags={"蔬菜兑换模块",},
     *     summary="兑换蔬菜",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="vegetable_id",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="exchange_num",
     *                     type="int"
     *                 ),
     *                 example={"vegetable_id": 1, "exchange_num": 100}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="success", value={"data": "","message":"您的配送需求已提交，将在48小时内为你送达！","code":1}, summary="操作成功"),
     *         )
     *     ),
     *    @OA\Response(
     *         response=400,
     *         description="发生错误",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="error1", value={"data": "","message":"该蔬菜尚未成熟，无法兑换！","code":1}, summary="未成熟"),
     *             @OA\Examples(example="error2", value={"data": "","message":"您种植了该蔬菜并已成熟无需兑换!","code":0}, summary="已种植"),
     *             @OA\Examples(example="error3", value={"data": "","message":"该蔬菜总量不足！","code":0}, summary="蔬菜量不足"),
     *             @OA\Examples(example="error4", value={"data": "","message":"蔬菜币不足！！","code":0}, summary="蔬菜币量不足"),
     *             @OA\Examples(example="error5", value={"data": "","message":"请填写收货地址！","code":0}, summary="无收货地址"),
     *             @OA\Examples(example="error6", value={"data": "","message":"未找到用户信息！","code":0}, summary="无用户"),
     *         )
     *     )
     * )
     */
    public function vegetable(Request $request)
    {
        $user = $this->getUserInfo($request->header('token'));
        if (!$user) {
            return $this->error('未找到用户信息');
        } elseif ($memberVegetable = MemberVegetable::where('m_id', '=', $user['id'])
            ->where('v_type', '=', $request->vegetable_id)
            ->where('vegetable_grow', '=', 5)
            ->where('v_status', '=', 2)
            ->first()) {
            return $this->error('您种植了该蔬菜并已成熟无需兑换 ');

        } else {
            $memberVegetables = MemberVegetable::where('v_type', '=', $request->vegetable_id)
                ->where('vegetable_grow', '=', 5)
                ->where('v_status', '=', 2)
                ->first();
            if (!$memberVegetables) {
                return $this->error('该蔬菜尚未成熟，无法兑换！');
            } else {
                $memberInfo = MemberInfo::find($user['id']);
                $vegetablesTotal = MemberVegetable::where('v_type', '=', $request->vegetable_id)
                    ->where('vegetable_grow', '=', 5)
                    ->where('v_status', '=', 2)
                    ->sum('yield');

                if ($vegetablesTotal < $request->exchange_num) {
                    return $this->error('该蔬菜总量不足！');
                } else {
                    $totalPrice = bcmul($request->exchange_num, $memberVegetables->f_price, 2);

                    if ($memberInfo->gold < $totalPrice) {
                        return $this->error('蔬菜币不足！');
                    } else {
                        if (!$memberInfo->v_address) {
                            return $this->error('请填写收货地址');
                        } else {
                            try {
                                $exchangeRes = $this->exchangeVegetable($request->exchange_num, $user['id'], $request->vegetable_id);
                                if ($exchangeRes) {
                                    $data = [
                                        'm_id' => (int)$memberInfo->id,
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
                                        return $this->success([], '您的配送需求已提交，将在48小时内为你送达！');
                                    }
                                }
                            } catch (\Exception $exception) {
                                return $this->error('提交失败' . $exception->getMessage());
                            }
                        };
                    }
                }
            }
        }
    }


    private function exchangeVegetable($exchange_num, $uid, $vegetable_id)
    {
        try {
            if ($exchange_num > 0) {
                DB::beginTransaction();
                $exchangeVegetable = MemberVegetable::lockForUpdate()
                    ->where('vegetable_type_id', '=', $vegetable_id)
                    ->where('vegetable_grow', '=', 5)
                    ->where('v_status', '=', 2)
                    ->where('yield', '>', 0)
                    ->orderBy('planting_time')
                    ->first();
                if ($exchangeVegetable->yield > $exchange_num) {
                    $exchangeVegetable->yield -= $exchange_num;
                    $exchangeVegetableMember = $exchangeVegetable->memberInfo()->lockForUpdate()->first();
                    $exchangeVegetableMember->gold += $exchange_num;
                    $member = MemberInfo::lockForUpdate()->where('id', '=', $uid)->first();
                    $member->gold -= $exchange_num;
                    $exchange_num -= $exchangeVegetable->yield;
                } else {
                    $exchangeVegetableMember = $exchangeVegetable->memberInfo()->lockForUpdate()->first();
                    $exchangeVegetableMember->gold += $exchangeVegetable->yield;
                    $member = MemberInfo::lockForUpdate()->where('id', '=', $uid)->first();
                    $member->gold -= $exchangeVegetable->yield;
                    $exchange_num -= $exchangeVegetable->yield;
                    $exchangeVegetable->yield = 0;
                }
                $member->save();
                $exchangeVegetableMember->save();
                $exchangeVegetable->save();
                $this->exchangeVegetable($exchange_num, $uid, $vegetable_id);
                DB::commit();
                return true;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }
        return false;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/exchange/coin",
     *     tags={"蔬菜兑换模块",},
     *     summary="蔬菜兑换蔬菜币",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="vegetable_id",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="id",
     *                     type="int",
     *                     description="用户蔬菜id 必须",
     *                 ),
     *                 example={"vegetable_id": 1, "id":1}
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="success", value={"data": "","message":"兑换成功","code":1}, summary="操作成功"),
     *         )
     *     ),
     *    @OA\Response(
     *         response=400,
     *         description="发生错误",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="error1", value={"data": "","message":"未找到您的蔬菜或您的蔬菜已过期！","code":1}, summary="找不到"),
     *             @OA\Examples(example="error3", value={"data": "","message":"该蔬菜总量不足！","code":0}, summary="量不足"),
     *             @OA\Examples(example="error4", value={"data": "","message":"兑换失败！","code":0}, summary="失败"),
     *             @OA\Examples(example="error6", value={"data": "","message":"未找到用户信息！","code":0}, summary="无用户"),
     *         )
     *     )
     * )
     */
    public function vegetableToCoin(Request $request)
    {
        $user = $this->getUserInfo($request->header('token'));
        if (!$user) {
            return $this->error('未找到用户信息');
        } else {
            $user = MemberInfo::find($user['id']);
            $memberVegetable = MemberVegetable::where('vegetable_type_id', '=', $request->vegetable_id)
                ->where('id', '=', $request->id)
                ->where('m_id', '=', $user['id'])
                ->where('nums', '>', 0)
                ->where('vegetable_grow', '=', 0)
                ->where('v_status', '=', 2)
                ->first();
            if (!$memberVegetable) {
                return $this->error('未找到您的蔬菜或您的蔬菜已过期！');
            } else {
                if ($memberVegetable->yield <= 0) {
                    return $this->error('该蔬菜剩余量不足！');
                }
                $gold = bcmul($memberVegetable->f_price, $memberVegetable->nums, 2);
                $log = ExchangeLog::create([
                    'm_id' => $user['id'],
                    'm_v_id' => $request->vegetable_id,
                    'f_price' => $memberVegetable->f_price,
                    'v_num' => $memberVegetable->nums,
                    'n_price' => $gold,
                    'create_time' => time()
                ]);
                if ($log) {
                    $memberVegetable->nums = 0; //数量归0
                    $memberVegetable->save();
                    $user->gold += (int)$gold;

                }
                $res = $user->save();
                if ($res) {
                    $user->refresh();
                    // 更新缓存
                    $tmpData = $user->toArray();
                    $tmpRsJson = json_encode($tmpData);
                    // 保存更新缓存
                    $days = 15 * 24 * 60 * 60;
                    Redis::setex(config("comm_code.redis_prefix.token") . $request->header("token"), $days, $tmpRsJson);
                    return $this->success($res, '兑换成功！');
                } else {
                    return $this->error('兑换失败！');
                }
            }
        }
    }
}
