<?php
// +----------------------------------------------------------------------
// | HkCms 后台用户验证器
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: HkCms team <admin@hkcms.cn>
// +----------------------------------------------------------------------

declare (strict_types = 1);

namespace app\admin\validate\hkmakefile;

use app\admin\validate\BaseValidate;

class Addons extends BaseValidate
{

	protected $rule = [
        'title'       => 'require|length:4,100',
        'description' => 'require',
        'name'        => 'require',
        'author'      => 'require',
        'version'     => 'require',
    ];

}
