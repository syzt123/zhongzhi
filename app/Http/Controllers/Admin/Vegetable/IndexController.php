<?php


namespace App\Http\Controllers\Admin\Vegetable;


class IndexController
{
    public function index()
    {
        return view('admin.vegetable.index');
    }
    public function data(){
        $data = [
            [
                'id'=>1, 'v_type'=>"花生", 'status'=>"已入库", 'v_price'=>"5.60",'f_price'=>56, 'grow_1'=>1,'grow_2'=>2, 'grow_3'=>3, 'grow_4'=>4, 'grow_5'=>5, 'storage_time'=>6,
            ],
            [
                'id'=>1, 'v_type'=>"花生", 'status'=>"已入库", 'v_price'=>"5.60",'f_price'=>56, 'grow_1'=>1,'grow_2'=>2, 'grow_3'=>3, 'grow_4'=>4, 'grow_5'=>5, 'storage_time'=>6,
            ],
            [
                'id'=>1, 'v_type'=>"花生", 'status'=>"已入库", 'v_price'=>"5.60",'f_price'=>56, 'grow_1'=>1,'grow_2'=>2, 'grow_3'=>3, 'grow_4'=>4, 'grow_5'=>5, 'storage_time'=>6,
            ],
            [
                'id'=>1, 'v_type'=>"花生", 'status'=>"已入库", 'v_price'=>"5.60",'f_price'=>56, 'grow_1'=>1,'grow_2'=>2, 'grow_3'=>3, 'grow_4'=>4, 'grow_5'=>5, 'storage_time'=>6,
            ],
            [
                'id'=>1, 'v_type'=>"花生", 'status'=>"已入库", 'v_price'=>"5.60",'f_price'=>56, 'grow_1'=>1,'grow_2'=>2, 'grow_3'=>3, 'grow_4'=>4, 'grow_5'=>5, 'storage_time'=>6,
            ]
        ];
        $code=0;
        $count = count($data);
        return response()->json(compact('data','code','count'));
    }
}
