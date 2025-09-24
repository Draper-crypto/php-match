<?php
namespace addons\user;

use app\admin\model\routine\Config;
use think\Addons;
use think\addons\Dir;
use think\facade\Route;

class User extends Addons
{
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function enable()
    {
        // 菜单数组文件路径
        $menu = include $this->addon_path.'data'.DIRECTORY_SEPARATOR.'menu.php';
        // 参数一给路径，参数二给当前插件的标识
        create_menu($menu,$this->getName());

        return true;
    }

    public function disable()
    {
        return true;
    }

    /**
     * 插件初始化
     */
    public function addonsInitHook()
    {
        // 加载初始化函数
        include_once $this->addon_path . 'common.php';
        // 加载扩展语言包
        app()->lang->load($this->addon_path.'data'.DIRECTORY_SEPARATOR.$this->app->lang->getLangset().'.php');
    }

    /**
     * 前台路由加载
     */
    public function indexRouteHook()
    {
        Route::get('/u$', '/user.user/index')->ext('');
        Route::rule('/u/login$', '/user.user/login');
        Route::rule('/u/register$', '/user.user/register');
        Route::rule('/u/loginout$', '/user.user/loginout');
        Route::rule('/u/profile$', '/user.user/profile');
        Route::rule('/u/bind$', '/user.user/bind');
        Route::rule('/u/changePwd$', '/user.user/changePwd');
        Route::rule('/u/sms/send$', '/user.user/send');
        Route::rule('/u/upload$', '/user.user/upload');
        Route::rule('/u/resetpwd', '/user.user/resetPwd');
    }

    /**
     * 主题更改
     * @param $theme
     */
    public function themeChangeHook($theme)
    {
        $site = Config::initConfig();
        $theme = $site[$theme];

        $path = config('cms.tpl_path').'index'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR;
        if (!file_exists($path.'user'.DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR.'index.html')) {
            $dir = Dir::instance();
            $addonPath = $this->addon_path.'install'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'index'.DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR;
            $dir->copyDir($addonPath, $path.'user'.DIRECTORY_SEPARATOR);
        }
    }
}