<?php

// 初始化
define("VOD_CLIENT_NAME", 'AliyunVodClientDemo');

use \AlibabaCloud\Client\AlibabaCloud;
use \AlibabaCloud\Client\Exception\ClientException;
use \AlibabaCloud\SDK\Vod\V20170321\Vod;

use \Darabonba\OpenApi\Models\Config;
use \AlibabaCloud\SDK\Vod\V20170321\Models\CreateUploadVideoRequest;
use \AlibabaCloud\SDK\Vod\V20170321\Models\GetVideoPlayAuthRequest;
use \AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoRequest;
use \AlibabaCloud\SDK\Vod\V20170321\Models\DeleteVideoRequest;

// 阿里云点播管理
class VodManagerController
{

    /**
     * 使用AK&SK初始化账号Client
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @return Vod Client
     */
    public static function createClient($accessKeyId, $accessKeySecret): Vod
    {
        $config = new Config([
            // 您的AccessKey ID
            "accessKeyId" => $accessKeyId,
            // 您的AccessKey Secret
            "accessKeySecret" => $accessKeySecret
        ]);
        // 访问的域名
        $config->endpoint = "vod.cn-shenzhen.aliyuncs.com";
        return new Vod($config);
    }

    /**
     * 获取视频上传地址和凭证，并创建视频信息
     * @param string[] $args
     * @return void
     */
    public static function main($args)
    {
        $client = self::createClient("accessKeyId", "accessKeySecret");
        $createUploadVideoRequest = new CreateUploadVideoRequest([
            "description" => "上传",
            "fileName" => "demo.avi",
            "fileSize" => 102400,
            "title" => "woain",
            "cateId" => 40002
        ]);
        // 复制代码运行请自行打印 API 的返回值

        $client->createUploadVideo($createUploadVideoRequest);
    }

    /**
     * 获通过视频ID直接获取媒体文件（支持视频和音频）的播放地址
     * @param string $videoId
     * @return void
     */
    public static function dddd($videoId = '')
    {
        $client = self::createClient("accessKeyId", "accessKeySecret");
        $getPlayInfoRequest = new GetPlayInfoRequest([
            "videoId" => "easrfdsffaf"
        ]);
        // 复制代码运行请自行打印 API 的返回值
        $client->getPlayInfo($getPlayInfoRequest);
    }

    /**
     * 获取音视频播放凭证
     * @param string $videoId
     * @return void
     */
    public static function getVideoPlayAuth($videoId = '')
    {
        $client = self::createClient("accessKeyId", "accessKeySecret");
        $getVideoPlayAuthRequest = new GetVideoPlayAuthRequest([
            "videoId" => $videoId,
        ]);
        // 复制代码运行请自行打印 API 的返回值
        $client->getVideoPlayAuth($getVideoPlayAuthRequest);
    }

    /**
     * 删除完整视频（包括其源文件、转码后的流文件、封面截图等），支持批量删除
     * @param $args
     */
    public static function delete11($args)
    {
        $client = self::createClient("accessKeyId", "accessKeySecret");
        $deleteVideoRequest = new DeleteVideoRequest([
            "videoIds" => "wwww,eerwrewrwe,wwqeq"
        ]);
        // 复制代码运行请自行打印 API 的返回值
        $client->deleteVideo($deleteVideoRequest);
    }


}
