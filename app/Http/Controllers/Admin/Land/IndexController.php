<?php


namespace App\Http\Controllers\Admin\Land;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\VegetableLand;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index()
    {
        return view('admin.land.index');
    }
    public function data(Request $request)
    {
        $userData = VegetableLand::paginate($request->limit);
        return $this->success($userData);
    }
}
