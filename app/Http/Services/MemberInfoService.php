<?php

namespace App\Http\Services;
// 用户信息
use App\Models\MemberInfo;

class MemberInfoService
{
    //注册用户
    static function registerUser($data): int
    {
        return MemberInfo::addUser($data);
    }

    //获取用户登陆信息
    static function LoginUser($data): array
    {
        return MemberInfo::findUserByPhone($data["phone"], $data);
    }

    //删除登陆信息
    static function delUser($phone): int
    {
        return MemberInfo::delUserByPhone($phone);
    }
}
