<?php


namespace App\Http\Controllers\Admin\Vegetable;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\MemberInfoService;
use App\Http\Services\VegetableTypeService;
use App\Models\Admin\VegetableType;
use Illuminate\Http\Request;

class EditController extends BaseController
{
    public function index($id)
    {
        $res = MemberInfoService::delModelByAdmin($id);
        if ($res) {
            return $this->success();
        } else {
            return $this->error('删除失败！');
        }
    }
    public function submit(Request $request)
    {
        $userData = VegetableTypeService::editModelByAdmin();
        return $this->success($userData);
    }
}
