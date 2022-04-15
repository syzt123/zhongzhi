<?php


namespace App\Http\Controllers\Admin\System\Notice;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\NoticeService;
use App\Models\Admin\Notice;
use Illuminate\Http\Request;

class AddController extends BaseController
{
    public function index()
    {
        return view('admin.system.notice.add');
    }
    public function submit(Request $request)
    {
        $data = $request->post();
        $data['create_time'] = time();
        $data = NoticeService::addNotice($data);
        return $this->success($data);
    }
}
