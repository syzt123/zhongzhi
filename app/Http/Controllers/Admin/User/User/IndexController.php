<?php


namespace App\Http\Controllers\Admin\User\User;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\MemberInfoService;
use App\Http\Services\VegetableLandService;
use App\Models\Admin\MemberInfo;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index()
    {
        return view("admin.user.user.index");
    }
    public function data(Request $request)
    {
        $userData = MemberInfoService::getPageDataListByAdmin();
        return $this->success($userData);
    }
}
