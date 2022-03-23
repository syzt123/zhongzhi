<?php


namespace App\Http\Controllers\Admin\System;


class NoticeController
{
    public function index()
    {
        return view('admin.system.notice');
    }
    public function data()
    {
        $data = [
            [
                "id"=>1,"notice"=>"下雨收衣服喽","create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id"=>1,"notice"=>"下雨收衣服喽","create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id"=>1,"notice"=>"下雨收衣服喽","create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id"=>1,"notice"=>"下雨收衣服喽","create_time"=>date("Y-m-d H:i:s")
            ],
            [
                "id"=>1,"notice"=>"下雨收衣服喽","create_time"=>date("Y-m-d H:i:s")
            ]
        ];
        $code=0;
        $count = count($data);
        return response()->json(compact('data','code','count'));
    }
}
