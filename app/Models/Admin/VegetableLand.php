<?php


namespace App\Models\Admin;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class VegetableLand extends Base
{
    protected $table = "vegetable_land";
    protected $fillable = ['monitor', 'v_num', 'l_status'];

    public function getLStatusAttribute($value)
    {
        $arr = ["未使用", '已使用', "其他"];
        return $arr[$value];
    }
    public function creatLand(){
        try {
            DB::beginTransaction();
            $this->monitor = Request::get('monitor');
            $this->v_num =Request::get('v_num');
            $this->l_status =Request::get('l_status');
            $this->save();
            DB::commit();
            return true;
        }catch (QueryException $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function editLand(){
        try {
            DB::beginTransaction();
            $land = $this->find(Request::input('id'));
            $land->monitor = Request::input('monitor');
            $land->v_num =Request::input('v_num');
            $land->l_status =Request::input('l_status');
            $land->save();
            DB::commit();
            return true;
        }catch (QueryException $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

}
