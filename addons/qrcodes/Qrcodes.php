<?php
declare (strict_types=1);
namespace addons\qrcodes;
use think\Addons;
class Qrcodes extends Addons
{
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
   
    // 对应showTest
    public function showcodeHook($param)
    {
        $url="https://api.xiaole.work/api/qrcode/qrcode.php?text=".$param['text'];
        return  $url;
    }

}