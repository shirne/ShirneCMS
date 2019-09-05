ALTER TABLE `sa_links`
ADD `group` varchar(50) DEFAULT '' AFTER `title`,
ADD `status` int(11) DEFAULT 0 AFTER `sort`,
ADD `create_time` int(11) DEFAULT 0 AFTER `status`,
ADD `update_time` int(11) DEFAULT 0 AFTER `create_time`;

ALTER TABLE `sa_feedback`
ADD `update_time` int(11) NOT NULL DEFAULT '0' AFTER `create_time`,
CHANGE COLUMN `reply_at` `reply_time` int(11) DEFAULT '0';

ALTER TABLE `sa_order`
ADD `deliver_time` INT NULL DEFAULT 0 after `pay_time`,
ADD `reason` VARCHAR(50) NULL COMMENT '取消/退款原因' AFTER `cancel_time`;


CREATE TABLE IF NOT EXISTS `sa_express_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '',
  `express` varchar(50) DEFAULT '',
  `status` tinyint(1) DEFAULT '1',
  `use_times` int(11) DEFAULT '0',
  `create_time` INT NULL DEFAULT 0,
  `update_time` INT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `sa_express_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `express_no` VARCHAR(100) NULL,
  `express_code` VARCHAR(20) NULL,
  `create_time` INT NULL DEFAULT 0,
  `update_time` INT NULL DEFAULT 0,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `express_no` (`express_no`),
  KEY `express_code` (`express_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;