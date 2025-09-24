<?php

declare (strict_types=1);

namespace app\admin\controller\excel;

use app\admin\controller\BaseController;
use addons\excel\Excelclass;
use libs\Tree;
use think\facade\Db;

class Index extends BaseController
{

    protected $middleware = [
        'login',
        'auth' => ['only'=>['index']]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\excel\Excel;
        $this->Categorymodel = new \app\admin\model\cms\Category;
        $this->Modelmodel = new \app\admin\model\cms\Model;
    }

    public function index()
    {

        if ($this->request->isAjax()) {
            $type = $this->request->param('type');
            if ($type == 'category') {
                $category_id = input('category_id');
                $model_id = $this->Categorymodel->where('id',$category_id)->value('model_id');

                $list = $this->Modelmodel->where('id', $model_id)->field('tablename,type')->find();
                $prefix = $this->config->get('database')['connections']['mysql']['prefix'];

                $tablename = $prefix.'archives';
                $table = Db::query("show FULL COLUMNS FROM ".$tablename);


                $tabledataname = $prefix.$list['tablename'];
                $tabledata = Db::query("show FULL COLUMNS FROM ".$tabledataname);
                foreach ($tabledata as $k => $v) {
                    if ($v['Field'] == 'id') {
                        unset($tabledata[$k]);
                    }
                }
                $table = array_merge($table,$tabledata);

                return json(['total'=>count($table),'rows'=>$table,'model_id'=>$model_id]);
            } else if ($type == 'excel_list') {
                $list = $this->model->alias('e')
                        ->leftJoin('category c','e.category_id = c.id')
                        ->leftJoin('admin a','e.admin_id = a.id')
                        ->field('e.id,e.title,e.create_time,c.title as category_name,a.nickname as admin_name')
                        ->order('e.create_time desc')
                        ->select();

                return json(['total'=>count($list),'rows'=>$list]);
            }

        }
        $lang = $this->request->param('clang', $this->contentLang);
        $category_list = $this->getCateList($lang);
        $this->view->assign('category_list',$category_list);
        return $this->view->fetch();
    }

    /**
     * 获取栏目数据，有限
     * @param $lang
     * @return array
     */
    private function getCateList($lang)
    {
        $model = $this->Categorymodel
                ->where('lang','=',$lang)
                ->field('id,model_id,parent_id,type,title');
        if (!$this->user->hasSuperAdmin()) { // 判断是否是超级管理员
            $group = $this->user->getUserGroupId();
            $categoryIdArr = Db::name('category_priv')->whereIn('auth_group_id', $group)->column('category_id');
            $data = [];
            if (!empty($categoryIdArr)) {
                $data = $model->whereIn('id', $categoryIdArr)->order(['weigh'=>'asc','id'=>'asc'])->select()->append(['type_text'])->toArray();
            }
        } else {
            $data = $model->order(['weigh'=>'asc','id'=>'asc'])->select()->append(['type_text'])->toArray();
        }

        return Tree::instance()->getTreeList(Tree::instance()->init($data)->getTreeArray(0));
    }

    /**
     * 文档导出
     * @return \think\response\Json
     */
    public function export()
    {
        if ($this->request->isAjax()) {
            $row = $this->request->param('row');
            if (empty($row['model_id'])) return json(['code'=>0,'msg'=>lang('No MODEL_ID found, please re-select the column')]);
            if (empty($row['category_id'])) return json(['code'=>0,'msg'=>lang('Please select category')]);
            if (empty($row['name'])) return json(['code'=>0,'msg'=>lang('Please enter a document name')]);
            if (!isset($row['field']) || empty($row['field']) || !is_array($row['field'])) return json(['code'=>0,'msg'=>lang('Please select exported data')]);

            $model_list = $this->Modelmodel->where('id', $row['model_id'])->field('tablename,type')->find();
            $prefix = $this->config->get('database')['connections']['mysql']['prefix'];
            $tabledataname = $prefix.'archives';
            $table = Db::query("show FULL COLUMNS FROM ".$tabledataname);
            $title = [];
            foreach ($table as $k => $v) {
                if (in_array($v['Field'],$row['field'])) {
                    $title[$v['Field']]['name'] = empty($v['Comment'])?$v['Field']:$v['Comment'];
                    $strposval = strpos($v['Type'], '(');
                    if ($strposval) {
                        $type = substr($v['Type'],0,$strposval);
                        $title[$v['Field']]['type'] = $type;
                        $typeval = substr($v['Type'],$strposval+1,strpos($v['Type'], ')')-$strposval-1);
                        if ($type == 'enum') {
                            $title[$v['Field']]['typeval'] = str_replace("'","",$typeval);

                        } else {
                            $title[$v['Field']]['typeval'] = $typeval;
                        }
                    } else {
                        $title[$v['Field']]['type'] = $v['Type'];
                        $title[$v['Field']]['typeval'] = "";
                    }
                }
            }

            $tabledata = Db::query("show FULL COLUMNS FROM ".$prefix.$model_list['tablename']);
            $type = false;
            $dataField = [];
            foreach ($tabledata as $k => $v) {
                if (in_array($v['Field'],$row['field']) && $v['Field'] != 'id') {
                    $title[$v['Field']]['name'] = empty($v['Comment'])?$v['Field']:$v['Comment'];
                    $strposval = strpos($v['Type'], '(');
                    if ($strposval) {
                        $type = substr($v['Type'],0,$strposval);
                        $title[$v['Field']]['type'] = $type;
                        $typeval = substr($v['Type'],$strposval+1,strpos($v['Type'], ')')-$strposval-1);
                        if ($type == 'enum') {
                            $title[$v['Field']]['typeval'] = str_replace("'","",$typeval);

                        } else {
                            $title[$v['Field']]['typeval'] = $typeval;
                        }
                    } else {
                        $title[$v['Field']]['type'] = $v['Type'];
                        $title[$v['Field']]['typeval'] = "";
                    }
                    $dataField[] = $v['Field'];
                    $type = true;
                }
            }
            if ($type) {
                foreach ($row['field'] as $k => $v) {
                    if (in_array($v,$dataField)) {
                        $row['field'][$k] = 'd.'.$v;
                    } else {
                        $row['field'][$k] = 't.'.$v;
                    }
                }
                $data = Db::name('archives')->alias('t')
                    ->leftJoin($model_list['tablename'].' d','d.id = t.id')
                    ->where('t.category_id',$row['category_id'])
                    ->field(implode(",", $row['field']))
                    ->select();
            } else {
                $data = Db::name('archives')->where('category_id',$row['category_id'])->field(implode(",", $row['field']))->select();
            }

            $excel = new Excelclass();
            $res = $excel->exportExcel($data,$title);
            $mdata = [
                'admin_id'    => $this->user->id,
                'category_id' => $row['category_id'],
                'title'       => $row['name'],
                'url'         => $res,
                'create_time' => time(),
            ];
            $this->model->insert($mdata);
            return json(['code'=>1,'msg'=>lang('The export is successful, please go to the export list to check')]);
        }
        $this->error(lang('Access error!'));
    }

    /**
     * 导出栏目excel模板
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportDome()
    {
        $category_id = $this->request->param('category_id');
        if (!$category_id) {
            $this->error(lang('Access error!'));
        }
        $model_id = $this->Categorymodel->where('id',$category_id)->value('model_id');

        $list = $this->Modelmodel->where('id', $model_id)->field('tablename,type,controller')->find();
        $prefix = $this->config->get('database')['connections']['mysql']['prefix'];

        if ($list['controller']!='Guestbook') {
            $tablename = $prefix.'archives';
            $table = Db::query("show FULL COLUMNS FROM ".$tablename);
        } else {
            $table = [];
        }

        //判断是否有副表
        $tabledataname = $prefix.$list['tablename'];
        $tabledata = Db::query("show FULL COLUMNS FROM ".$tabledataname);
        foreach ($tabledata as $k => $v) {
            if ($v['Field'] == 'id') {
                unset($tabledata[$k]);
            }
        }
        $table = array_merge($table,$tabledata);

        $title = [];
        foreach ($table as $k => $v) {
            if (!in_array($v['Field'],['id','category_id','category_ids','model_id','admin_id','user_id','style','url','comments','iscomment','collection','likes','dislikes','islogin','status','lang','update_time','create_time','delete_time'])) {
                $title[$v['Field']]['name'] = empty($v['Comment'])?$v['Field']:$v['Comment'];
                $strposval = strpos($v['Type'], '(');
                if ($strposval) {
                    $type = substr($v['Type'],0,$strposval);
                    $title[$v['Field']]['type'] = $type;
                    $typeval = substr($v['Type'],$strposval+1,strpos($v['Type'], ')')-$strposval-1);
                    if ($type == 'enum') {
                        $title[$v['Field']]['typeval'] = str_replace("'","",$typeval);

                    } else {
                        $title[$v['Field']]['typeval'] = $typeval;
                    }
                } else {
                    $title[$v['Field']]['type'] = $v['Type'];
                    $title[$v['Field']]['typeval'] = "";
                }
            }
        }
        $excel = new Excelclass();
        $excel->exportExcel([],$title,"dome");
    }

    /**
     * 导入数据
     * @return \think\response\Json
     */
    public function import()
    {
        if ($this->request->isAjax()) {

            if ($this->request->isGet()) {

            } else if ($this->request->isPost()) {

                $row = $this->request->param('row');

                $res = $this->importfield($row['category_id'],$row['file']);
                $mainfield = $vicefield = $data = $vicedata = [];
                $content_lang = site('content_lang');
                $auto_increment = $res['AUTO_INCREMENT'];

                $time = time();

                //匹配主表字段
                foreach ($res['title']['main'] as $k => $v) {
                    $key = array_keys($res['excel'][0],$v);
                    if (!empty($key)) {
                        $mainfield[$k] = $key[0];
                    } else {
                        $mainfield[$k] = "";
                    }
                }

                //匹配副表字段
                if (!empty($res['title']['vice'])) {
                    foreach ($res['title']['vice'] as $k => $v) {
                        $key = array_keys($res['excel'][0],$v);
                        if (!empty($key)) {
                            $vicefield[$k] = $key[0];
                        } else {
                            $vicefield[$k] = "";
                        }
                    }
                }

                for ($i=1; $i<count($res['excel']); $i++) {
                    if (empty($res['excel'][$i][0])) {
                        continue;
                    }
                    $data[$i] = [
                        'id'          => $auto_increment,
                        'category_id' => $res['category']['id'],
                        'show_tpl'    => $res['category']['show_tpl'],
                        'model_id'    => $res['model']['id'],
                        'admin_id'    => $this->user->id,
                        'user_id'    => 0,
                    ];
                    foreach ($mainfield as $k => $v) {
                        if ($v!=='') {
                            if (!empty($res['excel'][$i][$v])) {
                                if ($k == 'create_time' || $k == 'update_time' || ($res['model']['controller'] == 'Archives' && $k == 'publish_time')) {
                                    if (is_numeric($res['excel'][$i][$v])) {
                                        $data[$i][$k] = $res['excel'][$i][$v];
                                    } else {
                                        $data[$i][$k] = strtotime($res['excel'][$i][$v]);
                                    }
                                } else {
                                    $data[$i][$k] = $res['excel'][$i][$v];
                                }
                            } else {
                                if ($k=='publish_time') {
                                    $data[$i][$k] = $time;
                                } else if ($res['table'][$k]['Default'] === null) {
                                    $data[$i][$k] = null;
                                } else {
                                    if (!empty($res['table'][$k]['Default'])) {
                                        $data[$i][$k] = $res['table'][$k]['Default'];
                                    } else {
                                        $data[$i][$k] = "";
                                    }
                                }
                            }
                        } else {
                            if (in_array($k,['category_ids'])) {
                                $data[$i][$k] = '';
                            }
                            if (in_array($k,['create_time','update_time','publish_time'])) {
                                $data[$i][$k] = $time;
                            } else {
                                $data[$i][$k] = $res['table'][$k]['Default'];
                            }
                        }
                    }
                    $data[$i]['lang'] = $res['category']['lang'];
                    if (!empty($vicefield)) {
                        foreach ($vicefield as $k => $v) {
                            $vicedata[$i]['id'] = $auto_increment;
                            if (!empty($v)) {
                                if (!empty($res['excel'][$i][$v])) {
                                    $vicedata[$i][$k] = $res['excel'][$i][$v];
                                } else {
                                    if ($res['tabledata'][$k]['Default'] === null) {
                                        $vicedata[$i][$k] = null;
                                    } else {
                                        if (!empty($res['tabledata'][$k]['Default'])) {
                                            $vicedata[$i][$k] = $res['tabledata'][$k]['Default'];
                                        } else {
                                            $vicedata[$i][$k] = "";
                                        }
                                    }
                                }
                            } else {
                                $vicedata[$i][$k] = $res['tabledata'][$v]['Default'];
                            }
                        }
                    }
                    $auto_increment++;
                }

                Db::startTrans();
                try {
                    $result = DB::name('archives')->insertAll($data);
                    if ($result) {
                        if (!empty($vicefield)) {
                            $resultdata = DB::name($res['model']['tablename'])->insertAll($vicedata);
                            if ($resultdata) {
                                Db::commit();
                                return json(['code'=>1,'msg'=>lang('Operation completed')]);
                            }
                        } else {
                            Db::commit();
                            return json(['code'=>1,'msg'=>lang('Operation completed')]);
                        }
                    } else {
                        return json(['code'=>0,'msg'=>lang('Operation failed')]);
                    }
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return json(['code'=>0,'msg'=>$e->getMessage()]);
                }

            }
        }
        $this->error(lang('Access error!'));
    }

    /*public function timestr($time)
    {
        if (is_numeric($time)) {
            return $time;
        }
        $res = false;
        $arr = [
            'Y-m-d'    => "/\d{4}-\d{1,2}-\d{1,2}/",
            'Y/m/d'    => "/\d{4}\/\d{1,2}\/\d{1,2}/",
            'Y年m月d日' => "/\d{4}年\d{1,2}月\d{1,2}日/",
            'Y.m.d'    => "/\d{4}[.]\d{1,2}[.]\d{1,2}/",
            'Y-m-d H:i:s'    => "/\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}/",
            'Y/m/d H:i:s'    => "/\d{4}\/\d{1,2}\/\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}/",
            'Y年m月d日 H:i:s' => "/\d{4}年\d{1,2}月\d{1,2}日 \d{1,2}:\d{1,2}:\d{1,2}/",
            'Y.m.d H:i:s'    => "/\d{4}[.]\d{1,2}[.]\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}/",
        ];
        $format = "";
        foreach ($arr as $k => $v) {
            if(preg_match($v, $time)) {
                $res = true;
            } else {
                $res = false;
            }
            if ($res) {
                $format = $k;
                break;
            }
        }
        if ($format == 'Y-m-d H:i:s' || $format == 'Y年m月d日 H:i:s' || $format == 'Y-m-d' || $format == 'Y年m月d日') {
            $time = strtotime($time);
        } else if ($format == 'Y/m/d H:i:s' || $format == 'Y/m/d') {
            $time = strtotime(str_replace("/","-",$time));
        } else if ($format == 'Y.m.d H:i:s' || $format == 'Y.m.d') {
            $time = strtotime(str_replace(".","-",$time));
        }
        return $time;
    }*/

    /**
     * 获取栏目模型数据表信息
     * @param $category_id
     * @param $excelpath
     * @return array
     * @throws \Exception
     */
    public function importfield($category_id,$excelpath)
    {
        $excel = new Excelclass();
        if (!$category_id) {
            $this->error(lang('Access error!'));
        }
        $category_list = $this->Categorymodel->where('id',$category_id)->field('id,model_id,show_tpl,lang')->find()->toArray();

        $list = $this->Modelmodel->where('id', $category_list['model_id'])->field('id,tablename,controller,type')->find()->toArray();
        $prefix = $this->config->get('database')['connections']['mysql']['prefix'];
        $database = $this->config->get('database')['connections']['mysql']['database'];
        $tablename = $prefix.'archives';
        $table = Db::query("show FULL COLUMNS FROM ".$tablename);
        $AUTO_INCREMENT = Db::query("select auto_increment from information_schema.tables where table_schema='".$database."' and table_name='".$tablename."'")[0]['auto_increment'];
        $title = ['main'=>[],'vice'=>[]];
        foreach ($table as $k => $v) {
            if ($v['Field'] != 'id' && $v['Field'] != 'category_id' && $v['Field'] != 'model_id' && $v['Field'] != 'delete_time') {
                $title['main'][$v['Field']] = empty($v['Comment'])?$v['Field']:$v['Comment'];
            }
        }

        $tabledataname = $prefix.$list['tablename'];
        $tabledata = Db::query("show FULL COLUMNS FROM ".$tabledataname);
        foreach ($tabledata as $k => $v) {
            if ($v['Field'] == 'id') {
                unset($tabledata[$k]);
            } else {
                $title['vice'][$v['Field']] = empty($v['Comment'])?$v['Field']:$v['Comment'];
            }
        }
        $res = $excel->importExcel(public_path().$excelpath);
        return ['category'=>$category_list,'model'=>$list,'title'=>$title,'excel'=>$res,'table'=>array_column($table, null, 'Field'),'tabledata'=>array_column($tabledata, null, 'Field'),'AUTO_INCREMENT'=>$AUTO_INCREMENT];
    }

    /**
     * 下载
     */
    public function download()
    {
        $id = input('id', 0, 'intval');
        if ($id) {
            $list = $this->model->where('id',$id)->field('title,url')->find();
            if (!$list) {
                $this->error(lang('Parameter error!'));
            }
            //Excel文件名
            $path = $list['url'];
            $path = glob($path);
            if (empty($path)) {
                $this->error('下载文件不存在！');
            }
            $file = $path[0];
            $file_part = pathinfo($file);
            $basename = $list['title'] . "_" . $file_part['filename'];
            //获取用户客户端UA，用来处理中文文件名
            $ua = $_SERVER["HTTP_USER_AGENT"];
            //从下载文件地址中获取的后缀
            $fileExt = $file_part['extension'];
            if (preg_match("/MSIE/", $ua)) {
                $filename = iconv("UTF-8", "GB2312//IGNORE", $basename . "." . $fileExt);
            } else {
                $filename = $basename . "." . $fileExt;
            }
            header("Content-type: application/octet-stream");
            $encoded_filename = urlencode($filename);
            $encoded_filename = str_replace("+", "%20", $encoded_filename);
            if (preg_match("/MSIE/", $ua)) {
                header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
            } else if (preg_match("/Firefox/", $ua)) {
                header("Content-Disposition: attachment; filename*=\"utf8''" . $filename . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $filename . '"');
            }
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header("Content-Length: " . filesize($file));
            readfile($file);
        } else {
            $this->error(lang('Parameter error!'));
        }
    }

    /**
     * 删除文件
     */
    public function delexcel()
    {
        if ($this->request->isAjax()) {
            $id = input('id', 0, 'intval');
            if (!$id) {
                return json(['code'=>0,'msg'=>lang('Parameter error!')]);
            }
            $url = $this->model->where('id',$id)->value('url');
            if (!$url) {
                return json(['code'=>0,'msg'=>lang('Parameter error!')]);
            }

            array_map("unlink", glob($url));
            if (count(glob($url))) {
                return json(['code'=>0,'msg'=>lang('Excel file deletion failed, please check permissions!')]);
            } else {
                $this->model->where('id',$id)->delete();
                return json(['code'=>1,'msg'=>lang('Excel file deleted successfully!')]);
            }
        } else {
            $this->error(lang('Access error!'));
        }
    }
}