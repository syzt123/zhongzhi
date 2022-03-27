<?php

namespace App\Http\Controllers\Api\V1\Ys;

use \App\Http\Controllers\Controller;

use \Illuminate\Support\Facades\Cache;

//萤石
class YsController extends Controller
{
    // 获取accessToken
    static function getAccessToken()
    {
        $url = config('comm_code.ys_config.url') . 'lapp/token/get';
        $data = [
            "appKey" => config('comm_code.ys_config.appKey'),
            "appSecret" => config('comm_code.ys_config.appSecret'),
        ];

        $stringData = http_build_query($data);
        $tokenInfo = self::methodCurl($url, "post", 1, $stringData);
        //var_dump($tokenInfo);
        //
        if (isset($tokenInfo["code"]) && $tokenInfo["code"] == '200' && isset($tokenInfo["data"]["accessToken"])) {
            //写入缓存
            Cache::setex(config('comm_code.ys_config.accessToken'), 6.8 * 24 * 60 * 60, $tokenInfo["data"]["accessToken"]);
            return $tokenInfo["data"]["accessToken"];
        } else {
            return '';
        }
    }

    // 刷新token过期
    static function flashAccessToken()
    {
        // 取
        $token = Cache::get(config('comm_code.ys_config.accessToken'));
        if (!$token) {
            // 刷新
            $token = self::getAccessToken();
        }
        return $token;
    }

    // 获取直播地址
    static function getLiveAddress(): array
    {
        $url = config('comm_code.ys_config.url') . 'lapp/v2/live/address/get';
        $data = [
            "accessToken" => self::flashAccessToken(),
            "deviceSerial" => config('comm_code.ys_config.deviceSerial'),
            "channelNo" => config('comm_code.ys_config.channelNo'),
        ];
        return self::methodCurl($url, "post", 1, $data);
    }
}
