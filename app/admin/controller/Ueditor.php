<?php
// +----------------------------------------------------------------------
// | 百度编辑器
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\routine\Attachment;
use app\common\exception\UploadException;
use app\common\library\Upload;
use GuzzleHttp\Exception\ClientException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Filesystem;
use think\addons\Dir;
use think\facade\Validate;
use think\helper\Str;

class Ueditor extends BaseController
{
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        'login', // 登录中间件
        'auth' => ['except'=>['index']]
    ];

    protected $saveConfig = [
        'file_size'=>10485760, // 上传文件大小默认10m
        'savename'=>'/uploads/{year}{month}{day}/{md5}{suffix}', // 保存格式
        'chunk'=>2, // 1-开启，0关闭
        'chunk_size'=>2097152, // 分片大小默认2m
        'user_type'=>1,
        'user_id'=>0,
        'storage'=>'local' // 默认本地
    ];

    protected $model = null;

    /**
     * 初始化操作
     */
    public function initialize()
    {
        parent::initialize();

        $user = app('user');
        if (!$user->id) {
            return json(['code'=>-1000, 'msg'=>lang('Please log in and operate'), 'data'=>[]]);
        }

        $action = strtolower($this->request->action());
        if ($action!='index') {
            $bl = $user->check('common/upload', $user->id);
            if (!$bl) {
                $this->error(lang('No permission'));
            }
        }

        $this->model = new \app\admin\model\routine\Attachment;
        $site = site();
        $this->saveConfig['user_id'] = $user->id;
        $this->saveConfig = array_merge($this->saveConfig, $site);
    }

    public function index()
    {
        $config = $this->app->getRootPath().'addons'.DIRECTORY_SEPARATOR.'ueditor'.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'config.json';
        $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($config)), true);

        $action = $this->request->param('action');
        $offset = $this->request->param('start','0','intval');
        $limit = $config['imageManagerListSize'];
        switch ($action) {
            case 'config':
                $result =  $config;
                break;
            /* 上传图片 */
            case 'uploadimage':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $files = $this->request->file('upfile');
                if (empty($files)) {
                    $result = ['state'=>'没有文件被上传'];
                    break;
                }

                try {
                    $res = Upload::instance(['user_id'=>$this->user->id, 'user_type'=>1])->upload([$files]);
                } catch (\Exception $exception) {
                    $result = ['state'=>$exception->getMessage()];
                    break;
                }

                if (empty($res)) {
                    $result = ['state'=>'没有文件被上传'];
                } else {
                    $result = [
                        'state'=>'SUCCESS', //上传状态，上传成功时必须返回"SUCCESS"
                        'url'=>$res[0]['path'], //返回的地址
                        'title'=>basename($res[0]['path']), //新文件名
                        'original'=>$res[0]['title'],   //原始文件名
                        'type'=>$res[0]['mime_type'],   //文件类型
                        'size'=>$res[0]['size'],    //文件大小
                    ];
                }
                break;
            /* 列出图片 */
            case 'listimage':
                $data = $this->model->where('mime_type','like','image/%')->order('id', 'desc')->limit($offset, $limit)->select()->append(['size_text','user_name','cdn_url'])->toArray();
                $total = $this->model->where('mime_type','like','image/%')->order('id', 'desc')->count();
                if (empty($total)) {
                    $result = [
                        "state" => "no match file",
                        "list" => $data,
                        "start" => $offset,
                        "total" => $total
                    ];
                } else {
                    foreach ($data as $key=>&$value) {
                        $value['url'] = $value['path'];
                        $value['mtime'] = strtotime($value['create_time']);
                    }
                    $result = [
                        "state" => "SUCCESS",
                        "list" => $data,
                        "start" => $offset,
                        "total" => $total
                    ];
                }
                break;
            /* 列出文件 */
            case 'listfile':
                $data = $this->model->order('id', 'desc')->limit($offset, $limit)->select()->append(['size_text','user_name','cdn_url'])->toArray();
                $total = $this->model->order('id', 'desc')->count();
                if (empty($total)) {
                    $result = [
                        "state" => "no match file",
                        "list" => $data,
                        "start" => $offset,
                        "total" => $total
                    ];
                } else {
                    foreach ($data as $key=>&$value) {
                        $value['url'] = $value['path'];
                        $value['mtime'] = strtotime($value['create_time']);
                    }
                    $result = [
                        "state" => "SUCCESS",
                        "list" => $data,
                        "start" => $offset,
                        "total" => $total
                    ];
                }
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $fieldName = $config['catcherFieldName'];
                $fileArr = $this->request->param($fieldName, '');
                if (empty($fileArr)) {
                    $result = [
                        "state" => "no match file"
                    ];
                    break;
                } else {
                    $list = [];
                    foreach ($fileArr as $imgUrl) {
                        $info = $this->remote([$imgUrl]);
                        if (empty($info)) {
                            $result = [
                                "state" => "未能抓取到远程图片"
                            ];
                            return json($result);
                        }
                        array_push($list, array(
                            "state" => !empty($info) ? 'SUCCESS':'ERROR',
                            "url" => $info["path"],
                            "size" => $info["size"],
                            "title" => htmlspecialchars($info["title"]),
                            "original" => htmlspecialchars($info["title"]),
                            "source" => htmlspecialchars($imgUrl)
                        ));
                    }
                    $result = [
                        "state" => count($list) ? 'SUCCESS':'ERROR',
                        "list" => $list
                    ];
                    break;
                }
            default:
                $result = ['state'=> '请求地址出错'];
                break;
        }
        return json($result);
    }

    /**
     * 远程图片下载
     * @param array $files url地址 ['http://www.hkcms.cn/img/img.png']
     * @return array
     */
    protected function remote($files)
    {
        $add = [];
        $infos = [];
        foreach ($files as $key=>$value) {
            $value = htmlspecialchars($value);
            $value = str_replace("&amp;", "&", $value);
            // url链接格式检测
            if (!Validate::is($value, 'url')) {
                continue;
            }
            // 获取响应头部
            if(version_compare(PHP_VERSION,'8.0.0','<')) {
                $heads = get_headers($value,1);
            } else {
                $heads = get_headers($value,true);
            }
            if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
                // 链接不可用
                continue;
            }
            // 链接contentType必须是图片
            if (!isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
                continue;
            }

            // 解析url
            $pu = parse_url($value);
            // 获取文件后缀
            $ext = '';
            if (isset($pu['path'])) {
                $ext = strtolower(ltrim((string)strrchr($pu['path'],'.'),'.'));
            }
            $to = getExtToMime($heads['Content-Type'],'mime');
            if ($to && $ext && in_array($ext, $to)) {
                $to = [$ext];
            }
            if (empty($to)) {
                continue;
            }

            // 文件下载
            $client = new \GuzzleHttp\Client([
                'headers' => []
            ]);
            try {
                $response = $client->request('get', $value);
                $content = $response->getBody()->getContents();
            }  catch (ClientException $exception) {
                $this->error($exception->getMessage());
            }  catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            if ($response->getStatusCode()!=200) {
                $this->error(__('Operation failed'));
            }

            // 文件名
            $filename = isset($pu['path']) ? basename($pu['path'],$to[0]).'.'.$to[0] : md5((string)time());
            $filename = str_replace('..','.',$filename);
            // 保存路径
            $zip = runtime_path().$filename;
            if (file_exists($zip)) {
                @unlink($zip);
            }
            $w = fopen($zip, 'w');
            fwrite($w, $content);
            fclose($w);

            $adapter = new LocalFilesystemAdapter(runtime_path());
            $filesystem = new Filesystem($adapter);
            $pathinfo = new \SplFileInfo($zip);

            $mimetype = $filesystem->mimeType($filename);

            $temp['storage'] = $this->saveConfig['storage'];
            $temp['user_type'] = $this->saveConfig['user_type'];
            $temp['user_id'] = $this->saveConfig['user_id']; // 后台用户
            $temp['ext'] = $pathinfo->getExtension();  // 文件类型
            $temp['title'] = Str::substr($pathinfo->getFilename(), 0, 30);;
            $temp['md5'] = hash_file('md5', $zip);
            $temp['mime_type'] = $mimetype;
            $temp['size'] = $pathinfo->getSize();

            // 生成访问路径
            $name = Upload::instance()->getFileName(null, $temp['md5'], hash_file('sha1', $zip),'.'.$temp['ext']);

            $temp['path'] = $name;
            $temp['cdn_url'] = cdn_url($name);

            Dir::instance()->movedFile($zip,public_path().$temp['path']);
            $attr = Attachment::where(['path'=>$name,'storage'=>'local'])->find();
            if ($attr) {
                $attr = $attr->toArray();
                $attr['cdn_url'] = cdn_url($attr['path']);
                $infos[] = $attr;
            } else {
                $add[] = $temp;
                $infos[] = $temp;
            }
        }
        if (!empty($add)) {
            $bl = (new \app\admin\model\routine\Attachment)->saveAll($add);
            if (!$bl) {
                throw new UploadException(lang('No rows added'));
            }
        }

        // 上传文件后的标签位
        hook('uploadAfter', $infos);
        return !empty($infos)?$infos[0]:[];
    }

    /**
     * 生成保存文件路径
     * @param \think\file\UploadedFile $file
     * @param  $md5
     * @param  $sha1
     * @param  $suffix
     * @return string
     */
    protected function getFileName($file, $md5 = '', $sha1 = '', $suffix = '')
    {
        $var = [
            '{year}'=> date('Y'),
            '{month}'=> date('m'),
            '{day}'=> date('d'),
            '{hour}'=> date('H'),
            '{minute}'=> date('i'),
            '{second}'=> date('s'),
            '{md5}'=> $md5?$md5:$file->md5(),
            '{sha1}'=> $sha1?$sha1:$file->sha1(),
            '{random}'=> get_random_str(16),
            '{suffix}'=> $suffix?$suffix:'.'.$file->extension(),
        ];

        return str_replace(array_keys($var),array_values($var), $this->saveConfig['savename']);
    }
}