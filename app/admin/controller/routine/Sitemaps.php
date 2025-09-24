<?php
// +------------------------------------------------------------------------------
// | 控制器
// +------------------------------------------------------------------------------
// | Copyright (c) 2023-2099 https://www.hkcms.cn/u/82.html, All rights reserved.
// +------------------------------------------------------------------------------
// | Author: Inspire <1438214726@qq.com>
// +------------------------------------------------------------------------------
declare (strict_types=1);
namespace app\admin\controller\routine;

use addons\sitemaps\services\SitemapsService;
use app\admin\controller\BaseController;

class Sitemaps extends BaseController
{
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        'login', // 登录中间件
        'auth'=>['except'=>['generate']],  // 权限认证中间件
    ];

    /**
     * 服务层
     * @var SitemapsService
     */
    protected $service;

    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->service = app()->make(SitemapsService::class);
    }

    /**
     * 生成xml
     * @return void
     */
    public function generateXml()
    {
        $this->service->generateXml();
        $this->success();
    }
}