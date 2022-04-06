<?php


namespace App\Http\Controllers\Admin\Vegetable;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\VegetableTypeService;
use App\Models\Admin\VegetableType;
use Illuminate\Http\Request;

class AddController extends BaseController
{
    public function index()
    {
        return view('admin.vegetable.add');
    }

    public function submit(Request $request)
    {
        $res = VegetableTypeService::addVegetableType($request->post());
        if ($res === true) {
            return $this->success('', '添加成功');
        } else {
            return $this->error($res);
        }

    }

    public function upResources(Request $request)
    {
        return $this->success($this->upload());
    }
}
