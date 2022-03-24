<?php


namespace App\Http\Controllers\Admin\User\user;


use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\MemberInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AddController extends BaseController
{
    public function index(){
        return view('admin.user.user.add');
    }

    public function submit(Request $request)
    {

        $model = new MemberInfo();
        $res = $model->creatLand();
        if($res === true){
            return  $this->success();
        }else{
            return $this->error($res);
        }
    }
}
