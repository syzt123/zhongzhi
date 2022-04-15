<?php


namespace App\Http\Controllers\Admin\Land;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\VegetableLandService;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index()
    {
        return view('admin.land.index');
    }
    public function data(Request $request)
    {
        $userData = VegetableLandService::getPageDataListByAdmin();
        return $this->success($userData);
    }
}
