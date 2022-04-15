<?php


namespace App\Http\Controllers\Admin\User\User;


use App\Http\Controllers\Admin\BaseController;
use App\Http\Services\MemberInfoService;

class DeleteMemberInfoController extends BaseController
{
    public function index($id)
    {
        $res = MemberInfoService::delModelByAdmin($id);
        if ($res) {
            return $this->success();
        } else {
            return $this->error('删除失败！');
        }

    }
}
