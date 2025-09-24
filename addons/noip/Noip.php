<?php
declare (strict_types=1);

namespace addons\noip;

use think\Addons;

class Noip extends Addons
{
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    /**
     * 前端
     * @param $param
     * @return false|mixed|string
     * @throws \think\Exception
     */
    public function noIPHook()
    {
		// print_r($_SERVER);exit;
        $config=$this->getConfig();
        $ip=explode('|',$config['base']['ip']);
        $baseip=$this->getClientIP();
		if($config['base']['status']==1){
			if(in_array($baseip,$ip)){
			   header("location:".$config['base']['url']);
			   $this->assign('views', '您已被拉入黑名单，禁止访问该网站');
			   return $this->fetch('/index');
			   exit();
			}
		}
    }
    public function getClientIP() {
     $ipAddress = '';
     if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
     } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { // 检查是否通过代理访问
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
     } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ipAddress = $_SERVER['REMOTE_ADDR']; // 无代理直接访问
     }
       return $ipAddress;
    }
}