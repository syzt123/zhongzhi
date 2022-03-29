<?php


namespace App\Http\Controllers\Admin\User;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\ExchangeLogService;
use Illuminate\Http\Request;

class ExchangeLogController extends BaseController
{
    public function index()
    {
        return view("admin.user.exchange_log");
    }
    public function data(Request $request)
    {
        $data = ExchangeLogService::getPageDataListByAdmin(
            [
                "table" => "member_info",
                "foreign_key" => "m_id",
                "type" => "left"
            ]
        );
        return $this->success($data);
    }
}
