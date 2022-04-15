<?php


namespace App\Http\Controllers\Admin\Logistics;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\DeliveryOrderService;
use App\Models\Admin\DeliveryOrder;
use App\Models\Admin\PaymentOrder;
use Illuminate\Http\Request;

class DistributionController extends BaseController
{
    public function index()
    {
        return view("admin.logistics.distribution");
    }

    public function data(Request $request)
    {
        $data = DeliveryOrderService::getPageDataListByAdmin([
            "table" => "member_info",
            "foreign_key" => "m_id",
            "field"=>["delivery_order.*","member_info.nickname"],
            "type" => "left"
        ]);


        return $this->success($data);
    }
}
