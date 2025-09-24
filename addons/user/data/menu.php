<?php
return [
    [
        // 父菜单ID，0为顶级菜单
        "parent_id" => 58,
        // 标题
        "title" => 'Member',
        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
        "name"=>"user",
        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
        "route" => "",
        // 图标。fontawesome 图标
        "icon" => 'fas fa-users',
        // 备注
        "remark" => "",
        // 排序，倒序形式。
        "weigh" => 100,
        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
        "type" => 1,
        // 是否支持快速导航:1-是,0-否
        "is_nav" => 1,
        // 状态，normal-正常,hidden-禁用
        "status" => 'normal',
        // 子级
        "child"=>[
            [
                // 标题
                "title" => 'Member',
                // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                "name"=>"user/user",
                // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                "route" => "user.user/index",
                // 图标。fontawesome 图标
                "icon" => 'far fa-circle',
                // 备注
                "remark" => "",
                // 排序，倒序形式。
                "weigh" => 0,
                // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                "type" => 1,
                // 是否支持快速导航:1-是,0-否
                "is_nav" => 1,
                // 状态，normal-正常,hidden-禁用
                "status" => 1,
                "child"=>[
                    [
                        // 标题
                        "title" => 'View',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/user/index",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.user/index",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Edit',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/user/edit",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.user/edit",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Delete',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/user/del",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.user/del",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Batches',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/user/batches",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.user/batches",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ]
                ]
            ],
            [
                // 标题
                "title" => 'Member group',
                // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                "name"=>"user/group",
                // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                "route" => "user.group/index",
                // 图标。fontawesome 图标
                "icon" => 'far fa-circle',
                // 备注
                "remark" => "",
                // 排序，倒序形式。
                "weigh" => 0,
                // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                "type" => 1,
                // 是否支持快速导航:1-是,0-否
                "is_nav" => 1,
                // 状态，normal-正常,hidden-禁用
                "status" => 1,
                "child"=>[
                    [
                        // 标题
                        "title" => 'View',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/group/index",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.group/index",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Edit',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/group/edit",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.group/edit",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Add',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/group/add",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.group/add",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Delete',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/group/del",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.group/del",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Batches',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/group/batches",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.group/batches",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ]
                ]
            ],
            [
                // 标题
                "title" => 'Member rule',
                // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                "name"=>"user/rule",
                // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                "route" => "user.rule/index",
                // 图标。fontawesome 图标
                "icon" => 'far fa-circle',
                // 备注
                "remark" => "",
                // 排序，倒序形式。
                "weigh" => 0,
                // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                "type" => 1,
                // 是否支持快速导航:1-是,0-否
                "is_nav" => 1,
                // 状态，normal-正常,hidden-禁用
                "status" => 1,
                "child"=>[
                    [
                        // 标题
                        "title" => 'View',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/rule/index",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.rule/index",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Edit',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/rule/edit",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.rule/edit",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Add',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/rule/add",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.rule/add",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Delete',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/rule/del",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.rule/del",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ],
                    [
                        // 标题
                        "title" => 'Batches',
                        // 权限认证规则，控制器/方法，二级文件夹则：文件夹名/控制器/方法，注意二级需要填写路由
                        "name"=>"user/rule/batches",
                        // 路由地址，文件夹名.控制器/方法，常用与有文件夹的情况，注意是“点”。
                        "route" => "user.rule/batches",
                        // 图标。fontawesome 图标
                        "icon" => 'fas fa-circle',
                        // 备注
                        "remark" => "",
                        // 排序，倒序形式。
                        "weigh" => 0,
                        // 类型:0-权限规则,1-菜单,2-菜单头(提供的额外标识)
                        "type" => 0,
                        // 是否支持快速导航:1-是,0-否
                        "is_nav" => 0,
                        // 状态，normal-正常,hidden-禁用
                        "status" => 1,
                    ]
                ]
            ]
        ]
    ],
];