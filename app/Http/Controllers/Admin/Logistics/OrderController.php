<?php


namespace App\Http\Controllers\Admin\Logistics;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\PaymentOrderService;
use App\Models\Admin\PaymentOrder;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function index()
    {
        return view("admin.logistics.order");
    }

    public function data(Request $request)
    {
        $data = PaymentOrderService::getPageDataListByAdmin([
            "table" => "member_info",
            "foreign_key" => "m_id",
            "field"=>["payment_order.*","member_info.nickname"],
            "type" => "left"
        ]);
        return $this->success($data);
    }
}
