<?php


namespace App\Http\Controllers\Admin\Vegetable;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\VegetableTypeService;
use Illuminate\Http\Request;

class EditController extends BaseController
{
    public function index($id)
    {
        $vegetableType = VegetableTypeService::getModelInfoById($id);
        return view('admin.vegetable.edit',compact('vegetableType'));
    }
    public function submit(Request $request)
    {
        $userData = VegetableTypeService::editModelByAdmin();
        return $this->success($userData);
    }
}
