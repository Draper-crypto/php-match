CREATE TABLE IF NOT EXISTS `@prefix@tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '标签封面',
  `seo_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO关键字',
  `seo_description` varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '文档数量',
  `views` int(11) NOT NULL DEFAULT '0' COMMENT '点击量',
  `weigh` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `autolink` tinyint(2) NOT NULL DEFAULT '1' COMMENT '自动内链:1=自动内链',
  `lang` varchar(20) NOT NULL DEFAULT '' COMMENT '语言标识',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='标签';

CREATE TABLE IF NOT EXISTS `@prefix@tags_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tags_id` int(10) unsigned NOT NULL COMMENT '标签ID',
  `model_id` int(10) unsigned NOT NULL COMMENT '模型ID',
  `category_id` int(11) NOT NULL COMMENT '栏目ID',
  `content_id` int(11) NOT NULL COMMENT '内容ID',
  `content_title` varchar(255) NOT NULL DEFAULT '' COMMENT '内容标题',
  `lang` varchar(20) NOT NULL DEFAULT '' COMMENT '语言标识',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;