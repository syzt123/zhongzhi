<?php


namespace App\Http\Controllers\Admin\Logistics;


use App\Http\Controllers\Admin\BaseController;
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
        $userData = PaymentOrder::leftJoin('member_info','m_id','=','member_info.id')
            ->paginate($request->limit,["payment_order.*","member_info.nickname"]);
        return $this->success($userData);
    }
}
