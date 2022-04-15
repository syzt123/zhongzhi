<?php


namespace App\Http\Controllers\Admin\User;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\BuyLogService;
use Illuminate\Http\Request;


class BuyLogController extends BaseController
{
    public function index()
    {
        return view("admin.user.buy_log");
    }

    public function data(Request $request)
    {
        $data = BuyLogService::getPageDataListByAdmin([
            "table" => "member_info",
            "foreign_key" => "m_id",
            "type" => "left"
        ]);
        return $this->success($data);
    }

}
