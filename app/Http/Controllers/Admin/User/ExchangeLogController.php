<?php


namespace App\Http\Controllers\Admin\User;


class ExchangeLogController
{
    public function index()
    {
        return view("admin.user.exchange_log");
    }
    public function data()
    {
        $data = [
            [
                'id' => 1,
                'nick_name' => "张三",
                'f_price' => '1.10',
                'v_num' => 10,
                'n_price' => 11,
                'create_time' => date("Y-m-d H:i:s")
            ],
            [
                'id' => 1,
                'nick_name' => "张三",
                'f_price' => '1.10',
                'v_num' => 10,
                'n_price' => 11,
                'create_time' => date("Y-m-d H:i:s")
            ],
            [
                'id' => 1,
                'nick_name' => "张三",
                'f_price' => '1.10',
                'v_num' => 10,
                'n_price' => 11,
                'create_time' => date("Y-m-d H:i:s")
            ],
            [
                'id' => 1,
                'nick_name' => "张三",
                'f_price' =>'1.10',
                'v_num' => 10,
                'n_price' => 11,
                'create_time' => date("Y-m-d H:i:s")
            ],
        ];
        $code=0;
        $count = count($data);
        return response()->json(compact('data','code','count'));
    }
}
