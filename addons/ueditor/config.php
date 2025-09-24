<?php
return [
    'isformat' => [
        'title' => '自动格式化',
        'type' => 'radio',
        'tips' => '开启后前台根据提供的class或id，格式化百度编辑器的内容，注意模板必须有{:hook("index_footer")}',
        'rules' => '',
        'error_tips'=>'',
        'options' => [
            '1' => '开启',
            '0' => '关闭'
        ],
        'value' => '0'
    ],
    'classid' => [
        'title' => '类名或id',
        'type' => 'text',
        'tips' => '例如：.content或#content',
        'rules' => '',
        'error_tips'=>'',
        'value' => ''
    ],
];