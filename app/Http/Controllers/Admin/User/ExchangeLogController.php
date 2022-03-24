<?php


namespace App\Http\Controllers\Admin\User;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\ExchangeLog;
use Illuminate\Http\Request;

class ExchangeLogController extends BaseController
{
    public function index()
    {
        return view("admin.user.exchange_log");
    }
    public function data(Request $request)
    {
        $data = ExchangeLog::leftJoin('member_info','m_id','=','member_info.id')
            ->paginate($request->limit,["exchange_log.*","member_info.nickname"]);
        return $this->success($data);
    }
}
