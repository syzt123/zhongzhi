<?php


namespace App\Http\Controllers\Admin\Logistics;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\DeliveryOrderService;
use App\Models\Admin\DeliveryOrder;
use App\Models\Admin\PaymentOrder;
use Illuminate\Http\Request;

class EditDistributionController extends BaseController
{
    public function index()
    {
        $res = DeliveryOrderService::editModelByAdmin();
        if($res===true){
            return $this->success();
        }else{
            return $this->error($res);
        }
    }

}
