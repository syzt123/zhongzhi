<?php

namespace App\Http\Controllers\Api\V1\Ys;

use \App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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

        //$tokenInfo = self::methodCurl($url, "post", 0, $data);
        $tokenInfo = self::httpCurl($url, "post", [], $data);
        if (isset($tokenInfo["code"]) && $tokenInfo["code"] == '200' && isset($tokenInfo["data"]["accessToken"])) {
            //写入缓存
            Redis::setex(config('comm_code.ys_config.accessToken'), 6.8 * 24 * 60 * 60, $tokenInfo["data"]["accessToken"]);
            return $tokenInfo["data"]["accessToken"];
        } else {
            return '';
        }
    }

    // 刷新token过期
    static function flashAccessToken()
    {
        // 取
        $token = Redis::get(config('comm_code.ys_config.accessToken'));
        if (!$token) {
            // 刷新
            $token = self::getAccessToken();
        }
        return $token;
    }

    // 获取直播地址
    static function getLiveAddress(Request $request): array
    {
        if (!isset($request->device_serial)) {
            return ["msg" => '设备号必须', "data" => [], "code" => -1];
        }
        if (($request->device_serial === '')) {
            return ["msg" => '设备号不能为空', "data" => [], "code" => -1];
        }
        $url = config('comm_code.ys_config.url') . 'lapp/v2/live/address/get';
        $data = [
            "accessToken" => self::flashAccessToken(),
            "deviceSerial" => $request->device_serial,//config('comm_code.ys_config.deviceSerial'),
            "channelNo" => config('comm_code.ys_config.channelNo'),
            "expireTime" => 720 * 24 * 60 * 60,
            "protocol" => 2,//1-ezopen、2-hls、3-rtmp、4-flv，默认为1
            "quality" => 2,//视频清晰度，1-高清（主码流）、2-流畅（子码流）
        ];
        // 取出所有的再追加。
        $rs = self::httpCurl($url, "post", [], $data);
        if (isset($rs["code"]) && (int)$rs["code"] != 200) {
            return ["msg" => $rs["msg"], "data" => [], "code" => -1];
        }
        if (isset($rs["data"]["url"])) {
            // 存直播url并设置有效期
            //var_dump(config("comm_code.ys_config.live_address"), [config('comm_code.ys_config.deviceSerial') => $rs["code"]["url"]]);
            Redis::lpush(config("comm_code.ys_config.live_address"), json_encode([config('comm_code.ys_config.deviceSerial') => $rs["code"]["url"]]));
        }

        /*$rs = [
            "msg" => "Operation succeeded",
            "code" => "200",
            "data" => [
                "id" => "254708522214232064",//https://open.ys7.com/v3/openlive/J38620611_1_2.m3u8?expire=1648524458&id=428515506633474048&t=3b7ec39cc98e76472f9f321ad642eb5ac273e01d9e8e2f1a492426c6c0187471&ev=100
                "url" => "https://open.ys7.com/v3/openlive/C78957921_1_1.m3u8?expire=1606999273&id=254708522214232064&t=093e5c6668d981e0f0b8d2593d69bdc98060407d1b2f42eaaa17a62b15ee4f99&ev=100",
                "expireTime" => "2020-12-03 20:41:13"
            ]
        ];*/
        //Redis::lpush(config("comm_code.ys_config.live_address"), json_encode([config('comm_code.ys_config.deviceSerial').'888' => $rs["data"]["url"]]));
        // 加入新数据
        //Redis::hmset(config("comm_code.ys_config.live_address"), $request->device_serial, $rs["data"]["url"]);

        // 获取所有的数据
        /*$rrrr = Redis::hgetall(config("comm_code.ys_config.live_address"));
        var_dump('meget数据：',$rrrr);*/
        return ["msg" => '设备号必须', "data" => $rs["data"], "code" => 200];
    }
}
