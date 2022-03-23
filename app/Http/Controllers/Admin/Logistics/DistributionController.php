<?php


namespace App\Http\Controllers\Admin\Logistics;


class DistributionController
{
    public function index()
    {
        return view("admin.logistics.distribution");
    }

    public function data()
    {
        $data = [
            [
                "id" => 1, "name" => "张三", "order_type" => "兑换单", "f_price" => "200.00", "status" => "配送中", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id" => 1, "name" => "张三", "order_type" => "兑换单",  "f_price" => "200.00", "status" => "配送中", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id" => 1, "name" => "张三", "order_type" => "兑换单",  "f_price" => "200.00", "status" => "配送中", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id" => 1, "name" => "张三", "order_type" => "兑换单",  "f_price" => "200.00", "status" => "配送中", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id" => 1, "name" => "张三", "order_type" => "兑换单",  "f_price" => "200.00", "status" => "已签收", "wechat_no" => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),"create_time"=>date("Y-m-d H:i:s")
            ]
        ];
        $code = 0;
        $count = count($data);
        return response()->json(compact('data', 'code', 'count'));
    }
}
