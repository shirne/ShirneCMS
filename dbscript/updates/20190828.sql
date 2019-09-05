
ALTER TABLE `sa_article_comment`
ADD `nickname` varchar(50) NOT NULL DEFAULT '' AFTER `article_id`;

CREATE TABLE `sa_member_level_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `amount` int(11) DEFAULT '0' COMMENT '金额 单位分',
  `create_time` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `remark` varchar(45) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sa_member_level` ADD `upgrade_type` TINYINT NULL DEFAULT 0 COMMENT '1: 累计消费升级, 2: 购买升级' AFTER `is_default`;

ALTER TABLE `sa_product`
CHANGE `commission_percent` `commission_percent`  text;