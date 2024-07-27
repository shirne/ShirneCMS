INSERT INTO `sa_permission` (`parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES
  (9,'公众号管理','wechat.index/index','wechat_index','ion-md-chatboxes',13,0);

DROP TABLE IF EXISTS `sa_wechat`;
CREATE TABLE `sa_wechat` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT 'wechat',
  `account_type` varchar(20) DEFAULT '',
  `hash` varchar(20) DEFAULT '',
  `is_default` tinyint(4) DEFAULT '0',
  `is_debug` tinyint(4) DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `logo` varchar(150) DEFAULT '',
  `qrcode` varchar(150) DEFAULT '',
  `shareimg` varchar(150) DEFAULT '',
  `share_poster_url` VARCHAR(150) DEFAULT '',
  `account` varchar(100) DEFAULT '',
  `original` varchar(50) DEFAULT '',
  `appid` varchar(50) DEFAULT '',
  `appsecret` varchar(64) DEFAULT '',
  `token` varchar(50) DEFAULT '',
  `encodingaeskey` varchar(100) DEFAULT '',
  `subscribeurl` varchar(200) DEFAULT '',
  `mch_id` varchar(50) DEFAULT '',
  `key` varchar(50) DEFAULT '',
  `cert_path` varchar(150) DEFAULT '',
  `key_path` varchar(150) DEFAULT '',
  `access_token` TEXT,
  `jsapi_ticket` TEXT,
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `sa_wechat_reply`;
CREATE TABLE `sa_wechat_reply` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL,
  `type` VARCHAR(30) NULL,
  `reply_type` VARCHAR(30) NULL,
  `title` VARCHAR(50) NULL,
  `keyword` VARCHAR(50) NULL,
  `sort` INT NULL,
  `content` TEXT NULL,
  `create_time` INT NULL,
  `update_time` INT NULL,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_wechat_material`;
CREATE TABLE `sa_wechat_material` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL,
  `type` VARCHAR(30) NULL COMMENT 'image,voice,video,thumb,article',
  `media_id` VARCHAR(50) NULL,
  `title` VARCHAR(60) NULL,
  `keyword` VARCHAR(50) NULL,
  `description` VARCHAR(200) NULL,
  `url` VARCHAR(300) NULL,
  `create_time` INT NULL,
  `update_time` INT NULL,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_wechat_material_article`;
CREATE TABLE `sa_wechat_material_article` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL DEFAULT '0',
  `material_id` INT(11) NULL DEFAULT '0',
  `thumb_url` VARCHAR(150) NULL DEFAULT '',
  `thumb_media_id` VARCHAR(50) NULL DEFAULT '',
  `title` VARCHAR(60) NULL DEFAULT '',
  `author` VARCHAR(30) NULL DEFAULT '',
  `keyword` VARCHAR(50) NULL DEFAULT '',
  `digest` VARCHAR(100) NULL DEFAULT '',
  `content` MEDIUMTEXT NULL,
  `url` VARCHAR(300) NULL DEFAULT '',
  `content_source_url` VARCHAR(150) NULL DEFAULT '',
  `show_cover_pic` tinyint(4) default 0,
  `need_open_comment` tinyint(4) default 0,
  `only_fans_can_comment` tinyint(4) default 0,
  `create_time` INT NULL,
  `update_time` INT NULL,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_wechat_template_message`;
CREATE TABLE `sa_wechat_template_message` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL,
  `type` VARCHAR(30) NULL,
  `title` VARCHAR(50) NULL,
  `tid` VARCHAR(30) NULL,
  `template_id` VARCHAR(60) NULL,
  `keywords` VARCHAR(200) NULL,
  `content` TEXT,
  `create_time` INT NULL,
  `update_time` INT NULL,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;

 DROP TABLE IF EXISTS `sa_task_template`;
CREATE TABLE `sa_task_template` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL,
  `type` VARCHAR(30) NULL,
  `send_type` VARCHAR(30) NULL,
  `title` VARCHAR(50) NULL,
  `msgid` VARCHAR(30) NULL,
  `template_id` VARCHAR(60) NULL,
  `content` TEXT,
  `status` TINYINT(4) NULL DEFAULT 0,
  `send_result` VARCHAR(50) NULL DEFAULT '',
  `create_time` INT NULL DEFAULT 0,
  `update_time` INT NULL DEFAULT 0,
  `finish_time` INT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;