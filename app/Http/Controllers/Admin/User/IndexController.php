<?php


namespace App\Http\Controllers\Admin\User;


class IndexController
{
    public function index()
    {
        return view("admin.user.index");
    }
    public function data()
    {
        $data = [
            [
                'id'=>1,
                'tel'=>80,
                'nick_name'=>"张三",
                'gold'=>80,
                'vegetable_num'=>10,
                'status'=>"正常",
                'v_address'=>"成都市成华区xxx小区"
            ],
            [
                'id'=>1,
                'tel'=>80,
                'nick_name'=>"张三",
                'gold'=>80,
                'vegetable_num'=>10,
                'status'=>"正常",
                'v_address'=>"成都市成华区xxx小区"
            ],
            [
                'id'=>1,
                'tel'=>80,
                'nick_name'=>"张三",
                'gold'=>80,
                'vegetable_num'=>10,
                'status'=>"正常",
                'v_address'=>"成都市成华区xxx小区"
            ]
        ];
        $code=0;
        $count = count($data);
        return response()->json(compact('data','code','count'));
    }
}
