<?php
// +----------------------------------------------------------------------
// | HkCms 登录验证
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: HkCms team <admin@hkcms.cn>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace addons\user\middleware;

use addons\user\library\User;

class Login
{
    /**
     * @param \think\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $user = User::instance();
        $token = $user->getToken();
        $bl = $user->checkToken($token);

        if (!$bl) {
            $error = $user->getError();
            if ($request->isAjax()) {
                return json(['code'=>-1000, 'msg'=>$error ? $error : '请登录后再试', 'data'=>[]]);
            }
            return redirect((string)url('/user.user/login'));
        }
        return $next($request);
    }
}