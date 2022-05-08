<?php

namespace App\Http\Services\TencentVod;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Vod\V20180717\VodClient;
use TencentCloud\Vod\V20180717\Models\CommitUploadRequest;

class VodManagerService
{
    //获取签名
    static function getSign()
    {
        // 确定 App 的云 API 密钥
        $secret_id = config("comm_code.tencent_vod.secret_id");
        $secret_key = config("comm_code.tencent_vod.secret_key");
        // 确定签名的当前时间和失效时间
        $current = time();
        $expired = $current + 86400;  // 签名有效期：1天
        // 向参数列表填入参数
        $arg_list = array(
            "secretId" => $secret_id,
            "currentTimeStamp" => $current,
            "expireTime" => $expired,
            "random" => rand(),
            "procedure" => 'LongVideoPreset',//自动转码 转自适应码流、截图、截取封面图 // https://cloud.tencent.com/document/product/266/9221
        );
        // 计算签名
        $original = http_build_query($arg_list);
        $signature = base64_encode(hash_hmac('SHA1', $original, $secret_key, true) . $original);
        //echo $signature;
        //echo "\n";
        return $signature;
    }

    /**
     * 确认上传媒体文件
     */
    static function confirmUploadVideo()
    {
        try {
            $cred = new Credential("SecretId", "SecretKey");
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "ap-seoul", $clientProfile);

            $req = new CommitUploadRequest();

            $params = array(
                "VodSessionKey" => "rdsfsdfsdfsdfafd"
            );
            $req->fromJsonString(json_encode($params));

            $resp = $client->CommitUpload($req);

            print_r($resp->toJsonString());
        } catch (TencentCloudSDKException $e) {
            echo $e;
        }
    }

    /*
     * 申请上传
     */
    static function applyUploadVideo()
    {
        try {

            $cred = new Credential("SecretId", "SecretKey");
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "ap-chengdu", $clientProfile);

            $req = new ApplyUploadRequest();

            $params = array(
                "MediaType" => "mp4"
            );
            $req->fromJsonString(json_encode($params));

            $resp = $client->ApplyUpload($req);

            print_r($resp->toJsonString());
        } catch (TencentCloudSDKException $e) {
            echo $e;
        }
    }

    /**
     * 根据媒体Id删除媒体
     */
    static function delVideoByFileId()
    {
        try {

            $cred = new Credential("SecretId", "SecretKey");
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);

            $req = new DeleteMediaRequest();

            $params = array(
                "FileId" => "wwwwwwwwwwwww"
            );
            $req->fromJsonString(json_encode($params));

            $resp = $client->DeleteMedia($req);

            print_r($resp->toJsonString());
        } catch (TencentCloudSDKException $e) {
            echo $e;
        }
    }
}
