<?php
// +------------------------------------------------------------------------------
// | 服务层
// +------------------------------------------------------------------------------
// | Copyright (c) 2023-2099 https://www.hkcms.cn/u/82.html, All rights reserved.
// +------------------------------------------------------------------------------
// | Author: Inspire <1438214726@qq.com>
// +------------------------------------------------------------------------------
declare (strict_types=1);

namespace addons\sitemaps\services;

use app\admin\model\cms\Archives;
use app\admin\model\Tags;
use app\common\services\BaseService;
use app\index\model\cms\Category;
use think\facade\Db;

class SitemapsService extends BaseService
{
    public $config = [
        // 分页大小
        'page'=>5000,
    ];

    /**
     * 获取xml基础结构
     * @param string $type
     * @return string[]
     */
    public function getNode(string $type = 'index'): array
    {
        if ($type=='index') { // 索引
            return [
                'item_key'  => '',
                'root_node' => 'sitemapindex',
                'item_node' => 'sitemap',
                'root_attr' => 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
            ];
        } else {
            return [
                'item_key'  => '',
                'root_node' => 'urlset',
                'item_node' => 'url',
                'root_attr' => 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/"'
            ];
        }
    }

    /**
     * 生成sitemap索引
     * @param string $table
     * @param callable $url
     * @param array $where
     * @return array
     */
    public function generate(string $table, callable $url, array $where = []): array
    {
        $config = get_addons_config('addons','sitemaps');
        $lists = [];
        $count = Db::name($table)
            ->where($where)
            ->count();
        if ($count) {
            $page = ceil($count / (int)$config['page_category']);
            for ($i=1; $i<=$page; $i++) {
                $lists[] = ['loc'=>$url($i)];
            }
        }
        return $lists;
    }

    /**
     * 获取栏目数据
     * @param int $page
     * @return array
     */
    public function category(int $page): array
    {
        // 获取插件配置
        $config = get_addons_config('addons','sitemaps');
        $data = Category::where([
                ['ismenu', '=', 1],
                ['status','=', 'normal'],
                ['model_id', '>', 0]
            ])
            ->page($page, (int)$config['page_category'])
            ->order('update_time desc')
            ->field('id,model_id,parent_id,type,url,ismenu,lang,weigh,status,update_time,name')
            ->select();
        $lists = [];
        // 首页放到栏目页
        if (1==$page) {
            $domain = site('cdn_url');
            $lists[] = [
                'loc'=>$domain ?: app('request')->domain(),
                'lastmod'=>date('Y-m-d'),
                'changefreq'=>$config['changefreq_home'],
                'priority'=>$config['priority_home'],
            ];
        }
        foreach ($data as $item) {
            $lists[] = [
                'loc'=>str_replace('&', '&amp;', $item->fullurl),
                'lastmod'=>date('Y-m-d', $item->getData('update_time')),
                'changefreq'=>$config['changefreq_category'],
                'priority'=>$config['priority_category'],
            ];
        }
        return $lists;
    }

    /**
     * 获取文章
     * @param int $page
     * @return array
     */
    public function content(int $page): array
    {
        $config = get_addons_config('addons','sitemaps');
        $data = Archives::where(['status'=>'normal'])
            ->page($page, (int)$config['page_content'])
            ->order('publish_time desc')
            ->field('url,id,category_id,model_id,url,diyname,lang,status,publish_time')
            ->select();
        $lists = [];
        foreach ($data as $item) {
            $lists[] = [
                'loc'=>str_replace('&', '&amp;', $item->fullurl),
                'lastmod'=>date('Y-m-d', $item->getData('publish_time')),
                'changefreq'=>$config['changefreq_content'],
                'priority'=>$config['priority_content'],
            ];
        }
        return $lists;
    }

    /**
     * 获取标签
     * @param int $page
     * @return array
     */
    public function tags(int $page): array
    {
        $config = get_addons_config('addons','sitemaps');
        $data = Tags::page($page, (int)$config['page_tags'])
            ->order('update_time desc')
            ->field('lang,update_time,weigh,title,id')
            ->select();

        $lists = [];
        foreach ($data as $item) {
            $lists[] = [
                'loc'=>str_replace('&', '&amp;', !empty($item->fullurl) ? $item->fullurl : cdn_url($item->url, true)),
                'lastmod'=>date('Y-m-d', $item->getData('update_time')),
                'changefreq'=>$config['changefreq_tags'],
                'priority'=>$config['priority_tags'],
            ];
        }
        return $lists;
    }

    /**
     * 生成xml
     * @return void
     */
    public function generateXml()
    {
        // 获取插件配置
        $config = get_addons_config('addons','sitemaps');
        // 保存目录
        $rootPath = public_path($config['filepath']);
        if (!is_dir($rootPath)) {
            mkdir($rootPath, 0777, true);
        }
        // 获取栏目
        $category = $this->generate('category', function ($page) use ($config) {
            return cdn_url('/'.$config['filepath'].'/category_'.$page.'.xml', true);
        }, [
            ['ismenu', '=', 1],
            ['status','=', 'normal'],
            ['delete_time', '=', null],
            ['model_id', '>', 0]
        ]);
        // 获取内容
        $content = $this->generate('archives', function ($page) use ($config) {
            return cdn_url('/'.$config['filepath'].'/archives_'.$page.'.xml', true);
        },['status'=>'normal', 'delete_time'=>null]);
        // 获取标签
        $tags = $this->generate('tags', function ($page) use ($config) {
            return cdn_url('/'.$config['filepath'].'/tags_'.$page.'.xml',true);
        });
        $xml = xml(array_merge($category, $content, $tags), 200, [], $this->getNode());
        file_put_contents($rootPath.'index.xml', $xml->getContent());
        // 生成子页sitemap
        $page = 1;
        while (true) {
            $lists = $this->category($page);
            if (empty($lists)) {
                break;
            }
            $xml = xml($lists, 200, [], $this->getNode());
            file_put_contents($rootPath.'category_'.$page.'.xml', $xml->getContent());
            $page ++;
        }
        $page = 1;
        while (true) {
            $lists = $this->content($page);
            if (empty($lists)) {
                break;
            }
            $xml = xml($lists, 200, [], $this->getNode());
            file_put_contents($rootPath.'archives_'.$page.'.xml', $xml->getContent());
            $page ++;
        }
        $page = 1;
        while (true) {
            $lists = $this->tags($page);
            if (empty($lists)) {
                break;
            }
            $xml = xml($lists, 200, [], $this->getNode());
            file_put_contents($rootPath.'tags_'.$page.'.xml', $xml->getContent());
            $page ++;
        }
    }
}