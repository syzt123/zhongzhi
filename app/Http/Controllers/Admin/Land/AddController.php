<?php


namespace App\Http\Controllers\Admin\Land;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\VegetableLand;
use Illuminate\Http\Request;

class AddController extends BaseController
{
    public function index()
    {
        return view('admin.land.add');
    }
    public function submit(Request $request)
    {
        $model = new VegetableLand();
        $res = $model->creatLand();
        if($res === true){
            return  $this->success();
        }else{
            return $this->error($res);
        }
    }
}
