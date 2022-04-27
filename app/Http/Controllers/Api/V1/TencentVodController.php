<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Ys\YsController;
use \App\Http\Controllers\Controller;
use App\Http\Services\NoticeService;
use App\Http\Services\TencentVod\VodManagerService;
use Illuminate\Http\Request;

/**
 * Class IndexController
 * @package App\Http\Controllers\Api\V1
 */
//点播管理
class TencentVodController extends Controller
{

    function getSign(Request $request): array
    {
        $info = VodManagerService::getSign();
        return $this->backArr('系统公告', config("comm_code.code.ok"), ["sign"=>$info]);
    }
}
