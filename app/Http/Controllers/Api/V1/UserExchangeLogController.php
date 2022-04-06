<?php

namespace App\Http\Controllers\Api\V1;

use \App\Http\Controllers\Controller;
use App\Http\Services\ExchangeLogService;
use Illuminate\Http\Request;

/**
 * Class ExchangeLogController
 * @package App\Http\Controllers\Api\V1
 */
//兑换记录
class UserExchangeLogController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/v1/user/addExchangeLog",
     *     tags={"用户管理",},
     *     summary="用户新增兑换蔬菜",
     *     description="用户新增兑换蔬菜(2022/03/29日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *     @OA\Parameter(name="f_price", in="query", @OA\Schema(type="int"),description="蔬菜单价"),
     *     @OA\Parameter(name="v_num", in="header", @OA\Schema(type="int"),description="蔬菜数量"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *     ),
     *    )
     * @param Request $request
     * @return array
     */
    function addExchangeLog(Request $request): array
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }

        if (!isset($request->f_price)) {
            return $this->backArr('用户兑换蔬菜单价字段f_price必须', config("comm_code.code.fail"), []);
        }
        if (!isset($request->v_num)) {
            return $this->backArr('用户兑换蔬菜数量字段v_num必须', config("comm_code.code.fail"), []);
        }
        /*if (!isset($request->n_price)){
            return $this->backArr('蔬菜总价字段n_price必须', config("comm_code.code.fail"), []);
        }
        if ($request->n_price != $request->f_price *$request->v_num){
            return $this->backArr('蔬菜总价不等', config("comm_code.code.fail"), []);
        }*/
        $data = [
            "m_id" => $this->getUserInfo($request->header("token"))["id"],
            "f_price" => $request->f_price,
            "v_num" => $request->v_num,
            "n_price" => $request->f_price * $request->v_num,
            "create_time" => time(),
        ];
        $bool = ExchangeLogService::addExchangeLog($data);
        if ($bool) {
            return $this->backArr('兑换成功', config("comm_code.code.ok"), []);
        }
        return $this->backArr('兑换失败', config("comm_code.code.fail"), []);
    }
}
