<?php
// +----------------------------------------------------------------------
// | HkCms
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 广州恒企教育科技有限公司 <admin@hkcms.cn>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\cms\Model;
use app\admin\model\TagsList;
use think\facade\Db;

class Tags extends BaseController
{
    protected $middleware = [
        'login',
        'auth' => ['except'=>['getModelList','tags','reset']]
    ];

    /**
     * 文章模型
     * @var \app\admin\model\Tags
     */
    protected $model;

    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\admin\model\Tags;
    }

    /**
     * 列表显示
     * @return \think\response\Json
     */
    public function index()
    {
        if ($this->request->isAjax()) {

            if ($this->request->param('searchTable')) {
                return $this->selectPage(); // 判断请求。如果是动态下拉组件请求，则交接给selectPage方法
            }

            list($map, $limit, $offset, $order, $sort) = $this->buildparams();

            $data = $this->model->where($map)->order($sort, $order)->limit($offset, $limit)->select()->append(['url'])->toArray();
            $total = $this->model->where($map)->order($sort, $order)->count();
            return json(['total'=>$total,'rows'=>$data]);
        }
        $list = Model::where(['controller'=>'Archives','status'=>'normal'])->select()->toArray();
        $this->view->assign('models', $list);
        return $this->view->fetch();
    }

    /**
     * 标签添加
     * @return mixed|string|void
     * @throws \Exception
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $titles = $this->request->post('title');
            $arr = explode("\r\n", $titles);
            if (empty($arr)) {
                $this->error(lang('Parameter is empty'));
            }

            $data = [];
            foreach ($arr as $key=>$value) {
                $data[] = ['title'=>$value,'lang'=>$this->contentLang];
            }

            try {
                $result = $this->model->saveAll($data);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            if ($result !== false) {
                $this->success();
            } else {
                $this->error(lang('No rows added'));
            }
        }
        return $this->view->fetch();
    }

    /**
     * 删除
     * @param string $ids
     */
    public function del($ids = '')
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $list = $this->model->where($pk, 'in', $ids)->select();

            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {
                    TagsList::where(['tags_id'=>$v['id']])->delete();
                    $count += $v->delete();
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(lang('No rows deleted'));
            }
        }
        $this->error(lang('%s not null',['ids']));
    }

    /**
     * 重置标签
     * @param null $id
     */
    public function reset($id = null)
    {
        if (empty($id)) {
            $this->error(lang('Parameter is empty'));
        }
        $page = $this->request->param('page');

        $list = controller($id, function ($obj, $model, $category) {
            return $obj->where('tags','<>','')->paginate(1)->toArray();
        });

        // 清空原始数据
        if ($page==1) {
            $tagsIds = TagsList::where(['model_id'=>$id])->column('tags_id');
            TagsList::where(['model_id'=>$id])->delete();
            $this->model->whereIn('id', $tagsIds)->delete();
        }

        foreach ($list['data'] as $key=>$value) {
            if (!empty($value['tags'])) {
                $arr = explode(',', $value['tags']);
                foreach ($arr as $k=>$v) {
                    $info = $this->model->where(['title'=>$v])->find();
                    if (!empty($info)) {
                        $gid = $info['id'];
                    } else {
                        $gid = $this->model->insertGetId(['title'=>$v,'create_time'=>time(),'update_time'=>time(),'lang'=>$value['lang']??'']);
                    }
                    TagsList::insert([
                        'tags_id'=>$gid,
                        'model_id'=>$value['model_id'],
                        'category_id'=>$value['category_id'],
                        'content_id'=>$value['id'],
                        'content_title'=>$value['title'],
                        'lang'=>$value['lang']??'',
                        'create_time'=>time()
                    ]);
                    Db::name('tags')->where(['id'=>$gid])->inc('total')->update();
                }
            }
        }

        $this->success('','',['last_page'=>$list['last_page']]);
    }

    /**
     * 获取模型列表
     */
    public function getModelList()
    {
        $list = Model::where(['controller'=>'Archives','status'=>'normal'])->select()->toArray();
        $this->success('','',$list);
    }

    /**
     * 标签
     * @return \think\response\Json
     */
    public function tags()
    {
        $searchValue = $this->request->param('searchValue', '');
        $arr = $this->request->param('searchField');
        if ($arr) {
            $name = $this->request->param($arr[0], '');
        }
        if (!empty($searchValue)) {
            $searchValue = explode(',', $searchValue);
            $arr = [];
            foreach ($searchValue as $key=>$value) {
                $arr[] = ['title'=>$value];
            }
            return json(['rows'=>$arr]);
        }
        if (empty($name)) {
            $rows = $this->model->limit(15)->order('create_time', 'desc')->where(['lang'=>$this->contentLang])->select()->toArray();
            return json(['rows'=>$rows]);
        }

        $data = $this->model->where('title','like',"%$name%")->where(['lang'=>$this->contentLang])->column('title');
        if (!in_array($name, $data)) {
            array_push($data, $name);
        }
        $arr = [];
        foreach ($data as $key=>$value) {
            $arr[] = ['title'=>$value];
        }
        return json(['rows'=>$arr]);
    }
}