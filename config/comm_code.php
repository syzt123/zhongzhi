<?php

//公共码

return [
    'msg' => [
        'ok' => 'successful',
        'fail' => 'fail',
        'register_ok' => '注册成功',
        'register_fail' => '注册失败',
        'request_method_post' => '请求方式必须为post',
    ],
    'code' => [
        'ok' => 200,
        'fail' => -1,
        'not_found' => 403,
        'serve_error' => 502,
    ],
    'redis_prefix' => [
        "token" => 'laravel_user_token:prefix_',
    ],
    'ys_config' => [//萤石
        'url' => 'https://open.ys7.com/api/',
        'accessToken' => 'ys_access_token:accessToken',
        "appKey" => 'xxxxxxxxxxxxxxxx',
        "appSecret" => 'xxxxxxxxxxxxxxxx',
        "deviceSerial" => 'xxxxxxxxxxxxxxxx',
        "channelNo" => 1,
        "live_address" => 'ys_live_address:list',// ["key":"url"]存储为设备号:url
    ],
    'local_url' => 'http://139.186.152.4',//
    "ali_config" => [
        "appID" => '2016080600177462',//应用ID
        "sign_type" => 'RSA2',//加密方式
        "appPrivateKey" => 'MIIEpAIBAAKCAQEAgwHqQIUKWsi5Kmp4brEXSTVI/TcxGg3qEreRC/8tsalCt1p2bU5Kf3h11Z9hdUnZZwDq6f6g0tyeSSf7b81GaDgs2gMrkSbcCWiq0dTLt+JAOwqPxS9QxLs8kthYDxcyDQTY4AwAsuDHMz/4+7jpt+Wfjcalq8e3IeNXOq7DPiUTaI8RbrV6gOgx9GV2Z7HLKZM/83/nSm3FX75dFlIgASzeOb0fFvVJGTIKyX2UTxnUkaWiK1r6azf4ORUu/Wcu8q66adk62sNjx3ou3Os+bWRISuSHNEeavTrbCsmSGeKllDBAHL3j7CIqJY6GgeTXu3N7Od7Bie8vAB8d5Gv9zQIDAQABAoIBABRn1/T0xAgf+7d/ngKf0uC0TAok7qEASdVgglc8CAIEO0AT9x5Pc4snWDNOAAAk4JgKrIyF/MbbVkxOzfs8HhtdpJ7Qn5icVOmiQ/krDBA4TYjkvEAafpomD+lPiWfQVRtyBdXgCLvUBzMUY9PRmZXrTk7nAicflIbSwmOBlSe4paA/cBdglDvlwsgKspg7ae+ipAsb0yc6Z4zVRygOSkmuRhy+rOQ+iicOMVnaYiEOUrMLvA5OowGlZqw+gdFpXFFo8GedSmfHQ2bH7MX3FwQP8qNYx3CVpP5hN3MfYgvygxIFCqujrpjSpfMEiyFJ2EPCI3skRjzIRdjhJl1k9AECgYEAt9olAby0+ud8UjxkCu0NJGGeWgHZhzdTpMbLZhTKZpwpKKlhHcq8cImxnoJQXsY2ag0ehnq40RbJCS93XyFh8uVPCnfQzPN/BLB8l89hHZ1lT62FIjQ7IbLZwYlWzGVhFgVEsNQNCxwSYB+d/j6nSD626wwnfIAZB8zXWdeUovkCgYEAtmr6Glu3LdT1IsiFKmcIG6uNrwdSJ9KPubHoB+zt/DQ/gFJyO86+gPJm7uKRn5SDbBafm3AqJ1EDMGaT1EZ5QP+ZbB6hQaNpzxa037uWPdMICC5XGVRimj5i9a9r0kLWP/8RXE7APTbAXyXG2StxFSo398UyBM+xK/yIybRIEnUCgYEAtXTVr5g7m6PbXSMDrpD2tqCudLF8q+scX/ZhC/ibJ5kbOmmqU4gkYoJUT1jY0CGb1SHrrdj0DlIJy0oQ7FB7ZwuR7ogBCjeNduhloEPq2xrcwj5Ft1OLxR/LsivGAmhQ4TYD7O7tjLiBHmQ2QJg/7JsRWH2ff07C5aL0LVUL+6kCgYB4gYcjLn9+OXaPOeQutM24acY5YB63y/PCenKL+crllyZ0AQMR22wiBwBUwzvsLdH7754Usy5FttDigoEZ9ExZ0gBqWnmwwOE/OwLV4jhIM0bVELfdNc/FkX0STIZv6lNbB4dScXuxdJ/0uyH3iSk1ECTU+2Ilp4alRjie9we9mQKBgQCGDYooYt/sqi0F+osfi7rEpxNTJdMwydquQhPlsJS7mipJt/RrWG/nkwumIJpCPVCwECKo0E8lidk5mSr8Z5Z73NJO/n/wSoTzrWeZnJ/TOWE6Rq2aCGgFxjAihIsLBWt7jx84gWQox1wllrX/jH4bh5nJOW5ZxiF8D8O1/cBhGQ==',//应用私钥证书
        "appPrivateKeyFile" => '',
        "apiDomain" => 'https://openapi.alipaydev.com/gateway.do',//api调用接口域名
        "notify_url" => 'http://aqpr8g.natappfree.cc/api/alipay_notify',//回调地址
        "return_url" => 'http://aqpr8g.natappfree.cc/admin',//跳转地址

    ],
    "wx_config" => [
        "appID" => '2016080600177462',//应用ID
        "mch_id" => '',
        "key" => '',
        "notify_url" => 'http://aqpr8g.natappfree.cc/api/wxpay_notify',

    ],
    "pay_order" => [
        "pay_ok" => 1,//已完成
        "wait_pay" => 2,//未完成 待支付
    ],
    "pay_type" => [
        1 => "微信支付",
        2 => "支付宝"
    ],
];
