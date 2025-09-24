<?php
// +------------------------------------------------------------------------------
// | 插件路由
// +------------------------------------------------------------------------------
// | Copyright (c) 2023-2099 https://www.hkcms.cn/u/82.html, All rights reserved.
// +------------------------------------------------------------------------------
// | Author: Inspire <1438214726@qq.com>
// +------------------------------------------------------------------------------

use \think\facade\Route;

Route::group('sitemaps', function (){
    // 站点地图汇总，即索引
    Route::get('index', '\\think\\addons\\Route::execute')
        ->name('sitemapsIndex')
        ->completeMatch(true)
        ->append([
            'controller' => 'IndexController', // 插件控制器
            'action' => 'index' // 插件方法
        ])->ext('xml');

    // 栏目
    Route::get('category/:page', '\\think\\addons\\Route::execute')
        ->name('sitemapsCategory')
        ->completeMatch(true)
        ->append([
            'controller' => 'IndexController', // 插件控制器
            'action' => 'category' // 插件方法
        ])->ext('xml');

    // 内容
    Route::get('content/:page', '\\think\\addons\\Route::execute')
        ->name('sitemapsContent')
        ->completeMatch(true)
        ->append([
            'controller' => 'IndexController', // 插件控制器
            'action' => 'content' // 插件方法
        ])->ext('xml');

    // 标签
    Route::get('tags/:page', '\\think\\addons\\Route::execute')
        ->name('sitemapsTags')
        ->completeMatch(true)
        ->append([
            'controller' => 'IndexController', // 插件控制器
            'action' => 'tags' // 插件方法
        ])->ext('xml');

    // 生成XML
    Route::get('generateXml', '\\think\\addons\\Route::execute')
        ->name('generateXml')
        ->completeMatch(true)
        ->append([
            'controller' => 'IndexController', // 插件控制器
            'action' => 'generateXml' // 插件方法
        ])->ext('html');
})->append([
    'addon' => 'sitemaps', // 插件标识
])->completeMatch(true); // 路由地址完整匹配

return [
];