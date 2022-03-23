<?php


namespace App\Http\Controllers\Admin\Land;


class IndexController
{
    public function index()
    {
        return view('admin.land.index');
    }
    public function data()
    {
        $data = [
            [
                "id"=>1,
                "monitor"=>"tcp://192.168.101.132",
                "v_num"=>"10",
                "l_status"=>"闲置"
            ],
            [
                "id"=>1,
                "monitor"=>"tcp://192.168.101.132",
                "v_num"=>"10",
                "l_status"=>"闲置"
            ],
            [
                "id"=>1,
                "monitor"=>"tcp://192.168.101.132",
                "v_num"=>"10",
                "l_status"=>"闲置"
            ],
            [
                "id"=>1,
                "monitor"=>"tcp://192.168.101.132",
                "v_num"=>"10",
                "l_status"=>"闲置"
            ]
        ];
        $code=0;
        $count = count($data);
        return response()->json(compact('data','code','count'));
    }
}
