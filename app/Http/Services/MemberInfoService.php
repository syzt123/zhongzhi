<?php

namespace App\Http\Services;
// 用户信息
use App\Models\HeadImg;
use App\Models\MemberInfo;
use Illuminate\Support\Facades\Request;

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
    static function getUserInfo($uid){
        return MemberInfo::find($uid);
    }

    // 新增或更新用户信息
    static function updateUserInfo($uId, $data = []):int
    {

        // 更新头像
        if (isset($data["head_img"]) && trim($data["head_img"]) != '') {
            HeadImg::updateHeadImg($uId, ["head" => $data["head_img"]]);
            // 更新缓存

        }
        // 更新发货地址
        if (isset($data["user_address"]) && trim($data["user_address"]) != '') {
            MemberInfo::updateUserInfo($uId, ["v_address" => $data["user_address"]]);
        }
        // 更新昵称
        if (isset($data["nickname"]) && trim($data["nickname"]) != '') {
            MemberInfo::updateUserInfo($uId, ["nickname" => $data["nickname"]]);
        }
        return 1;
    }
}
