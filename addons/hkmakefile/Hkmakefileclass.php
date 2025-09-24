<?php
declare (strict_types=1);

namespace addons\hkmakefile;

class Hkmakefileclass
{

    /**
     * 生成应用信息
     * @param $path 路径
     * @param $row  参数
     * @return bool
     */
    public function addinfo($path = "",$row = [],$type = 'template')
    {
        if (!empty($row)) {
            $path = $path."/info.ini";
            if (file_exists($path)) {
                unlink($path);
            }
            $str = 'name = "'.$row['name'].'"
type = "template"
module = "index"
title = "'.$row['title'].'"
description = "'.$row['description'].'"
author = "'.$row['author'].'"
version = "'.$row['version'].'"
status = 1';
            //插件扩展信息
            if ($type == 'addon') {
                if (!empty($row['database'])) {
                    $str .= '
database = "'.$row['database'].'"';
                }
                if (!empty($row['dir'])) {
                    $str .= '
dir = "'.$row['dir'].'"';
                }
            }
            $myfile = fopen($path, "a");
            fwrite($myfile, $str);
            fclose($myfile);
        } else {
            return false;
        }
        return true;
    }

    /**
     * 生成文件夹
     * @param       $pa   路径
     * @param array $arr  多文件夹：一级数组，单文件夹：字符串
     * @return bool
     */
    public function addfile($pa = "",$arr = [])
    {
        if (!empty($arr)) {
            if (is_array($arr)) {
                foreach ($arr as $v) {
                    $path = $pa.'/'.$v;
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                }
            } else {
                $path = $pa.'/'.$arr;
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * @param           $path       //粘贴的路径
     * @param string    $stubpath   //需要复制的路径
     * @param array     $arr        //需要复制的文件名
     * @param array     $suffix        //粘贴的文件后缀
     * @return bool
     */
    public function copyfile($path="",$stubpath="",$arr = ['error','success'],$suffix = ".html")
    {
        $file_path = '../addons/hkmakefile/stub/';
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $file = $file_path.$stubpath.$v.'.stub';
                $file_new = $path.'/'.$v.$suffix;
                copy($file,$file_new);
            }
        } else {
            return false;
        }
        return true;
    }

    //创建插件类
    public function classfile($path,$name)
    {
        $bname = ucfirst($name);
        $file = '../addons/hkmakefile/stub/addons/dome.stub';
        $file_new = $path.'/'.$name.'/'.$bname.'.php';
        copy($file,$file_new);
        $content = file_get_contents($file_new);
        $content = str_replace('{%$name%}',$name,$content);
        $content = str_replace('{%$bname%}',$bname,$content);
        file_put_contents($file_new,$content);
        return true;
    }

    //插件创建基础文件
    public function basefile($path,$arr = [])
    {
        if (!empty($arr)) {
            //配置文件
            if (in_array('config.php',$arr)) {
                $file = '../addons/hkmakefile/stub/addons/config.stub';
                $file_new = $path.'/config.php';
                copy($file,$file_new);
            }
            //函数库
            if (in_array('common.php',$arr)) {
                $file = '../addons/hkmakefile/stub/addons/common.stub';
                $file_new = $path.'/common.php';
                copy($file,$file_new);
            }
            //路由
            if (in_array('route.php',$arr)) {
                $file = '../addons/hkmakefile/stub/addons/route.stub';
                $file_new = $path.'/route.php';
                copy($file,$file_new);
            }
        } else {
            return false;
        }
        return true;
    }

    //插件创建CMV文件
    public function addonsfile($path,$name,$arr = [])
    {
        if (!empty($arr)) {
            //插件控制器
            if (in_array('controller',$arr)) {
                $this->addfile($path,'controller');
                $file = '../addons/hkmakefile/stub/addons/controller/Index.stub';
                $file_new = $path.'/controller/Index.php';
                copy($file,$file_new);
                $content = file_get_contents($file_new);
                $content = str_replace('{%$name%}',$name,$content);
                file_put_contents($file_new,$content);
            }
            //插件模型
            if (in_array('model',$arr)) {
                $this->addfile($path,'model');
                $file = '../addons/hkmakefile/stub/addons/model/Ceshi.stub';
                $file_new = $path.'/model/Ceshi.php';
                copy($file,$file_new);
                $content = file_get_contents($file_new);
                $content = str_replace('{%$name%}',$name,$content);
                file_put_contents($file_new,$content);
            }
            //插件视图
            if (in_array('view',$arr)) {
                $this->addfile($path,['view','view/index']);
                $file = '../addons/hkmakefile/stub/addons/view/index/test.stub';
                $file_new = $path.'/view/index/test.html';
                copy($file,$file_new);
            }
            //插件中间件
            if (in_array('middleware',$arr)) {
                $this->addfile($path,'middleware');
            }
            //插件验证器
            if (in_array('validate',$arr)) {
                $this->addfile($path,'validate');
                $file = '../addons/hkmakefile/stub/addons/validate/Index.stub';
                $file_new = $path.'/validate/Index.php';
                copy($file,$file_new);
                $content = file_get_contents($file_new);
                $content = str_replace('{%$name%}',$name,$content);
                file_put_contents($file_new,$content);
            }
        } else {
            return false;
        }
        return true;
    }

}