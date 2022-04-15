<?php


namespace App\Http\Controllers\Admin\System\Notice;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\NoticeService;
use App\Models\Admin\Notice;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index()
    {
        return view('admin.system.notice');
    }
    public function data(Request $request)
    {
        $data = NoticeService::getPageDataListByAdmin();
        return $this->success($data);
    }
}
