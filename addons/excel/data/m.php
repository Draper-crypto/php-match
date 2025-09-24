<?php

return [
    [
        // 父菜单ID，0为顶级菜单
        "parent_id" => 0,
        // 标题
        "title" => 'Excel Features',
        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
        "name"=>"excel/index/index",
        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
        "route" => "excel.index/index",
        // 图标。fontawesome 图标
        "icon" => 'fas fa-print',
        // 备注
        "remark" => "",
        // 排序，倒序形式。
        "weigh" => 100,
        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
        "type" => 1,
        // 是否支持快速导航:1-是,0-否
        "is_nav" => 0,
        // 状态，normal-正常,hidden-禁用
        "status" => 'normal',
        // 子级
        "child"=>[
        ]
    ],
]

?>