<?php


namespace App\Http\Controllers\Admin\Land;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\VegetableLand;
use Illuminate\Http\Request;

class EditController extends BaseController
{
    public function index(Request $request, $id)
    {
        $land = VegetableLand::find($id);
        return view('admin.land.edit',compact('land'));
    }
    public function submit(Request $request)
    {
        $model = new VegetableLand();
        $res = $model->editLand();
        if($res === true){
            return  $this->success();
        }else{
            return $this->error($res);
        }
    }
}
