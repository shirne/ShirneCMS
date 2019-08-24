
--
-- Table structure for table `sa_wechat`
--

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

--
-- Table structure for table `sa_wechat_reply`
--

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

--
-- Table structure for table `sa_wechat_material`
--

DROP TABLE IF EXISTS `sa_wechat_material`;
CREATE TABLE `sa_wechat_material` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL,
  `type` VARCHAR(30) NULL COMMENT 'image,voice,video,thumb,article',
  `media_id` VARCHAR(50) NULL,
  `title` VARCHAR(60) NULL,
  `keyword` VARCHAR(50) NULL,
  `description` VARCHAR(200) NULL,
  `create_time` INT NULL,
  `update_time` INT NULL,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;

 --
-- Table structure for table `sa_wechat_material`
--

DROP TABLE IF EXISTS `sa_wechat_material_article`;
CREATE TABLE `sa_wechat_material` (
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


--
-- Table structure for table `sa_wechat_template_message`
--

DROP TABLE IF EXISTS `sa_wechat_template_message`;
CREATE TABLE `sa_wechat_template_message` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wechat_id` INT NOT NULL,
  `type` VARCHAR(30) NULL,
  `title` VARCHAR(50) NULL,
  `template_id` VARCHAR(60) NULL,
  `keywords` VARCHAR(200) NULL,
  `create_time` INT NULL,
  `update_time` INT NULL,
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;