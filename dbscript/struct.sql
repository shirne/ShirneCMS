
/*!40101 SET NAMES utf8 */;

--
-- Table structure for table `sa_lang`
--

DROP TABLE IF EXISTS `sa_lang`;
CREATE TABLE `sa_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) NOT NULL,
  `table` varchar(20) NOT NULL DEFAULT '',
  `field` varchar(20) DEFAULT '',
  `key_id` int(11) DEFAULT '',
  `value` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`lang`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_manager`
--

DROP TABLE IF EXISTS `sa_manager`;
CREATE TABLE `sa_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `realname` varchar(20) NOT NULL DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `password` varchar(32) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `login_ip` varchar(50) DEFAULT '',
  `status` tinyint(1) DEFAULT '1' COMMENT '0:禁止登陆 1:正常',
  `type` tinyint(1) DEFAULT '1' COMMENT '1:普通管理员 ',
  `logintime` INT(11) NULL DEFAULT 0,
  `last_view_member` INT(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_manager_permision`
--
DROP TABLE IF EXISTS `sa_manager_permision`;
CREATE TABLE `sa_manager_permision` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `global` VARCHAR(200) NULL DEFAULT '',
  `detail` TEXT NULL,
  `manager_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `manager_id_UNIQUE` (`manager_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `sa_manager_log`
--

DROP TABLE IF EXISTS `sa_manager_log`;
CREATE TABLE `sa_manager_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(11) DEFAULT '0',
  `ip` varchar(50) DEFAULT '',
  `create_time` int(11) DEFAULT NULL,
  `action` varchar(45) DEFAULT NULL,
  `result` tinyint(4) DEFAULT '1',
  `remark` varchar(250) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
  `access_token` TEXT,
  `jsapi_ticket` TEXT,
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `sa_category`
--

DROP TABLE IF EXISTS `sa_category`;
CREATE TABLE `sa_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL COMMENT '父分类ID',
  `title` varchar(100) DEFAULT NULL COMMENT '分类名称',
  `short` varchar(20) DEFAULT NULL COMMENT '分类简称',
  `name` varchar(50) DEFAULT NULL COMMENT '分类别名',
  `icon` varchar(150) DEFAULT NULL COMMENT '图标',
  `image` varchar(100) DEFAULT NULL COMMENT '大图',
  `sort` int(11) DEFAULT 0 COMMENT '排序',
  `pagesize` int(11) DEFAULT 12 COMMENT '分页',
  `use_template` tinyint(11) DEFAULT 0 COMMENT '独立模板',
  `keywords` varchar(255) DEFAULT NULL COMMENT '分类关键词',
  `description` varchar(255) DEFAULT NULL COMMENT '分类描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_adv_group`
--

DROP TABLE IF EXISTS `sa_adv_group`;
CREATE TABLE `sa_adv_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `flag` varchar(50) DEFAULT '',
  `create_time` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_adv_item`
--

DROP TABLE IF EXISTS `sa_adv_item`;
CREATE TABLE `sa_adv_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `group_id` int(11) DEFAULT NULL COMMENT '分组ID',
  `title` varchar(100) DEFAULT NULL,
  `image` varchar(150) DEFAULT '',
  `url` varchar(150) DEFAULT '',
  `text` varchar(200) DEFAULT '',
  `text_vice` varchar(200) DEFAULT '',
  `start_date` int(11) DEFAULT 0,
  `end_date` int(11) DEFAULT 0,
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  `sort` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_links`
--

DROP TABLE IF EXISTS `sa_links`;
CREATE TABLE `sa_links` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT NULL,
  `logo` varchar(150) DEFAULT '',
  `url` varchar(150) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_notice`
--

DROP TABLE IF EXISTS `sa_notice`;
CREATE TABLE `sa_notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `manager_id` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_o_auth`
--

DROP TABLE IF EXISTS `sa_o_auth`;
CREATE TABLE `sa_o_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `logo` varchar(150) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `appid` varchar(50) DEFAULT '',
  `appkey` varchar(50) DEFAULT '',
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `sa_subscribe`
--

DROP TABLE IF EXISTS `sa_subscribe`;
CREATE TABLE `sa_subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'email',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `last_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_member`
--

DROP TABLE IF EXISTS `sa_member`;
CREATE TABLE `sa_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `realname` varchar(20) NOT NULL DEFAULT '',
  `level_id` int(11) DEFAULT '0',
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `mobile_bind` tinyint(4) DEFAULT '0',
  `email` varchar(100) DEFAULT '',
  `email_bind` tinyint(4) DEFAULT '0',
  `password` varchar(32) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `gender` tinyint(4) DEFAULT '0',
  `birth` int(11) DEFAULT '0',
  `address` varchar(150) DEFAULT '',
  `province` varchar(50) DEFAULT '',
  `city` varchar(50) DEFAULT '',
  `county` varchar(50) DEFAULT '',
  `postcode` varchar(20) DEFAULT '',
  `qq` varchar(20) DEFAULT '',
  `wechat` varchar(20) DEFAULT '',
  `alipay` varchar(50) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `delete_time` INT NULL DEFAULT 0 COMMENT '删除状态',
  `login_ip` varchar(50) DEFAULT '',
  `status` tinyint(1) DEFAULT '1' COMMENT '0:禁止登陆 1:正常',
  `type` tinyint(1) DEFAULT '1' COMMENT '1:普通会员 ',
  `credit` int(11) DEFAULT '0',
  `money` int(11) DEFAULT '0',
  `total_cashin` int(11) DEFAULT '0',
  `total_recharge` int(11) DEFAULT '0',
  `total_consume` int(11) DEFAULT '0',
  `referer` int(11) DEFAULT '0',
  `is_agent` tinyint(1) DEFAULT '0' COMMENT ' ',
  `agentcode` varchar(10) DEFAULT '',
  `recom_total` int(11) DEFAULT '0',
  `recom_count` int(11) DEFAULT '0',
  `team_count` int(11) DEFAULT '0',
  `logintime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `mobile` (`mobile`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `sa_member_token`;

CREATE TABLE `sa_member_token` (
  `token_id` BIGINT NOT NULL AUTO_INCREMENT,
  `member_id` INT UNSIGNED NULL DEFAULT 0,
  `token` VARCHAR(50) NULL,
  `create_time` INT NULL DEFAULT 0,
  `update_time` INT NULL DEFAULT 0,
  `expire_in` INT NULL DEFAULT 720,
  `refresh_token` VARCHAR(50) NULL,
  PRIMARY KEY (`token_id`),
  KEY `member_id` (`member_id`),
  KEY `token` (`token`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_member_message`;

CREATE TABLE `sa_member_message` (
  `message_id` BIGINT NOT NULL AUTO_INCREMENT,
  `member_id` INT NOT NULL DEFAULT 0,
  `type` TINYINT NOT NULL DEFAULT 0,
  `from_member_id` INT NULL DEFAULT 0,
  `manager_id` INT NULL DEFAULT 0,
  `reply_id` INT NULL DEFAULT 0,
  `group_id` INT NULL DEFAULT 0,
  `title` VARCHAR(60) NULL DEFAULT '',
  `content` TEXT NULL,
  `create_time` INT NULL DEFAULT 0,
  `show_at` INT NULL,
  `read_at` INT NULL DEFAULT 0,
  `is_delete` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`),
  INDEX `member_id`(`member_id`),
  INDEX `type`(`type`),
  INDEX `show_at`(`show_at`),
  INDEX `is_delete`(`is_delete`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `sa_member_level`;

CREATE TABLE `sa_member_level` (
  `level_id` INT NOT NULL AUTO_INCREMENT,
  `level_name` VARCHAR(30) NULL,
  `short_name` VARCHAR(10) NULL,
  `style` VARCHAR(10) default 'secondary',
  `is_default` TINYINT NULL DEFAULT 0,
  `level_price` DECIMAL(10,2) NULL COMMENT '购买价格',
  `discount` TINYINT NULL DEFAULT 100 COMMENT '会员折扣',
  `is_agent` TINYINT NULL DEFAULT 0 COMMENT '是否代理组',
  `sort` INT NULL DEFAULT 0,
  `commission_layer` INT NULL COMMENT '分佣层数',
  `commission_percent` VARCHAR(200) NULL COMMENT '分佣奖励',
  PRIMARY KEY (`level_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_member_agent`;

CREATE TABLE `sa_member_agent` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NULL,
  `short_name` VARCHAR(10) NULL,
  `style` VARCHAR(10) default 'secondary',
  `is_default` TINYINT NULL DEFAULT 0,
  `recom_count` INT NULL DEFAULT 0,
  `team_count` INT NULL DEFAULT 0,
  `sale_award` INT NULL DEFAULT 0,
  `global_sale_award` INT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_address`;

CREATE TABLE `sa_member_address` (
  `address_id` INT NOT NULL AUTO_INCREMENT,
  `member_id` INT NULL,
  `recive_name` VARCHAR(50) NULL,
  `mobile` VARCHAR(30) NULL,
  `province` VARCHAR(50) NULL,
  `city` VARCHAR(50) NULL,
  `area` VARCHAR(50) NULL,
  `address` VARCHAR(150) NULL,
  `code` VARCHAR(10) NULL,
  `locate` VARCHAR(100) NULL,
  `is_default` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`address_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_member_log`;

CREATE TABLE `sa_member_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INT NULL DEFAULT 0,
  `model` VARCHAR(45) NULL,
  `ip` VARCHAR(50) DEFAULT '',
  `create_time` INT NULL,
  `action` VARCHAR(45) NULL,
  `result` TINYINT NULL DEFAULT 1,
  `remark` VARCHAR(250) NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `action`(`action`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_card`;

CREATE TABLE `sa_member_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT 0,
  `cardno` varchar(30) DEFAULT NULL,
  `bankname` varchar(50) DEFAULT NULL,
  `cardname` varchar(50) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `is_default` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_member_recharge`
--

DROP TABLE IF EXISTS `sa_member_recharge`;
CREATE TABLE `sa_member_recharge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `paytype_id` int(11) NOT NULL,
  `amount` int(11) DEFAULT '0' COMMENT '金额 单位分',
  `create_time` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `remark` varchar(45) DEFAULT '',
  `audit_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `sa_member_cashin`
--

DROP TABLE IF EXISTS `sa_member_cashin`;
CREATE TABLE `sa_member_cashin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `amount` int(11) DEFAULT '0' COMMENT '金额 单位分',
  `real_amount` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `audit_time` int(11) DEFAULT '0',
  `bank_id` int(11) DEFAULT '0',
  `bank_name` varchar(50) DEFAULT '',
  `cardno` varchar(20) DEFAULT '',
  `status` tinyint(4) DEFAULT '0',
  `remark` varchar(50) DEFAULT '',
  `cashtype` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_member_money_log`
--

DROP TABLE IF EXISTS `sa_member_money_log`;
CREATE TABLE `sa_member_money_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `from_member_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL,
  `before` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `after` int(11) DEFAULT NULL,
  `field` varchar(30) DEFAULT 'money',
  `reson` varchar(100) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY  `type`(`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_award_log`;

CREATE TABLE `sa_award_log`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `type` varchar(20) DEFAULT '',
  `from_member_id` int(11) NOT NULL,
  `amount` int(11) DEFAULT '0' COMMENT '金额 单位分',
  `real_amount` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `remark` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_member_oauth`;

CREATE TABLE `sa_member_oauth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `type` varchar(20) DEFAULT NULL COMMENT 'qq/sina/github/weixin/wxapp',
  `type_id` INT DEFAULT 0,
  `openid` varchar(64) DEFAULT '',
  `unionid` varchar(64) DEFAULT '',
  `data` TEXT,
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  `is_follow` tinyint(4) DEFAULT 0,
  `nickname` varchar(100) DEFAULT '',
  `name` varchar(100) DEFAULT '',
  `email` varchar(150) DEFAULT '',
  `gender` TINYINT DEFAULT 0,
  `avatar` varchar(200) DEFAULT '',
  `city` varchar(100) DEFAULT '',
  `province` varchar(100) DEFAULT '',
  `country` varchar(100) DEFAULT '',
  `language` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `type`(`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_invite_code`
--

DROP TABLE IF EXISTS `sa_invite_code`;
CREATE TABLE `sa_invite_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `create_time` int(11) DEFAULT 0,
  `invalid_at` int(11) DEFAULT 0,
  `member_id` int(11) DEFAULT 0,
  `level_id` int(11) DEFAULT 0,
  `member_use` int(11) DEFAULT '0',
  `use_at` int(11) DEFAULT '0',
  `is_lock` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_checkcode`
--

DROP TABLE IF EXISTS `sa_checkcode`;
CREATE TABLE `sa_checkcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-手机 1-邮箱',
  `sendto` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `is_check` tinyint(4) DEFAULT '0',
  `check_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sendto` (`sendto`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_checkcode_limit`
--

DROP TABLE IF EXISTS `sa_checkcode_limit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sa_checkcode_limit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '限制类型 ip,mobile',
  `key` varchar(100) NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sa_page`
--

DROP TABLE IF EXISTS `sa_page`;
CREATE TABLE `sa_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `title` varchar(100) NOT NULL,
  `vice_title` varchar(100) NOT NULL DEFAULT '' COMMENT '副标题',
  `group` varchar(50) NOT NULL DEFAULT '',
  `icon` varchar(150) NOT NULL DEFAULT '',
  `name` varchar(50) DEFAULT NULL,
  `sort` int(11) DEFAULT 0,
  `status` tinyint(11) DEFAULT 0,
  `use_template` TINYINT NULL DEFAULT 0,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_page_images`
--

DROP TABLE IF EXISTS `sa_page_images`;
CREATE TABLE `sa_page_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_page_group`
--

DROP TABLE IF EXISTS `sa_page_group`;
CREATE TABLE `sa_page_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `group_name` varchar(100) NOT NULL,
  `group` varchar(50) NOT NULL DEFAULT '',
  `sort` int(11) DEFAULT 0,
  `use_template` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_article`
--

DROP TABLE IF EXISTS `sa_article`;
CREATE TABLE `sa_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT NULL,
  `vice_title` varchar(200) DEFAULT NULL,
  `cover` varchar(100) DEFAULT NULL COMMENT '封面图',
  `description` varchar(250) DEFAULT NULL,
  `prop_data` text,
  `content` text,
  `create_time` INT(11) DEFAULT '0',
  `update_time` INT(11) DEFAULT '0',
  `cate_id` INT(11) DEFAULT NULL,
  `user_id` INT(11) DEFAULT NULL,
  `digg` INT(11) DEFAULT '0',
  `comment` INT(11) DEFAULT '0',
  `views` INT(11) DEFAULT '0',
  `type` tinyint(1) DEFAULT '1' COMMENT '1:普通,2:置顶,3:热门,4:推荐',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_article_images`
--

DROP TABLE IF EXISTS `sa_article_images`;
CREATE TABLE `sa_article_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_article_digg`
--

DROP TABLE IF EXISTS `sa_article_digg`;
CREATE TABLE `sa_article_digg` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `device` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_article_comment`
--

DROP TABLE IF EXISTS `sa_article_comment`;
CREATE TABLE `sa_article_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `article_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(150) NOT NULL DEFAULT '',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `device` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `is_anonymous` tinyint(4) NOT NULL DEFAULT '0',
  `content` text,
  `reply_id` int(11) DEFAULT '0',
  `group_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_setting`
--

DROP TABLE IF EXISTS `sa_setting`;
CREATE TABLE `sa_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `group` varchar(50) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_paytype`
--

DROP TABLE IF EXISTS `sa_paytype`;
CREATE TABLE `sa_paytype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `qrcode` varchar(150) DEFAULT NULL,
  `cardno` varchar(30) DEFAULT NULL,
  `cardname` varchar(50) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_feedback`
--

DROP TABLE IF EXISTS `sa_feedback`;
CREATE TABLE `sa_feedback` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(150) NOT NULL DEFAULT '',
  `type` tinyint(4) DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `content` text,
  `reply` varchar(255) DEFAULT '',
  `manager_id` int(11) DEFAULT '0',
  `reply_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `sa_permission`
--

DROP TABLE IF EXISTS `sa_permission`;
CREATE TABLE `sa_permission` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `parent_id` INT NULL,
  `name` VARCHAR(50) NULL,
  `url` VARCHAR(100) NULL,
  `key` VARCHAR(50) NULL,
  `icon` VARCHAR(30) NULL,
  `order_id` INT NULL,
  `disable` TINYINT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

