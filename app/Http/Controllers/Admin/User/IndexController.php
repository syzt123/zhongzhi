<?php


namespace App\Http\Controllers\Admin\User;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\MemberInfo;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index()
    {
        return view("admin.user.index");
    }
    public function data(Request $request)
    {
        $userData = MemberInfo::paginate($request->limit);
        return $this->success($userData);
    }
}
