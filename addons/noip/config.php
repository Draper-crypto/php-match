<?php

return [
    'base' => [
        'title' => '基础配置',
        'item' => [
            'ip' => [
                'title' => '禁止IP',
                'type' => 'textarea',
                'tips' => '请使用|隔开',
                'rules' => '',
                'error_tips' => '',
                'value' => '',
            ],
            'url'=>[
                'title' => '默认跳转的地址',
                'type' => 'text',
                'tips' => '默认跳转的地址',
                'rules' => '',
                'error_tips' => '',
                'value' => 'http://www.baidu.com',
              ],
			  'status'=>[
			      'title' => '是否开启',
			      'type' => 'radio',
			      'tips' => '是否开启',
			      'rules' => '',
			      'error_tips' => '',
				  'options' => [  // 选项
				    '1' => '开启',
				    '0' => '关闭'
				  ],
			      'value' => '1',
			    ],
        ],
       
    ],
     
];
