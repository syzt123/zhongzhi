<?php

namespace App\Http\Controllers\Api\V1\Client;
/**
 * @OA\Info (title="接口文档",version="V1",description="学习测试用的 先用再研究")
 * @OA\Tag(name="index",description="首页模块",)
 */
class IndexController
{
    /**
     * @OA\Get (
     *     path="/api/index/index",
     *     tags={"index"},
     *     summary="首页",
     *     @OA\Parameter(name="isFastLogin", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="client", in="header"),
     *     @OA\Parameter(name="version", in="header"),
     *     @OA\Parameter(name="tel", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="verification_code", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="  {err_code: int32, msg:string, data:[]}  "
     *     )
     * )
     */

    public function index(): string
    {
        return 'ok';
    }
}
