<?php


namespace App\Http\Controllers\Admin\User\User;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\MemberInfoService;

class EditMemberInfoController extends BaseController
{
    public function index($id)
    {
        $memberInfo = MemberInfoService::getUserInfo($id);

        return view('admin.user.user.edit',compact('memberInfo'));
    }
    public function submit()
    {
        return$res = MemberInfoService::editModelByAdmin();
        if($res === true){
            return  $this->success();
        }else{
            return $this->error($res);
        }
    }
}
