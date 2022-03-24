<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//用户表
class MemberInfo extends Model
{
    protected $table = 'member_info';

    //添加用户
    static function addUser($data): int
    {
        $selfRs = self::with([]);
        return $selfRs->insertGetId($data);
    }

    //查询单个用户根据手机号
    static function findUserByPhone($phone, $data = []): array
    {
        $selfRs = self::with([]);
        if (!empty($data)) {
            $selfRs = $selfRs->where($data);// 如密码
        }
        return $selfRs->where("tel", '=', $phone)->first();
    }

    //查询单个用户根据uid
    static function findUserByUid($id): array
    {
        $selfRs = self::with([]);
        return $selfRs->find($id);
    }

    //查询用户列表
    static function getUserList($data): array
    {
        $selfRs = self::with([]);
        //todo
        $rsData = $selfRs->get();
        if ($rsData->toArray()) {
            return $rsData->toArray();
        }
        return [];
    }

    //删除用户
    static function delUserByUId($uId): int
    {
        $selfRs = self::with([]);
        return $selfRs->where($uId)->delete();
    }

    //删除用户
    static function delUserByPhone($phone): int
    {
        $selfRs = self::with([]);
        return $selfRs->where($phone)->delete();
    }
}
