<?php

//公共码

return [
    'msg' => [
        'ok' => 'successful',
        'fail' => 'fail',
        'register_ok' => '注册成功',
        'register_fail' => '注册失败',
        'request_method_post'=>'请求方式必须为post',
    ],
    'code' => [
        'ok' => 200,
        'fail' => -1,
        'not_found' => 403,
        'serve_error' => 502,
    ],
    'redis_prefix'=>[
        "token"=>'laravel_user_token:',
    ]
];
