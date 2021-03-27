alter TABLE `sa_category` 
add  `fields` TEXT COMMENT '开启的字段' after `props`,
add  `template_dir` varchar(20) DEFAULT 0 COMMENT '独立模板目录' after `use_template`,
add `channel_mode` tinyint(11) DEFAULT 0 COMMENT '频道模式' after `template_dir`,
add `is_comment` tinyint(11) DEFAULT 0 COMMENT '是否开启评论' after `channel_mode`,
add `is_images` tinyint(11) DEFAULT 0 COMMENT '是否有图集' after `is_comment`,
add `is_attachments` tinyint(11) DEFAULT 0 COMMENT '是否有附件' after `is_images`;

alter TABLE `sa_article`
add `name` varchar(100) DEFAULT '' after `main_id`,
add `keywords` varchar(150) DEFAULT '' after `cover`,
add `template` varchar(100) NOT NULL DEFAULT '' after `type`;