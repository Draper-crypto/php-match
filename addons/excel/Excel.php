<?php
declare (strict_types=1);

namespace addons\excel;

use think\Addons;

class Excel extends Addons
{

    public function install()
    {
        $sql = $this->addon_path.'data'.DIRECTORY_SEPARATOR.'excel.sql';
        create_sql($sql);
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function enable()
    {
        // 菜单数组文件路径
        $menu = include $this->addon_path.'data'.DIRECTORY_SEPARATOR.'m.php';
        // 参数一给路径，参数二给当前插件的标识
        create_menu($menu,$this->getName());

        return true;
    }

    public function disable()
    {
        del_menu($this->getName());
        return true;
    }
    /**
     * 插件初始化
     */
    public function addonsInitHook()
    {
        // 加载扩展语言包
        app()->lang->load($this->addon_path.'data'.DIRECTORY_SEPARATOR.$this->app->lang->getLangset().'.php');

    }
}