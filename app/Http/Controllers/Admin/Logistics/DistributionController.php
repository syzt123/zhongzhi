<?php


namespace App\Http\Controllers\Admin\Logistics;


use App\Http\Controllers\Admin\BaseController;
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
        $userData = DeliveryOrder::leftJoin('member_info', 'm_id', '=', 'member_info.id')
            ->paginate($request->limit, ["delivery_order.*", "member_info.nickname"]);
        return $this->success($userData);
    }
}
