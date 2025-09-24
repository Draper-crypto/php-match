CREATE TABLE If Not Exists `@prefix@sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '事件',
  `mobile` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机',
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '验证码',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '验证次数',
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IP',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;