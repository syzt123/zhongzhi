<?php


namespace App\Http\Controllers;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class IndexController
{
    public function index()
    {
        $data = [
            "lng" => $this->randomFloat(101, 103),
            "lat" => $this->randomFloat(31, 32),
            "type" => date("i") % 2,
            "index" => 0
        ];

        $key = "user_location" . date('H');
        $len = Redis::LLEN($key);
        $newest = Redis::RPOP($key);
//        if ($newest) {
//            $newestPoint = json_decode($newest, true);
//            $data['index'] = $newestPoint["index"] + 1;
//            if ($len !== 1) {
//                if ($newestPoint['type']) {
//                    Redis::RPUSH($key, json_encode($newestPoint), json_encode($data));
//                } else {
//                    $data['index'] = $newestPoint["index"];
//                    Redis::RPUSH($key, json_encode($data));
//                }
//            } else {
//                Redis::RPUSH($key, $newest, json_encode($data));
//            }
//
//        } else {
//            Redis::RPUSH($key, json_encode($data));
//        }
        if ($newest) {
            $newestPoint = json_decode($newest, true);
            $data['index'] = $newestPoint["index"] + 1;
            if ($len !== 1) {
                if ($newestPoint['type']) {
                    Redis::RPUSH($key, json_encode($newestPoint), json_encode($data));
                } else {
                    $data['index'] = $newestPoint["index"];
                    Redis::RPUSH($key, json_encode($data));
                }
            } else {
                Redis::RPUSH($key, $newest, json_encode($data));
            }

        } else {
            Redis::RPUSH($key, json_encode($data));
        }

        $data = Redis::LRANGE($key,0,$len);
        foreach ($data as &$value)
        {
            $value = json_decode($value,$value);
        };
        return response()->json(compact('data'));

    }

    private function randomFloat($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
