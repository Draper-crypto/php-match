<?php
// +----------------------------------------------------------------------
// | HkCms 通用基础短信插件
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 广州恒企教育科技有限公司 <admin@hkcms.cn>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace addons\basesms;

use addons\basesms\library\Sms;

class Basesms extends \think\Addons
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
     * 短信发送
     * @param $param
     * @return bool
     */
    public function userMobileSendHook($param)
    {
        if (empty($param['mobile']) || empty($param['event']) || empty($param['code'])) {
            return false;
        }

        return Sms::instance()->send($param['mobile'], $param['code'], $param['event']);
    }

    /**
     * 短信验证码
     * @param $param
     * @return bool
     */
    public function userMobileCheckHook($param)
    {
        if (empty($param['mobile']) || empty($param['event']) || empty($param['code'])) {
            return false;
        }
        return Sms::instance()->check($param['mobile'], $param['event'], $param['code']);
    }
}