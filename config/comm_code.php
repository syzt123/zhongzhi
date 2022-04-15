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
    'local_url' => 'https://pay.zjzc88.com',//
    "ali_config" => [
        "appID" => '2017022805954738',//应用ID
        "sign_type" => 'RSA2',//加密方式
        "appPrivateKey" => 'MIIEowIBAAKCAQEAtQJndSgpoG2YELCBkpLMrUGfFL83nzaUPXaCJSmBdkxfO54JVJf8Z30aIQtj2lxLOC6g40j+YDFaLFeF3M3I2xg0RIr+8hd4IiuUfblyQ015sSOvalBOQlzl++G5m8VyaMpr/1GHBULjv0Hgik2zFG1HJGB0pBCju7h4v/XYGBTNUZfgNodcgjRnDdmYjl7c/0DOWsIrH90CtWuwQWzz/WtWYpoig5nN2B5cRjIVr+MIv2tBaNebqV0ZuTpBPavbagUKFuhcI9JFivP2S40frgQvkxegImB+N2TtY0IOjY9Mz2orZHHYcDdl2xykzkdaSu2dPOqVALGY2vFMgtBY3QIDAQABAoIBAASLG+ev62OQfKmmlIoGT0bmB8Uwi4feidgFGn0uToaWoilP7TIHYpmCZV6A96+qc2Tknrs1wNTPSFEmnUCOlcUSFXXG+2E+P5C0AVUqi3ivACf3GlcTzvMRe+BXqR4E1btVoWJWPIpgRHEviSBPlPglSgzasvLPTBzRI47F7/eiuyXvduf+b88JrfJfp05SnHHhvHIevlulnf2jAV1tN6mqJvmsXYI4sY4P2ky7OlQOuvdqVbZ8rsoRrfEen5p5qjc2x19HziH0oActRFqkq1Rb6fQs/kd/vvYl0QOAmN4th/2PLRzg760bvAnZerlEkFRuSvGBX8rlyjOznR9OMy0CgYEA8uQ98VhWnvjV/96Cg9KuzSjDaF+r+RIYZYKHpgHvf0gCvuzEGBcBlg61flO40V+y+J8XLH4WMpmIL3RekfUW9GFcDiupyvuCprDQAFA80nsLRiK74+kFAf+vPPb46q0dKBDYpkeSqVAhWXzeOlruUdHX9dv6fc3pobaORj4ZDZsCgYEAvsc0w4iLV/8ZduUDt4TEBI5ofm+ecmvEq6HZxMXbmA2s91ntM0MDApoJaerEHL2U5yy7PWggTCeRrynY7TTGmCbaEdpA/iGAVUXw/U9YtZ4oo7SvY8DmsjxB4WUIMAzkWD3XmfUuJ34slz+pPVv1pw3DbTUAsigbrNVb5KqZVucCgYBTqNPBBglH1jN3Xv+bQfzdQzYTBCjqsBXhGNV6E16Xe4kek9Ry67GrKsPOkC2vSAQP6FQGCiPBJ+qlVbKhGUbfw4z+gIGKHZkBqxLpmLlqUeEvNhV7Sa5k4tlL6VERG7FYNH06wJo+YRArj3vHo7xESD/XOf6MSFk5TvSwMwFUEwKBgQCQWETx1PQpRlhL/wcK6acuE1m4oFdwF14cxj7whQubRm2iUSYJbSBv7YBF5V0wqbhqGQwqcAhP1niB4dXB0/aW1H6Wl14pacuhuWOXJVvnPp1dD67MeP2TycfG9Bx3zqlOoqvoTvv24Z53abFxPYzmMqG0lS/LnFEkOeJnZzTR7QKBgB8c5HyR8vo6Upv2DFPwu2kzqja88v+B0nS8VGZweeVM5JBCj6ecj639Ldx+clUH1GDozI8/7gMGt+b07TXlSl0MCZFkw5Ht95iKwuVIAHo+kbcT+esiQ6f8i1FhwpCku0vGFj9QMfrbQ+WWWhSAMngzrh5fkqNxdrXglD45TfUc',//应用私钥证书
        "appPrivateKeyFile" => '',
        "apiDomain" => 'https://openapi.alipay.com/gateway.do',//api调用接口域名
        "notify_url" => 'https://pay.zjzc88.com/api/alipay_notify',//回调地址
        "return_url" => 'https://pay.zjzc88.com/admin',//跳转地址

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
