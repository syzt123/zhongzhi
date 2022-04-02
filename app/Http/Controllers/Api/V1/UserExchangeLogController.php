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
//    /**
//     * @OA\Post (
//     *     path="/api/v1/user/addExchangeLog",
//     *     tags={"兑换管理",},
//     *     summary="用户新增兑换蔬菜",
//     *     description="用户新增兑换蔬菜(2022/03/29日完)",
//     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
//     *     @OA\Parameter(name="f_price", in="query", @OA\Schema(type="int"),description="蔬菜单价"),
//     *     @OA\Parameter(name="v_num", in="header", @OA\Schema(type="int"),description="蔬菜数量"),
//     *     @OA\Response(
//     *         response=200,
//     *         description="{code: 200, msg:string, data:[]}",
//     *     ),
//     *    )
//     * @param Request $request
//     * @return array
//     */
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
        // todo 扣除用户蔬菜币

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

    /**
     * @OA\Post (
     *     path="/api/v1/user/userExchangeLogList",
     *     tags={"兑换管理"},
     *     summary="用户个人兑换列表",
     *     description="用户个人兑换列表列表/(2022/04/05日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="int"),description="当前页 默认1"),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="int"),description="每页显示条数 默认10"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="int"),description="1：生长中 2.仓库中 3已坏掉 4:已完成送货 默认全部数据"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                @OA\Property(property="page", type="array", description="分页信息",
     *                   @OA\Items(
     *                      @OA\Property(property="page", type="int", description="当前页"),
     *                      @OA\Property(property="page_size", type="int", description="每页大小"),
     *                      @OA\Property(property="count", type="int", description="总条数"),
     *                      @OA\Property(property="total_page", type="int", description="总页数"),
     *                  ),
     *               ),
     *
     *               @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_type", type="string", description="蔬菜的名字"),
     *                      @OA\Property(property="status", type="int", description="蔬菜种类的状态1可种植 2不可种植"),
     *                      @OA\Property(property="v_price", type="int", description="蔬菜认领的价格"),
     *                      @OA\Property(property="f_price", type="int", description="蔬菜币"),
     *                      @OA\Property(property="grow_1", type="int", description="种子时期"),
     *                      @OA\Property(property="grow_2", type="int", description="幼苗时期"),
     *                      @OA\Property(property="grow_3", type="int", description="生长"),
     *                      @OA\Property(property="grow_4", type="int", description="成年"),
     *                      @OA\Property(property="grow_5", type="int", description="成熟"),
     *                      @OA\Property(property="storage_time", type="int", description="可以存放的时间"),
     *                      @OA\Property(property="create_time", type="int", description="创建时间"),
     *                      @OA\Property(property="update_time", type="int", description="更新时间"),
     *                  ),
     *               ),
     *            ),
     *
     *         ),
     *     ),
     * )
     */
    function userExchangeLogList(Request $request)
    {
        if (!$request->isMethod('post')) {
            return $this->backArr('请求方式必须为post', config("comm_code.code.fail"), []);
        }
        $page = isset($request->page) ? (int)$request->page : 1;
        $pageSize = isset($request->page_size) ? (int)$request->page_size : 10;
        $userInfo = $this->getUserInfo($request->header("token"));
        $exchangeList = ExchangeLogService::getExchangeLogList($userInfo["id"], [
            "page" => $page,
            "page_size" => $pageSize,
        ]);

        return $this->backArr("获取用户兑换列表ok", config("comm_code.code.ok"), $exchangeList);
    }


    /**
     * @OA\Get (
     *     path="/api/v1/user/userDetailExchangeLog/{id}",
     *     tags={"兑换管理"},
     *     summary="用户个人兑换详情",
     *     description="用户个人兑换列表列表/(2022/04/05日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="header头token"),
     *     @OA\Parameter(name="id", in="query", @OA\Schema(type="int"),description="主键id"),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *               @OA\Property(property="list", type="array", description="数据列表",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="int", description="主键id"),
     *                      @OA\Property(property="v_type", type="string", description="蔬菜的名字"),
     *                      @OA\Property(property="status", type="int", description="蔬菜种类的状态1可种植 2不可种植"),
     *                      @OA\Property(property="v_price", type="int", description="蔬菜认领的价格"),
     *                      @OA\Property(property="f_price", type="int", description="蔬菜币"),
     *                      @OA\Property(property="grow_1", type="int", description="种子时期"),
     *                      @OA\Property(property="grow_2", type="int", description="幼苗时期"),
     *                      @OA\Property(property="grow_3", type="int", description="生长"),
     *                      @OA\Property(property="grow_4", type="int", description="成年"),
     *                      @OA\Property(property="grow_5", type="int", description="成熟"),
     *                      @OA\Property(property="storage_time", type="int", description="可以存放的时间"),
     *                      @OA\Property(property="create_time", type="int", description="创建时间"),
     *                      @OA\Property(property="update_time", type="int", description="更新时间"),
     *                  ),
     *               ),
     *            ),
     *         ),
     *     ),
     * )
     */
    function userDetailExchangeLog(Request $request)
    {
        if (!isset($request->id)) {
            return $this->backArr('id必须', config("comm_code.code.fail"), []);
        }
        if ($request->id <= 0) {
            return $this->backArr('id必须大于0', config("comm_code.code.fail"), []);
        } else {
            $data["id"] = $request->id;
        }
        $page = isset($request->page) ? (int)$request->page : 1;
        $pageSize = isset($request->page_size) ? (int)$request->page_size : 10;
        $userInfo = $this->getUserInfo($request->header("token"));
        $exchangeList = ExchangeLogService::getExchangeLogList($userInfo["id"], [
            "page" => $page,
            "page_size" => $pageSize,
        ]);

        if (count($exchangeList["list"]) <= 0) {
            return $this->backArr('详情不存在，请重试！', config("comm_code.code.fail"), []);
        }
        return $this->backArr('获取详情成功', config("comm_code.code.ok"), $exchangeList["list"][0]);

    }
}
