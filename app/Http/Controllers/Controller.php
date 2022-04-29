<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($data = "", $message = 'ok', $code = 1)
    {
        return response()->json(compact('data', 'message', 'code'), 200);
    }

    public function error($message = 'error', $data = "", $code = 0)
    {
        return response()->json(compact('data', 'message', 'code'), 400);
    }

    //返回json格式
    public function backjson($msg, $code = 200, $data = []): string
    {
        $arr = [
            'msg' => $msg,
            'code' => $code,
            'data' => $data,
        ];
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    //返回数组
    public function backArr($msg, $code = 200, $data = []): array
    {
        return [
            'msg' => $msg,
            'code' => $code,
            'data' => $data,
        ];
    }

    //手机号验证
    public function checkPhone($phone = ''): bool
    {
        $phone = preg_match_all("/^1[123456789]\d{9}$/", $phone);
        return $phone;
    }

    //获取用户信息
    public function getUserInfo($token = ''): array
    {
        //读取缓存
        $jsonData = json_decode(Redis::get(config("comm_code.redis_prefix.token") . $token), true);
        if (isset($jsonData["password"])) {
            unset($jsonData["password"]);
        }
        return $jsonData ?? [];
    }

    //生成token规则
    public function createTokenRules($string = ''): string
    {
        $key = config("comm_code.redis_prefix.token") . md5($string);
        $days = 15 * 24 * 60 * 60;
        $rs = Redis::setex($key, $days, $string);
        if ($rs) {
            return md5($string);
        }
        return '';
    }

    //请求方式
    static function methodCurl($url = '', $method = 'post', $contentType = 1, $data = []): array
    {
        if ($contentType == 1) {
            $headerArray = array("Content-Type:application/x-www-form-urlencoded");
        } else {
            $headerArray = array("Content-type:application/json", "charset='utf-8'");
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($method == 'PUT') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT"); //设置请求方式
        }
        if ($method == 'PATCH') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
        }
        if ($method == 'DELETE') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_HEADER, 0);//不抓取头部信息。只返回数据
        // 执行操作
        $response_body = curl_exec($curl);
        //捕抓异常
        $error_msg = "";
        if (curl_errno($curl)) {
            $error_msg = 'Errno' . curl_error($curl);
        }
        // 关闭CURL会话
        curl_close($curl);
        // 返回结果
        $response = json_decode($response_body, true);//请求接口返回的数据 大于0代表成功，否则根据返回值查找错误
        if (isset($response["code"]) && ($response["code"] == '200' || $response["code"] == 200)) {
            $response["msg"] = $error_msg;//curl post 提交发生的错误
        }
        return $response;
    }

    static function httpCurl($url = '', $method = 'get', $header = [], $param = [], $contentType = 1)
    {
        $val = strtolower($method) == 'get' ? 'GET' : 'POST';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);//接口地址
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $val);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        //设置header头
        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($param));
        if ($contentType == 1) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type:application/json", "charset='utf-8'"));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // 执行操作
        $response_body = curl_exec($curl);
        //捕抓异常
        $error_msg = "";
        if (curl_errno($curl)) {
            $error_msg = 'Errno' . curl_error($curl);
        }
        // 关闭CURL会话
        curl_close($curl);
        // 返回结果
        $response = json_decode($response_body, true);//请求接口返回的数据 大于0代表成功，否则根据返回值查找错误
        if (isset($response["code"]) && ($response["code"] == '200' || $response["code"] == 200)) {
            $response["msg"] = $error_msg;//curl post 提交发生的错误
        }
        return $response;
    }

    /**
     * @OA\Post (
     *     path="/api/v1/com_uploads",
     *     tags={"单/多图片公共上传",},
     *     summary="公共上传接口",
     *     description="公共上传接口(2022/03/28日完)",
     *     @OA\Parameter(name="token", in="header", @OA\Schema(type="string"),description="heder头带token"),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="上传的字段为files且必须为数组格式",
     *                     property="files[]",
     *                     type="file",
     *                     format="file",
     *                 ),
     *                 required={"files[]"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="{code: 200, msg:string, data:[]}",
     *     ),
     *    )
     */
    static function uploadImges(Request $request)
    {
        if ($request->isMethod('post')) {
            $files = $request->allFiles();
            if (!isset($files["files"])) {
                return ['code' => -1, 'msg' => 'files[]字段必须', 'data' => []];
            }
            if (!is_array($files["files"])) {
                return ['code' => -1, 'msg' => 'files字段必须为数组', 'data' => []];
            }
            if (count($files["files"]) > 9) {
                return ['code' => -1, 'msg' => '最多只能上传9张图片/文件', 'data' => []];
            }
            if (is_array($files["files"])) {
                $imgs = [];
                foreach ($files["files"] as $file) {
                    $path = Storage::disk('uploads')->putFile(date('Ymd'), $file);
                    if ($path != false) {
                        //图片追加
                        $imgs[] = config("comm_code.local_url") . '/uploads/public/' . $path;
                    }
                }
                return ['code' => 200, 'msg' => '上传成功', 'data' => $imgs];
            }
        } else {
            return ['code' => -1, 'msg' => '非法请求,请求方式须为post', 'data' => []];
        }
    }

    // 生成唯一订单号
    static function getUniqueOrderNums(): string
    {
        return date('YmdHis', time()) . substr(microtime(), 2, 6) . sprintf('%04d', rand(0, 9999));
    }

    // 获取支付类型列表
    function getPayTypeList(): array
    {
        return [
            "ali", "h5_wechat", "js_wechat", "native_wechat", "app_wechat"
        ];
    }

    // 判断支付类型是否存在
    function isHasInPayType(string $payType): bool
    {
        return in_array($payType, self::getPayTypeList());
    }

    // xml转数组
    function xmlToArr($xml = ''): array
    {
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    // 数组转xml
    function arrToXml($arr = []): string
    {

    }
}
