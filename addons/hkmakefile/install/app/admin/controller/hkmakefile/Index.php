<?php
declare (strict_types=1);

namespace app\admin\controller\hkmakefile;

use think\addons\AddonsException;
use think\App;
use think\facade\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use app\admin\controller\BaseController;
use addons\hkmakefile\Hkmakefileclass;

class Index extends BaseController
{
    protected $middleware = [
        'login',
        'auth' => ['except'=>['index']]
    ];

    /**
     * 初始化操作
     */
    public function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        return $this->view->fetch();
    }

    //一键生成模板的基础模板
    public function addtemplate()
    {
        if ($this->request->isAjax()) {
            $row = $this->request->param('row/a');
            validate('\\app\\admin\\validate\\hkmakefile\\Template')->check($row);

            /**创建目录**/
            $templatepath = '../template/index/'.$row['name'];
            $staticpath = './static/module/index/'.$row['name'];
            $arr = ['category','common','index','list','page','show'];
            $static = ['css','js','img','lang'];
            $hkmfclass = new Hkmakefileclass();

            //创建html目录
            if ($row['type'] == 'responsive') {
                $hkmfclass->addfile($templatepath,$arr);
                $hkmfclass->copyfile($templatepath.'/index','template/index/',['index','search']);
            } else if ($row['type'] == 'pcmobile') {
                $hkmfclass->addfile($templatepath.'/pc',$arr);
                $hkmfclass->addfile($templatepath.'/mobile',$arr);
            } else {
                $this->error("访问错误");
            }

            //创建样式目录
            $hkmfclass->addfile($staticpath,$static);
            //创建info.ini
            $hkmfclass->addinfo($templatepath,$row);

            //复制成功错误页
            if (!empty($row['file_path'])) $hkmfclass->copyfile($templatepath,'common/',$row['file_path']);

            //生成配置文件和语言包
            if (!empty($row['other_path'])) {
                foreach ($row['other_path'] as $v) {
                    if ($v == "config") {
                        $hkmfclass->copyfile($templatepath,'common/',[$v],'.json');
                    } else {
                        $hkmfclass->copyfile($staticpath.'/lang','common/',[$v],'.json');
                    }
                }
            }
            return json(['code'=>1,'msg'=>"生成模板成功"]);
        } else {
            $this->error("访问错误");
        }
    }

    //一键生成插件的基础模板
    public function addaddons()
    {
        if ($this->request->isAjax()) {
            $row = $this->request->param('row/a');
            validate('\\app\\admin\\validate\\hkmakefile\\Addons')->check($row);
            $hkmfclass = new Hkmakefileclass();
            //首字母大写
            $bname = ucfirst($row['name']);

            /**创建目录**/
            $addonspath = '../addons/';
            //创建目录
            $hkmfclass->addfile($addonspath,$row['name']);
            //创建info.ini
            $hkmfclass->addinfo($addonspath,$row);
            //创建插件类
            $hkmfclass->classfile($addonspath,$row['name']);

            if (!empty($row['base_path'])) {
                $hkmfclass->basefile($addonspath.$row['name'],$row['base_path']);
            }
            if (!empty($row['addons_path'])) {
                $hkmfclass->addonsfile($addonspath.$row['name'],$row['name'],$row['addons_path']);
            }
            return json(['code'=>1,'msg'=>"生成插件成功"]);
        } else {
            $this->error("访问错误");
        }
    }

    //查询应用标识是否存在
    public function detect()
    {
        $name = $this->request->param('name');
        $type = $this->request->param('type');
        try {
            $client = new Client(['base_uri' => Config::get('cms.api_url')]);
            $response = $client->request('post', 'appcenter/detectname', ['query' => ['name'=>$name,'type'=>$type]]);
            $content = $response->getBody()->getContents();
            return json(json_decode($content, true));
        }  catch (ClientException $exception) {
            throw new AddonsException($exception->getMessage());
        }
    }

}