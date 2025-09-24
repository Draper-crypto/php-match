<?php
// +----------------------------------------------------------------------
// | 百度编辑器 v1.0.0
// +----------------------------------------------------------------------
// | 个人主页：https://www.hkcms.cn/index/home/user/user/82
// | 注意：在使用的时候，请禁用其他编辑器。
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace addons\ueditor;

use think\Addons;

class Ueditor extends Addons
{
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    // 插件启用时触发(可选)
    public function enable()
    {
        return true;
    }

    // 插件禁用时的处理(可选)
    public function disable()
    {
        return true;
    }

    // 插件升级时的处理(可选)
    public function upgrade()
    {
        return true;
    }

    /**
     * 动态引用
     * @param $param
     * @return string
     */
    public function indexFooterHook($param)
    {
        $config = $this->getConfig();
        if (!empty($config['isformat']) && $config['isformat']==1 && !empty($config['classid'])) {
            return '<script src="/static/addons/ueditor/ue/ueditor.parse.min.js"></script><script>uParse("'.$config['classid'].'",{rootPath:"/static/addons/ueditor/ue/"});</script>';
        }
        return '';
    }
}