<?php
// +----------------------------------------------------------------------
// | HkCms api 权限认证
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: HkCms team <admin@hkcms.cn>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace addons\user\middleware;

use addons\user\library\User;

class Auth
{
    use \app\common\library\Jump;

    /**
     * 错误模板，主题文件夹下
     * @var string
     */
    protected $error_tmpl = '/error';
    protected $success_tmpl = 'success';

    /**
     * @param \think\Response $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $bl = User::instance()->checkToken(User::instance()->getToken());
        if (!$bl) {
            return redirect((string)url('/user.user/login'));
        }

        $action = strtolower($request->action());
        $url = str_replace('.','/',$request->controller()).'/'.$action;
        $url = strtolower($url);

        if (!User::instance()->check($url, User::instance()->id)) {
            $this->error(lang('No permission'));
        }

        return $next($request);
    }
}