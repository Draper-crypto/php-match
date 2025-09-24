<?php
declare (strict_types=1);

namespace addons\sitemap;

use think\Addons;
use think\facade\Db;
use think\facade\Event;
use think\facade\Route;

class Sitemap extends Addons
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