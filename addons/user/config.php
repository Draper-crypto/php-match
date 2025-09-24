<?php
return [
    'register_captcha_type' => [
        'title' => '注册验证码类型',
        'type' => 'radio',
        'tips' => '短信验证码需要安装短信插件',
        'rules' => 'checkbox',
        'error_tips' => '',
        'options' => [
            1 => '文字',
            2 => '邮箱',
            3 => '手机',
        ],
        'value' => '2',
    ],
    'login_captcha' => [
        'title' => '登录验证码',
        'type' => 'radio',
        'tips' => '',
        'rules' => 'checkbox',
        'error_tips' => '',
        'options' => [
            1 => '开启',
            2 => '关闭',
        ],
        'value' => '2',
    ],
    'driver' => [
        'title' => 'Token驱动方式',
        'type' => 'radio',
        'tips' => '',
        'rules' => 'checkbox',
        'error_tips' => '',
        'options' => [
            'MySql' => '数据库',
            'Redis' => 'redis',
        ],
        'value' => 'MySql',
    ],
    'host' => [
        'title' => 'redis服务',
        'type' => 'text',
        'tips' => '驱动方式为redis时生效',
        'error_tips' => '',
        'rules' => '',
        'value' => '127.0.0.1'
    ],
    'port' => [
        'title' => 'redis端口',
        'type' => 'text',
        'tips' => '',
        'error_tips' => '',
        'rules' => '',
        'value' => '6379'
    ],
    'password' => [
        'title' => 'redis密码',
        'type' => 'text',
        'tips' => '',
        'error_tips' => '',
        'rules' => '',
        'value' => ''
    ]
];