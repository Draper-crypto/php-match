<?php
// +------------------------------------------------------------------------------
// | 站点地图生成插件，安装初始化文件
// +------------------------------------------------------------------------------
// | Copyright (c) 2023-2099 https://www.hkcms.cn/u/82.html, All rights reserved.
// +------------------------------------------------------------------------------
// | Author: Inspire <1438214726@qq.com>
// +------------------------------------------------------------------------------

declare (strict_types=1);

namespace addons\sitemaps;

use think\Addons;

class Sitemaps extends Addons
{
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
}