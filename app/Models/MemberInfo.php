<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//用户表
class MemberInfo extends Model
{
    protected $table = 'member_info';
    protected $dateFormat = 'U';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

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
        $selfRs = $selfRs->where("tel", '=', $phone)->first();
        if ($selfRs != null) {
            return $selfRs->toArray();
        }
        return [];
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

    // 购买记录
    function buyLog()
    {
        $this->hasMany(BuyLog::class, "m_id");
    }

    // 头像
    function userImage()
    {
        $this->hasOne(HeadImg::class, "m_id");
    }

    // 头像
    function userDeliverOrder()
    {
        $this->hasMany(DeliveryOrder::class, "m_id");
    }

    // 支付订单
    function userPaymentOrder()
    {
        $this->hasMany(PaymentOrder::class, "m_id");
    }

    // 兑换记录
    function userExchangeLog()
    {
        $this->hasMany(ExchangeLog::class, "m_id");
    }

    // 用户蔬菜信息
    function userVegetable()
    {
        $this->hasMany(MemberVegetable::class, "m_id");
    }

    // 新增或更新用户信息
    static function updateUserInfo($uId, $data = []): int
    {
        $model = self::with([])->where("id", '=', $uId);
        if (count($data) != 0) {
            return $model->update($data);
        }
        return 0;
    }

    // 自增长
    static function increaseVegetableNums($uId, $nums = 1): int
    {
        return self::with([])->where("id", '=', $uId)
            ->increment('vegetable_num', $nums);
    }

    // 自减少
    static function decreaseVegetableNums($uId, $nums = 1): int
    {
        return self::with([])->where("id", '=', $uId)
            ->decrement('vegetable_num', $nums);
    }
}
