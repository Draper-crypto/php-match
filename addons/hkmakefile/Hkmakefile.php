<?php
declare (strict_types=1);

namespace addons\hkmakefile;

use think\Addons;

class Hkmakefile extends Addons
{
    // 菜单
    public $menu = [
        [
            'type'  => 1,
            'weigh' => 100,
            'title' => '一键生成应用',
            'name'  => 'hkmakefile/index/index',
            'route' => 'hkmakefile.index/index',
        ]
    ];

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function addonsInitHook()
    {

    }
}