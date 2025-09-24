CREATE TABLE If Not Exists `@prefix@user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录名称',
  `nickname` char(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `email` char(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` char(11) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机',
  `password` char(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
  `salt` char(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码盐',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '等级',
  `exp` int(11) NOT NULL DEFAULT '0' COMMENT '经验值',
  `avatar` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别:1-男,2-女,0-未指定',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `introduction` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '个人简介',
  `remark` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `latest_time` int(11) DEFAULT NULL COMMENT '上次登录时间',
  `login_time` int(11) DEFAULT NULL COMMENT '登录时间',
  `login_ip` char(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '登录IP',
  `login_failed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '登录失败次数',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态:normal-正常,hidden-禁用',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniaue_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

CREATE TABLE If Not Exists `@prefix@user_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parent_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '父级',
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `rules` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态:normal-正常,hidden-禁用',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色管理';


CREATE TABLE If Not Exists `@prefix@user_group_access` (
  `user_id` int(10) unsigned NOT NULL COMMENT '用户表ID',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '角色组ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  UNIQUE KEY `uid_group_id` (`user_id`,`group_id`),
  KEY `uid` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限分组表';


CREATE TABLE If Not Exists `@prefix@user_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级',
  `name` char(80) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '规则',
  `title` char(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '标题',
  `route` char(80) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路由',
  `app` char(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所属应用',
  `icon` char(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标',
  `remark` char(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型:0-权限规则,1-菜单,2-额外标识',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态:normal-正常,hidden-禁用',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='菜单规则';


CREATE TABLE If Not Exists `@prefix@user_token` (
  `token` char(32) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `expire_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;