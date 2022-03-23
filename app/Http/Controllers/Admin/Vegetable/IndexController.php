<?php


namespace App\Http\Controllers\Admin\Vegetable;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\VegetableType;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index()
    {
        return view('admin.vegetable.index');
    }
    public function data(Request $request)
    {
        $userData = VegetableType::paginate($request->limit);
        return $this->success($userData);
    }
}
