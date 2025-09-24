<?php
// +----------------------------------------------------------------------
// | HkCms simditor 编辑器
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: HkCms team <admin@hkcms.cn>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace addons\prism;

use app\admin\model\routine\Config;
use think\Addons;

class Prism extends Addons
{

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function indexHeadHook()
    {
        if ($this->getInfo()['status']!=1) {
            return '';
        }
        // 初始化配置
        $site = Config::initConfig();
        $config = $this->getConfig();
        $this->assign('site', $site);
        $this->assign('config', $config);
        return $this->fetch('/indexhead');
    }

    public function indexFooterHook()
    {
        if ($this->getInfo()['status']!=1) {
            return '';
        }
        // 初始化配置
        $site = Config::initConfig();
        $config = $this->getConfig();
        $this->assign('site', $site);
        $this->assign('config', $config);
        return $this->fetch('/indexfooter');
    }
}