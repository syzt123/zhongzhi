<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('TOKEN') === null) {
            $data = [
                "code" => -1,
                'msg' => "header中必须包含token",
                'data' => [],
            ];

            return Response(json_encode($data,JSON_UNESCAPED_UNICODE));
        } else {
            //校验正确性
            $token = $request->header('TOKEN');
            //
            if (!Redis::get(config("comm_code.redis_prefix.token") . $token)) {
                //token过期
                $data = [
                    "code" => -1,
                    'msg' => "token已过期,请重新尝试！",
                    'data' => [],
                ];
                return Response(json_encode($data, JSON_UNESCAPED_UNICODE));
            }
        }
        return $next($request);
    }
}
