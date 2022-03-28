<?php


namespace App\Models\Admin;


use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use App\Models\MemberInfo as Base;

class MemberInfo extends Base
{
    protected $table = "member_info";
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $casts = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];
    public function getStatusAttribute($value)
    {
        $arr = ["禁用", '正常', "其他"];
        return $arr[$value];
    }
    public function creatLand(){
        try {
            DB::beginTransaction();
            $this->tel = Request::get('tel');
            $this->nickname =Request::get('nickname');
            $this->gold =Request::get('gold');
            $this->status =Request::get('status');
            $this->vegetable_num =Request::get('vegetable_num');
            $this->v_address =Request::get('v_address');
            $this->password =md5(Request::get('password'));
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
            $member = $this->find(Request::input('id'));
            $member->v_address = Request::input('v_address');
            $member->nickname =Request::input('nickname');
            $member->vegetable_num =Request::input('vegetable_num');
            $member->tel =Request::input('tel');
            $member->status =Request::input('status');
            $member->gold =Request::input('gold');
            $member->save();
            DB::commit();
            return true;
        }catch (QueryException $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
