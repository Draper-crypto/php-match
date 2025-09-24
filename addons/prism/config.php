<?php
return [
    'font-size' => [
        'title' => '字体大小',
        'type' => 'text',
        'tips' => '',
        'rules' => '',
        'error_tips' => '',
        'value' => '16'
    ],
    'theme' => [
        'title' => '主题',
        'type' => 'select',
        'tips' => '',
        'rules' => 'required',
        'error_tips' => '',
        'options' => [
            'prism' => 'Default',
            'prism_dark' => 'Dark',
            'prism_funky' => 'Funky',
            'prism_okaidia' => 'Okaidia',
            'prism_twilight' => 'Twilight',
            'prism_coy' => 'Coy',
            'prism_solarized_light' => 'Solarized Light',
            'prism_tomorrow_night' => 'Tomorrow Night',
        ],
        'value' => 'prism',
    ],
    'is_line' => [
        'title' => '自动显示行号',
        'type' => 'radio',
        'tips' => '自动显示行号，手动可自行在pre标签增加class:line-numbers',
        'rules' => '',
        'error_tips' => '',
        'options' => [
            0=>'否',
            1=>'是'
        ],
        'value' => '1'
    ],
    'line_enter' => [
        'title' => '行号自动换行',
        'type' => 'radio',
        'tips' => '自动显示行号时有效',
        'rules' => '',
        'error_tips' => '',
        'options' => [
            0=>'否',
            1=>'是'
        ],
        'value' => '1'
    ],
];