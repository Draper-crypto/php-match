<?php
return [
    'changefreq_home' => [
        'title' => '首页频率',
        'type' => 'select',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            "always"=>'经常',
            "hourly"=>'每小时',
            "daily"=>'每天',
            "weekly"=>'每周',
            "monthly"=>'每月',
            "yearly"=>'每年',
            "never"=>'从不',
        ],
        'value' => 'always'
    ],
    'changefreq_category' => [
        'title' => '栏目频率',
        'type' => 'select',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            "always"=>'经常',
            "hourly"=>'每小时',
            "daily"=>'每天',
            "weekly"=>'每周',
            "monthly"=>'每月',
            "yearly"=>'每年',
            "never"=>'从不',
        ],
        'value' => 'daily'
    ],
    'changefreq_content' => [
        'title' => '内容频率',
        'type' => 'select',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            "always"=>'经常',
            "hourly"=>'每小时',
            "daily"=>'每天',
            "weekly"=>'每周',
            "monthly"=>'每月',
            "yearly"=>'每年',
            "never"=>'从不',
        ],
        'value' => 'hourly'
    ],
    'changefreq_tags' => [
        'title' => '标签频率',
        'type' => 'select',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            "always"=>'经常',
            "hourly"=>'每小时',
            "daily"=>'每天',
            "weekly"=>'每周',
            "monthly"=>'每月',
            "yearly"=>'每年',
            "never"=>'从不',
        ],
        'value' => 'hourly'
    ],
    'priority_home' => [
        'title' => '首页级别',
        'type' => 'select',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            '0.1'=>'0.1',
            '0.2'=>'0.2',
            '0.3'=>'0.3',
            '0.4'=>'0.4',
            '0.5'=>'0.5',
            '0.6'=>'0.6',
            '0.7'=>'0.7',
            '0.8'=>'0.8',
            '0.9'=>'0.9',
            '1.0'=>'1.0',
        ],
        'value' => '1.0'
    ],
    'priority_category' => [
        'title' => '栏目级别',
        'type' => 'select',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            '0.1'=>'0.1',
            '0.2'=>'0.2',
            '0.3'=>'0.3',
            '0.4'=>'0.4',
            '0.5'=>'0.5',
            '0.6'=>'0.6',
            '0.7'=>'0.7',
            '0.8'=>'0.8',
            '0.9'=>'0.9',
            '1.0'=>'1.0',
        ],
        'value' => '0.8'
    ],
    'priority_content' => [
        'title' => '内容级别',
        'type' => 'select',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            '0.1'=>'0.1',
            '0.2'=>'0.2',
            '0.3'=>'0.3',
            '0.4'=>'0.4',
            '0.5'=>'0.5',
            '0.6'=>'0.6',
            '0.7'=>'0.7',
            '0.8'=>'0.8',
            '0.9'=>'0.9',
            '1.0'=>'1.0',
        ],
        'value' => '0.8'
    ],
    'priority_tags' => [
        'title' => '标签级别',
        'type' => 'select',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            '0.1'=>'0.1',
            '0.2'=>'0.2',
            '0.3'=>'0.3',
            '0.4'=>'0.4',
            '0.5'=>'0.5',
            '0.6'=>'0.6',
            '0.7'=>'0.7',
            '0.8'=>'0.8',
            '0.9'=>'0.9',
            '1.0'=>'1.0',
        ],
        'value' => '0.8'
    ],
    'page_category' => [
        'title' => '栏目分页',
        'type' => 'text',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'value' => '1000'
    ],
    'page_content' => [
        'title' => '内容分页',
        'type' => 'text',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'value' => '1000'
    ],
    'page_tags' => [
        'title' => '标签分页',
        'type' => 'text',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'value' => '1000'
    ],
    'filepath' => [
        'title' => 'xml保存位置',
        'type' => 'text',
        'tips' => '',
        'rules' => '',
        'error_tips'=>'',
        'value' => 'map'
    ]
];