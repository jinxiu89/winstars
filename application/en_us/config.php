<?php
return [
    'url_route_on' =>true,
    'url_route_must'=>  true,
    'APP_TOKEN_MD5'       =>'akdasjf;kjasd;f@a;dskf___asdflsadfienasdkljf',//这个地址不能修改 了
    'LANG_SWITCH_ON' => true,
    'default_lang'           => 'en-us',
    'http_exception_template'    =>  [
        // 定义404错误的重定向页面地址
        404 =>  APP_PATH.'common/views/404.html',
        // 还可以定义其它的HTTP status
        401 =>  APP_PATH.'401.html',
        500 => APP_PATH.'/common/views/500.html'
    ],
];