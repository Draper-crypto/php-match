<?php

declare (strict_types = 1);

namespace app\admin\model\excel;

use think\Model;

class Excel extends Model
{
    /**
     * 格式化时间日期
     * @param $value
     * @return false|string
     */
    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}
