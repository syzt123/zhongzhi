<?php


namespace App\Http\Controllers\Admin\User;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\BuyLog;
use Illuminate\Http\Request;


class BuyLogController extends BaseController
{
    public function index()
    {
        return view("admin.user.buy_log");
    }

    public function data(Request $request)
    {
        $data = BuyLog::leftJoin('member_info','m_id','=','member_info.id')
            ->paginate($request->limit,["buy_log.*","member_info.nickname"]);
        return $this->success($data);
    }

}
