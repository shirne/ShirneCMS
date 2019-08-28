
ALTER TABLE `sa_pay_order` add `prepay_id` VARCHAR(45) NULL after `pay_type`;

ALTER TABLE `sa_member_token` ADD `platform` VARCHAR(20) NULL AFTER `member_id`;

ALTER TABLE `sa_order`
ADD `form_id` VARCHAR(45) NULL after `delete_time`,
ADD `platform` VARCHAR(45) NULL after `order_id`,
ADD `noticed` TINYINT NULL DEFAULT 0 after `status`,
ADD `comment_time` TINYINT NULL DEFAULT 0 after `confirm_time`,
ADD `refund_time` TINYINT NULL DEFAULT 0 after `cancel_time`,
ADD `rebate_total` DECIMAL(10,2) NULL DEFAULT 0 after `rebate_time`;

ALTER TABLE `sa_wechat_material` DROP COLUMN `content`;

CREATE TABLE `sa_wechat_material_article` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL,
  `material_id` INT(11) NULL,
  `thumb_media_id` VARCHAR(50) NULL,
  `title` VARCHAR(60) NULL,
  `author` VARCHAR(30) NULL,
  `keyword` VARCHAR(50) NULL,
  `digest` VARCHAR(100) NULL,
  `content` MEDIUMTEXT NULL,
  `content_source_url` VARCHAR(150) NULL,
  `show_cover_pic` tinyint(4) default 0,
  `need_open_comment` tinyint(4) default 0,
  `only_fans_can_comment` tinyint(4) default 0,
  `create_time` INT NULL,
  `update_time` INT NULL,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `sa_wechat_template_message` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL,
  `type` VARCHAR(30) NULL,
  `media_id` VARCHAR(30) NULL,
  `title` VARCHAR(50) NULL,
  `keyword` VARCHAR(50) NULL,
  `description` VARCHAR(500) NULL,
  `content` MEDIUMTEXT NULL,
  `template_id` VARCHAR(60) NULL,
  `keywords` VARCHAR(200) NULL,
  `create_time` INT NULL,
  `update_time` INT NULL,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;