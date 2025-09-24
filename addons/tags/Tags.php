<?php
// +----------------------------------------------------------------------
// | HkCms tags 标签
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 广州恒企教育科技有限公司 <admin@hkcms.cn>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace addons\tags;

use app\admin\model\routine\Config;
use think\Addons;
use think\addons\Dir;
use think\facade\Db;
use think\facade\Event;
use think\facade\Route;

class Tags extends Addons
{
    public $menu = [
        [
            'parent_id'=>58,
            'title'=>'Tags manage',
            'name'=>'tags/index',
            'icon'=>'fas fa-tags',
            'child'=>[
                ["title" => 'Add', "name"=>"tags/add", "type" => 0, "is_nav" => 0],
                ["title" => 'Edit', "name"=>"tags/edit", "type" => 0, "is_nav" => 0],
                ["title" => 'Delete', "name"=>"tags/delete", "type" => 0, "is_nav" => 0]
            ]
        ]
    ];

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        // 清理模型字段
        $modelIds = Db::name('model')->where(['controller'=>'Archives','status'=>'normal'])->column('id');
        Db::name('model_field')->whereIn('model_id',$modelIds)->where(['field_name'=>'tags'])->delete();

        return true;
    }

    // 插件升级时的处理(可选)
    public function upgrade()
    {
        $config = Db::name('app')->where(['name'=>'tags'])->value('config');
        if (!empty($config)) {
            $arr = json_decode($config,true);
            if (!empty($arr)) {
                $this->tagsConfigSaveHook($arr);
            }
        }

        return true;
    }

    public function enable()
    {
        // 检测模板是否有tags页面了，没有则使用默认的
        $site = Config::initConfig();
        $theme = $site['index_theme'];

        $path = config('cms.tpl_path').'index'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR;
        if (!file_exists($path.'tags'.DIRECTORY_SEPARATOR.'tags_index.html')) {
            $dir = Dir::instance();
            $addonPath = $this->addon_path.'data'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
            $dir->copyDir($addonPath, $path);
        }

        // 启用模型字段
        $modelIds = Db::name('model')->where(['controller'=>'Archives','status'=>'normal'])->column('id');
        Db::name('model_field')->whereIn('model_id',$modelIds)->where(['field_name'=>'tags'])->update(['status'=>'normal']);

        // 伪静态
        $url_rewrite = Db::name('config')->where(['name'=>'url_rewrite'])->find();
        $array = json_decode($url_rewrite['value'], true);
        if (!in_array('tags/index', $array)) {
            $array = array_merge(['tags/lists'=>'/t/:tag$.html'], $array);
        }
        if (!in_array('tags/index', $array)) {
            $array = array_merge(['tags/index'=>'/t/index$.html'], $array);
        }
        $json = json_encode($array);
        Db::name('config')->where(['name'=>'url_rewrite'])->update(['value'=>$json]);

        \app\admin\model\routine\Config::initConfig(true);
        return true;
    }

    public function disable()
    {
        // 禁用模型字段
        $modelIds = Db::name('model')->where(['controller'=>'Archives','status'=>'normal'])->column('id');
        Db::name('model_field')->whereIn('model_id',$modelIds)->where(['field_name'=>'tags'])->update(['status'=>'hidden']);

        // 清理伪静态
        $url_rewrite = Db::name('config')->where(['name'=>'url_rewrite'])->find();
        $array = json_decode($url_rewrite['value'], true);
        unset($array['tags/index']);
        unset($array['tags/lists']);
        Db::name('config')->where(['name'=>'url_rewrite'])->update(['value'=>json_encode($array)]);
        \app\admin\model\routine\Config::initConfig(true);
        return true;
    }

    /**
     * 插件初始化
     */
    public function addonsInitHook()
    {
        // 加载扩展语言包
        app()->lang->load($this->addon_path.'data'.DIRECTORY_SEPARATOR.$this->app->lang->getLangset().'.php');

//        Route::pattern([
//            'tag' => '[a-zA-Z0-9\-_\x{4e00}-\x{9fa5}]+',
//        ]);

        // 文档模型更新事件
        Event::listen('model.app\admin\model\cms\Archives.AfterUpdate',function ($model){
            $origin = $model->getOrigin();
            $data = $model->getData();
            // 源数据为空，更改的也是空则不处理
            if ((empty($data['tags']) && empty($origin['tags'])) || !isset($data['tags'])) {
                return true;
            } else if (empty($origin['tags']) && !empty($data['tags'])) { // 源数据为空，更改的有数据，则新增
                $oArr = explode(',', $data['tags']);
                foreach ($oArr as $key=>$value) {
                    $info = Db::name('tags')->where(['title'=>$value])->find();
                    if (empty($info)) {
                        $id = Db::name('tags')->insertGetId([
                            'title'=>$value,
                            'total'=>1,
                            'lang'=>$data['lang']??'',
                            'create_time'=>time(),
                            'update_time'=>time()
                        ]);
                    } else {
                        $id = $info['id'];
                        Db::name('tags')->where(['title'=>$value])->inc('total')->update();
                    }
                    Db::name('tags_list')->insert([
                        'tags_id'=>$id,
                        'model_id'=>$data['model_id'],
                        'category_id'=>$data['category_id'],
                        'content_id'=>$data['id'],
                        'content_title'=>$data['title'],
                        'lang'=>$data['lang']??'',
                        'create_time'=>time()
                    ]);
                }
            } else if (!empty($origin['tags']) && empty($data['tags'])) { // 源数据不为空，但是提交上来的空(清空)
                $oArr = explode(',', $origin['tags']);
                foreach ($oArr as $key=>$value) {
                    $info = Db::name('tags')->where(['title'=>$value])->find();
                    if (empty($info)) {
                        continue;
                    } else {
                        if ($info['total']>0) {
                            Db::name('tags')->where(['title'=>$value])->dec('total')->update();
                            Db::name('tags_list')->where(['tags_id'=>$info['id'],'content_id'=>$data['id']])->delete();
                        }
                    }
                }
            } else if (!empty($origin['tags']) && !empty($data['tags'])) { // 源数据不为空，提交上来的也不为空
                $oArr = explode(',', $origin['tags']);
                $arr = explode(',', $data['tags']);

                foreach ($oArr as $key=>$value) {
                    if (!in_array($value, $arr)) {
                        $info = Db::name('tags')->where(['title'=>$value])->find();
                        if (empty($info)) {
                            continue;
                        } else {
                            if ($info['total']>0) {
                                Db::name('tags')->where(['title'=>$value])->dec('total')->update();
                                Db::name('tags_list')->where(['tags_id'=>$info['id'],'content_id'=>$data['id']])->delete();
                            }
                        }
                    }
                }

                foreach ($arr as $key=>$value) {
                    if (!in_array($value, $oArr)) {
                        $info = Db::name('tags')->where(['title'=>$value])->find();
                        if (empty($info)) {
                            $id = Db::name('tags')->insertGetId([
                                'title'=>$value,
                                'total'=>1,
                                'lang'=>$data['lang']??'',
                                'create_time'=>time(),
                                'update_time'=>time()
                            ]);
                        } else {
                            $id = $info['id'];
                            Db::name('tags')->where(['title'=>$value])->inc('total')->update();
                        }
                        Db::name('tags_list')->insert([
                            'tags_id'=>$id,
                            'model_id'=>$data['model_id'],
                            'category_id'=>$data['category_id'],
                            'content_id'=>$data['id'],
                            'content_title'=>$data['title'],
                            'lang'=>$data['lang']??'',
                            'create_time'=>time()
                        ]);
                    }
                }
            }
        });

        // 文档模型新增事件
        Event::listen('model.app\admin\model\cms\Archives.AfterInsert',function ($model){
            $data = $model->getData();
            if (empty($data['tags'])) {
                return true;
            }

            $oArr = explode(',', $data['tags']);
            foreach ($oArr as $key=>$value) {
                $info = Db::name('tags')->where(['title'=>$value])->find();
                if (empty($info)) {
                    $id = Db::name('tags')->insertGetId([
                        'title'=>$value,
                        'total'=>1,
                        'lang'=>$data['lang']??'',
                        'create_time'=>time(),
                        'update_time'=>time()
                    ]);
                } else {
                    $id = $info['id'];
                    Db::name('tags')->where(['title'=>$value])->inc('total')->update();
                }
                Db::name('tags_list')->insert([
                    'tags_id'=>$id,
                    'model_id'=>$data['model_id'],
                    'category_id'=>$data['category_id'],
                    'content_id'=>$data['id'],
                    'content_title'=>$data['title'],
                    'lang'=>$data['lang']??'',
                    'create_time'=>time()
                ]);
            }
        });

        // 文档模型删除事件
        Event::listen('model.app\admin\model\cms\Archives.AfterDelete',function ($model){
            $data = $model->getData();
            if (empty($data['tags'])) {
                return true;
            }
            $oArr = explode(',', $data['tags']);
            foreach ($oArr as $key=>$value) {
                $info = Db::name('tags')->where(['title'=>$value])->find();
                if (empty($info)) {
                    continue;
                } else {
                    if ($info['total']>0) {
                        Db::name('tags')->where(['title'=>$value])->dec('total')->update();
                        Db::name('tags_list')->where(['tags_id'=>$info['id'],'content_id'=>$data['id']])->delete();
                    }
                }
            }
        });

        // 文档模型恢复事件
        Event::listen('model.app\admin\model\cms\Archives.AfterRestore',function ($model){
            $data = $model->getData();
            if (empty($data['tags'])) {
                return true;
            }
            $oArr = explode(',', $data['tags']);
            foreach ($oArr as $key=>$value) {
                $info = Db::name('tags')->where(['title'=>$value])->find();
                if (empty($info)) {
                    $id = Db::name('tags')->insertGetId([
                        'title'=>$value,
                        'total'=>1,
                        'lang'=>$data['lang']??'',
                        'create_time'=>time(),
                        'update_time'=>time()
                    ]);
                } else {
                    $id = $info['id'];
                    Db::name('tags')->where(['title'=>$value])->inc('total')->update();
                }
                Db::name('tags_list')->insert([
                    'tags_id'=>$id,
                    'model_id'=>$data['model_id'],
                    'category_id'=>$data['category_id'],
                    'content_id'=>$data['id'],
                    'content_title'=>$data['title'],
                    'lang'=>$data['lang']??'',
                    'create_time'=>time()
                ]);
            }
        });
    }

    /**
     * 配置保存事件
     * @param $param
     */
    public function tagsConfigSaveHook($param)
    {
        if ($param['tags_model']['value']) {
            $arr = explode(',', $param['tags_model']['value']);
            $modelIds = Db::name('model')->where(['controller'=>'Archives','status'=>'normal'])->column('id');
            foreach ($arr as $key=>$value) {
                $info = Db::name('model_field')->where(['model_id'=>$value,'field_name'=>'tags'])->find();
                if (empty($info)) {
                    Db::name('model_field')->insert([
                        'model_id'=>$value,
                        'field_name'=>'tags',
                        'field_title'=>'TAG标签',
                        'form_type'=>'text',
                        'extend'=>'class="form-control selectpage",data-show-field="title",data-search-field="title",data-key-field="title",data-data="/tags/tags",data-pagination="false",data-multiple="true"',
                        'weigh'=>6,
                        'iscore'=>1,
                        'default_field'=>1,
                        'update_time'=>time(),
                        'create_time'=>time(),
                    ]);
                }
            }

            foreach ($modelIds as $key=>$value) {
                if (!in_array($value, $arr)) {
                    Db::name('model_field')->where(['model_id'=>$value,'field_name'=>'tags'])->delete();
                }
            }
        } else {
            $modelIds = Db::name('model')->where(['controller'=>'Archives','status'=>'normal'])->column('id');
            Db::name('model_field')->where(['field_name'=>'tags'])->whereIn('model_id', $modelIds)->delete();
        }
    }

    /**
     * 路由
     * @param $param
     */
    public function indexRouteHook($param)
    {
//        Route::pattern([
//            'tag' => '[\w\-]+',
//        ]);
    }

    /**
     * 主题更改
     * @param $theme
     */
    public function themeChangeHook($theme)
    {
        $site = Config::initConfig();
        $theme = $site[$theme];

        $path = config('cms.tpl_path').'index'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR;
        if (!file_exists($path.'tags'.DIRECTORY_SEPARATOR.'tags_index.html')) {
            $dir = Dir::instance();
            $addonPath = $this->addon_path.'data'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
            $dir->copyDir($addonPath, $path);
        }
    }
}