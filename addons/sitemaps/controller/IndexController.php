<?php
// +------------------------------------------------------------------------------
// | 控制器
// +------------------------------------------------------------------------------
// | Copyright (c) 2023-2099 https://www.hkcms.cn/u/82.html, All rights reserved.
// +------------------------------------------------------------------------------
// | Author: Inspire <1438214726@qq.com>
// +------------------------------------------------------------------------------
declare (strict_types=1);

namespace addons\sitemaps\controller;

use addons\sitemaps\services\SitemapsService;
use app\Request;
use think\facade\Route;
use think\response\Json;

class IndexController
{
    protected $service;

    public function __construct()
    {
        $this->service = app()->make(SitemapsService::class);
    }

    /**
     * 获取索引
     * @return \think\response\Xml
     */
    public function index()
    {
        // 获取栏目
        $category = $this->service->generate('category', function ($page) {
            return (string)Route::buildUrl('sitemapsCategory', ['page'=>$page])->suffix('xml')->domain(true);
        }, [
            ['ismenu', '=', 1],
            ['status','=', 'normal'],
            ['delete_time', '=', null],
            ['model_id', '>', 0]
        ]);
        // 获取内容
        $content = $this->service->generate('archives',function ($page) {
            return (string)Route::buildUrl('sitemapsContent', ['page'=>$page])->suffix('xml')->domain(true);
        },['status'=>'normal', 'delete_time'=>null]);
        // 获取标签
        $tags = $this->service->generate('tags',function ($page) {
            return (string)Route::buildUrl('sitemapsTags', ['page'=>$page])->suffix('xml')->domain(true);
        });
        return xml(array_merge($category, $content, $tags), 200, [], $this->service->getNode());
    }

    /**
     * 栏目地图
     * @param Request $request
     * @return \think\response\Xml
     */
    public function category(Request $request)
    {
        $page = $request->param('page', '', 'intval');
        if (empty($page)) {
            return xml();
        }
        return xml($this->service->category($page), 200, [], $this->service->getNode('map'));
    }

    /**
     * 内容地图
     * @param Request $request
     * @return \think\response\Xml
     */
    public function content(Request $request)
    {
        $page = $request->param('page', '', 'intval');
        if (empty($page)) {
            return xml();
        }
        return xml($this->service->content($page), 200, [], $this->service->getNode('map'));
    }

    /**
     * 标签
     * @param Request $request
     * @return \think\response\Xml
     */
    public function tags(Request $request)
    {
        $page = $request->param('page', '', 'intval');
        if (empty($page)) {
            return xml();
        }
        return xml($this->service->tags($page), 200, [], $this->service->getNode('map'));
    }

    /**
     * 生成xml
     */
    public function generateXml()
    {
        $this->service->generateXml();
        return json(['code' => 200, 'msg' => '生成成功']);
    }
}