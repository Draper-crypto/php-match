<?php
return [
    'tags_model' => [
        'title' => '应用到模型',
        'type' => 'text',
        'tips' => '勾选模型生效。',
        'rules' => '',
        'error_tips' => '',
        'value' => '',
    ],
    'tags' => [
        'title' => 'tags主页',
        'type' => 'text',
        'tips' => '主页模板在主题tags文件夹下的tags_index.html文件, 如果主题有提供tags/tags_index.html，则使用主题的。',
        'rules' => 'required',
        'error_tips' => '值必须',
        'value' => 'tags_index',
    ],
    'tags_list' => [
        'title' => 'tags列表',
        'type' => 'text',
        'tips' => '标签列表页模板在主题tags文件夹下的tags_list.html文件, 如果主题有提供tags/tags_list.html，则使用主题的。',
        'rules' => 'required',
        'error_tips' => '值必须',
        'value' => 'tags_list',
    ],
    'seo_title' => [
        'title' => 'SEO标题',
        'type' => 'text',
        'tips' => '体现在标签首页',
        'rules' => '',
        'error_tips' => '',
        'value' => '',
    ],
    'seo_keyword' => [
        'title' => 'SEO关键字',
        'type' => 'text',
        'tips' => '体现在标签首页',
        'rules' => '',
        'error_tips' => '',
        'value' => '',
    ],
    'seo_desc' => [
        'title' => 'SEO描述',
        'type' => 'text',
        'tips' => '体现在标签首页',
        'rules' => '',
        'error_tips' => '',
        'value' => '',
    ]
];