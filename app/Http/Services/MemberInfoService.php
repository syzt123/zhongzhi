<?php

namespace App\Http\Services;
// 用户信息
use App\Models\HeadImg;
use App\Models\MemberInfo;

class MemberInfoService extends BaseService
{
    //注册用户
    static function registerUser($data): int
    {
        return MemberInfo::addUser($data);
    }

    //获取用户登陆信息
    static function LoginUser($data): array
    {
        return MemberInfo::findUserByPhone($data["tel"], $data);
    }

    //删除登陆信息
    static function delUser($phone): int
    {
        return MemberInfo::delUserByPhone($phone);
    }

    // 获取用户头像
    static function getUserImgInfo($uid): array
    {
        return HeadImg::getUserHeadImg($uid);
    }
}
