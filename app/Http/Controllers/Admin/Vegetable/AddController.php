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
        $userData = VegetableTypeService::addVegetableType($request->post());
        return $this->success($userData);
    }
}
