<?php


namespace App\Http\Controllers\Admin\System;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\Notice;
use Illuminate\Http\Request;

class NoticeController extends BaseController
{
    public function index()
    {
        return view('admin.system.notice');
    }
    public function data(Request $request)
    {
        $userData = Notice::paginate($request->limit);
        return $this->success($userData);
    }
}
