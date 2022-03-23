<?php


namespace App\Http\Controllers\Admin\Logistics;


class OrderController
{
    public function index()
    {
        return view("admin.logistics.order");
    }

    public function data()
    {
        $data = [
            [
                "id" => 1, "name" => "张三", "payment_type" => "支付宝", "f_price" => "200.00", "status" => "已支付", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id" => 1, "name" => "张三", "payment_type" => "支付宝", "f_price" => "200.00", "status" => "已支付", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id" => 1, "name" => "张三", "payment_type" => "支付宝", "f_price" => "200.00", "status" => "已支付", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id" => 1, "name" => "张三", "payment_type" => "支付宝", "f_price" => "200.00", "status" => "已支付", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id" => 1, "name" => "张三", "payment_type" => "支付宝", "f_price" => "200.00", "status" => "已支付", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ]
        ];
        $code = 0;
        $count = count($data);
        return response()->json(compact('data', 'code', 'count'));
    }
}
