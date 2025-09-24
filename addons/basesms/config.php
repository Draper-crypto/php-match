<?php

return [
    'driver' => [
        'title' => '短信平台',
        'type' => 'select',
        'tips' => '',
        'rules' => 'checkbox',
        'error_tips' => '',
        'options' => [
            'aliyun' => '阿里云短信',
            'aliyunrest' => '阿里云Rest',
            'qcloud' => '腾讯云短信',
            'huyi' => '互亿无线',
            'yunpian' => '云片',
            'submail' => 'Submail短信',
            'luosimao' => '螺丝帽',
            'juhe' => '聚合数据'
        ],
        'value' => 'aliyun',
    ],
    'app_id' => [
        'title' => 'api_id',
        'type' => 'text',
        'tips' => 'sdk_app_id、api_id',
        'error_tips' => '',
        'rules' => '',
        'value' => ''
    ],
    'project' => [
        'title' => '项目ID',
        'type' => 'text',
        'tips' => 'project',
        'error_tips' => '',
        'rules' => '',
        'value' => ''
    ],
    'app_key' => [
        'title' => 'app_key',
        'type' => 'text',
        'tips' => 'app_key、access_key_secret',
        'error_tips' => '',
        'rules' => '',
        'value' => ''
    ],
    'sign_name' => [
        'title' => '签名',
        'type' => 'text',
        'tips' => 'sign_name、signature、project',
        'error_tips' => '',
        'rules' => '',
        'value' => ''
    ],
    'template_id' => [
        'title' => '模板ID列表',
        'type' => 'textarea',
        'tips' => '格式：【事件名称|模板ID】。已有事件：default,change_mobile,register,reset_pwd',
        'error_tips' => '',
        'rules' => '',
        'value' => ''
    ],
    'content' => [
        'title' => '文字内容',
        'type' => 'textarea',
        'tips' => '',
        'error_tips' => '',
        'rules' => '',
        'value' => ''
    ],
    'max_expire' => [
        'title' => '短信过期时间',
        'type' => 'text',
        'tips' => '秒',
        'error_tips' => '',
        'rules' => '',
        'value' => '1800'
    ]
];
