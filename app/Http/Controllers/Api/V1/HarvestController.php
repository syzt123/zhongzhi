<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Services\DeliveryOrderService;
use App\Http\Services\MemberVegetableService;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    public function warehousing(Request $request)
    {
        $memberVegetable = MemberVegetableService::memberVegetableStatus($request->uid, $request->vegetable_id);
        if ($memberVegetable->vegetable_grow !== 5) {
            return $this->error('您的蔬菜未在成熟期无法入库！');
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

    public function distribution(Request $request)
    {
        $memberVegetable = MemberVegetableService::memberVegetableStatus($request->uid, $request->vegetable_id);
        $memberInfo = $memberVegetable->memberInfo;
        if (!$memberInfo->v_address) {
            return $this->error('请完善您的收货地址！');
        } else {
            $data = [
                'm_id' => $memberInfo->id,
                'f_price' => 0,
                'r_id'=>1,
                'status' => 1,
                'order_id' => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT).$memberInfo->id,
                'payment_order_id' => 0,
            ];
            $res = DeliveryOrderService::addDeliveryOrder($data);
            if($res){
                return $this->success([],'您的配送需求已提交，将在48小时内为你送达！');
            }else{
                return $this->error('提交失败');
            }
        }
    }
}
