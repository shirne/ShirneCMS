
/*!40101 SET NAMES utf8 */;

--
-- Table structure for table `sa_lang`
--

DROP TABLE IF EXISTS `sa_lang`;
CREATE TABLE `sa_lang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) NOT NULL,
  `table` varchar(30) NOT NULL DEFAULT '',
  `field` varchar(30) DEFAULT '',
  `key_id` int DEFAULT '0',
  `value` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`lang`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_manager`
--

DROP TABLE IF EXISTS `sa_manager`;
CREATE TABLE `sa_manager` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pid` int DEFAULT '0',
  `username` varchar(20) NOT NULL,
  `realname` varchar(20) NOT NULL DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `password` varchar(32) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `create_time` int UNSIGNED DEFAULT '0',
  `update_time` int UNSIGNED DEFAULT '0',
  `login_ip` varchar(50) DEFAULT '',
  `status` tinyint DEFAULT '1' COMMENT '0:禁止登陆 1:正常',
  `type` tinyint DEFAULT '1' COMMENT 'role.type',
  `logintime` int UNSIGNED NULL DEFAULT 0,
  `last_view_member` int NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_manager_login`;
CREATE TABLE `sa_manager_login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `manager_id` int DEFAULT '0',
  `hash` varchar(50) NOT NULL,
  `create_time` int UNSIGNED DEFAULT '0',
  `update_time` int UNSIGNED DEFAULT '0',
  `device` varchar(50) DEFAULT '',
  `create_ip` varchar(50) DEFAULT '',
  `create_user_agent` varchar(300) DEFAULT '',
  `login_time` int UNSIGNED NULL DEFAULT 0,
  `login_ip` varchar(50) DEFAULT '',
  `login_user_agent` varchar(300) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_manager_role`
--
DROP TABLE IF EXISTS `sa_manager_role`;
CREATE TABLE `sa_manager_role` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type` TINYINT NOT NULL DEFAULT 0,
  `label_type` VARCHAR(20) NOT NULL DEFAULT '',
  `role_name` VARCHAR(50) NOT NULL DEFAULT '',
  `global` VARCHAR(200) NULL DEFAULT '',
  `detail` TEXT NULL,
  `create_time` int UNSIGNED DEFAULT '0',
  `update_time` int UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `type_UNIQUE` (`type` ASC)
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
  `manager_id` int DEFAULT '0',
  `other_id` int DEFAULT '0',
  `ip` varchar(50) DEFAULT '',
  `create_time` int UNSIGNED DEFAULT NULL,
  `action` varchar(45) DEFAULT NULL,
  `result` tinyint DEFAULT '1',
  `remark` varchar(250) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `sa_category`
--

DROP TABLE IF EXISTS `sa_category`;
CREATE TABLE `sa_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pid` int DEFAULT 0 COMMENT '父分类ID',
  `title` varchar(100) DEFAULT '' COMMENT '分类名称',
  `short` varchar(20) DEFAULT '' COMMENT '分类简称',
  `name` varchar(50) DEFAULT '' COMMENT '分类别名',
  `icon` varchar(150) DEFAULT '' COMMENT '图标',
  `image` varchar(100) DEFAULT '' COMMENT '大图',
  `sort` int DEFAULT 0 COMMENT '排序',
  `props` TEXT COMMENT '默认属性',
  `fields` TEXT COMMENT '开启的字段',
  `pagesize` int DEFAULT 12 COMMENT '分页',
  `use_template` tinyint DEFAULT 0 COMMENT '独立模板',
  `template_dir` varchar(20) DEFAULT 0 COMMENT '独立模板目录',
  `channel_mode` tinyint DEFAULT 0 COMMENT '频道模式',
  `status` tinyint DEFAULT 1 COMMENT '状态 1为正常 0为关闭',
  `is_lock` tinyint DEFAULT 0 COMMENT '是否锁定',
  `is_comment` tinyint DEFAULT 0 COMMENT '是否开启评论',
  `is_images` tinyint DEFAULT 0 COMMENT '是否有图集',
  `is_attachments` tinyint DEFAULT 0 COMMENT '是否有附件',
  `keywords` varchar(255) DEFAULT '' COMMENT '分类关键词',
  `description` varchar(255) DEFAULT '' COMMENT '分类描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `sa_region`
--

DROP TABLE IF EXISTS `sa_region`;
CREATE TABLE `sa_region` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pid` int DEFAULT NULL COMMENT '父分类ID',
  `title` varchar(100) DEFAULT NULL COMMENT '地区名称',
  `title_en` varchar(150) DEFAULT NULL COMMENT '地区名称',
  `short` varchar(50) DEFAULT NULL COMMENT '地区简称',
  `code` varchar(10) DEFAULT NULL COMMENT '地区简称',
  `name` varchar(50) DEFAULT NULL COMMENT '地区拼音',
  `sort` int DEFAULT 0 COMMENT '排序',
  `description` varchar(255) DEFAULT NULL COMMENT '分类描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_subscribe`
--

DROP TABLE IF EXISTS `sa_subscribe`;
CREATE TABLE `sa_subscribe` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `title` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT '',
  `last_send_time` int UNSIGNED DEFAULT 0,
  `cancel_code` varchar(100) DEFAULT '',
  `is_subscribe` tinyint DEFAULT 1,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_subscribe_email`
--

DROP TABLE IF EXISTS `sa_subscribe_content`;
CREATE TABLE `sa_subscribe_content` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `status` tinyint DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_subscribe_email`
--

DROP TABLE IF EXISTS `sa_subscribe_email`;
CREATE TABLE `sa_subscribe_email` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `account` varchar(100) DEFAULT NULL,
  `type` varchar(10) DEFAULT 'smtp',
  `password` varchar(100) DEFAULT NULL,
  `smtp` varchar(100) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  `ssl` tinyint DEFAULT 1,
  `status` tinyint DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_booth`
--

DROP TABLE IF EXISTS `sa_booth`;
CREATE TABLE `sa_booth` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT '',
  `flag` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(30) NOT NULL DEFAULT '',
  `data` TEXT,
  `locked` tinyint DEFAULT '0',
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  `status` tinyint DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `flag_UNIQUE` (`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_adv_group`
--

DROP TABLE IF EXISTS `sa_adv_group`;
CREATE TABLE `sa_adv_group` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT '',
  `flag` varchar(50) DEFAULT '',
  `width` int DEFAULT 0,
  `height` int DEFAULT 0,
  `ext_set` varchar(500) DEFAULT '',
  `locked` tinyint DEFAULT 0,
  `status` tinyint DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `flag_UNIQUE` (`flag` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_adv_item`
--

DROP TABLE IF EXISTS `sa_adv_item`;
CREATE TABLE `sa_adv_item` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT '' COMMENT '语言',
  `main_id` int DEFAULT 0 COMMENT '主id',
  `group_id` int DEFAULT 0 COMMENT '分组ID',
  `title` varchar(100) DEFAULT '',
  `image` varchar(150) DEFAULT '',
  `video` varchar(150) DEFAULT '',
  `url` varchar(150) DEFAULT '',
  `elements` TEXT,
  `ext_data` TEXT,
  `start_date` int DEFAULT 0,
  `end_date` int DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  `sort` int DEFAULT 0,
  `status` tinyint DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_copyrights`
--

DROP TABLE IF EXISTS `sa_copyrights`;
CREATE TABLE `sa_copyrights` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT '',
  `name` varchar(100) DEFAULT '',
  `content` TEXT,
  `sort` int DEFAULT 0,
  `status` int DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_links`
--

DROP TABLE IF EXISTS `sa_links`;
CREATE TABLE `sa_links` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT '',
  `group` varchar(50) DEFAULT '',
  `logo` varchar(150) DEFAULT '',
  `url` varchar(150) DEFAULT '',
  `sort` int DEFAULT 0,
  `status` int DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_notice`
--

DROP TABLE IF EXISTS `sa_notice`;
CREATE TABLE `sa_notice` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT '' COMMENT '语言',
  `main_id` int DEFAULT 0 COMMENT '主id',
  `title` varchar(100) DEFAULT '',
  `page` varchar(100) DEFAULT '',
  `url` varchar(150) DEFAULT '',
  `status` tinyint DEFAULT 0,
  `manager_id` int DEFAULT '0',
  `create_time` int UNSIGNED DEFAULT '0',
  `update_time` int UNSIGNED DEFAULT '0',
  `summary` VARCHAR(500) DEFAULT '',
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_keywords`
--

DROP TABLE IF EXISTS `sa_keywords`;
CREATE TABLE `sa_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) DEFAULT '',
  `description` varchar(200) DEFAULT '',
  `group` varchar(20) DEFAULT '',
  `image` varchar(150) DEFAULT '',
  `v_hot` int DEFAULT 0,
  `hot` int DEFAULT 0,
  `status` int DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_o_auth`
--

DROP TABLE IF EXISTS `sa_o_auth`;
CREATE TABLE `sa_o_auth` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT '',
  `logo` varchar(150) DEFAULT '',
  `type` varchar(20) DEFAULT '',
  `appid` varchar(50) DEFAULT '',
  `appkey` varchar(50) DEFAULT '',
  `status` tinyint DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `sa_subscribe`
--

DROP TABLE IF EXISTS `sa_subscribe`;
CREATE TABLE `sa_subscribe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT 'email',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `create_time` int UNSIGNED DEFAULT '0',
  `update_time` int UNSIGNED DEFAULT '0',
  `last_time` int UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_member`
--

DROP TABLE IF EXISTS `sa_member`;
CREATE TABLE `sa_member` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称,用于同步微信昵称',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `level_id` int DEFAULT '0' COMMENT '会员级别id',
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `mobile_bind` tinyint DEFAULT '0' COMMENT '手机号码是否绑定',
  `email` varchar(100) DEFAULT '',
  `email_bind` tinyint DEFAULT '0',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `salt` varchar(8) NOT NULL COMMENT '密码盐',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `gender` tinyint DEFAULT '0' COMMENT '性别 0,1=>男,2=>女',
  `birth` int DEFAULT '0' COMMENT '生日日期的timestamp',
  `address` varchar(150) DEFAULT '',
  `province` varchar(50) DEFAULT '' COMMENT '所在省',
  `city` varchar(50) DEFAULT '' COMMENT '所在市',
  `county` varchar(50) DEFAULT '' COMMENT '所在镇',
  `postcode` varchar(20) DEFAULT '' COMMENT '邮政编码',
  `qq` varchar(20) DEFAULT '',
  `wechat` varchar(20) DEFAULT '',
  `alipay` varchar(50) DEFAULT '',
  `create_time` int UNSIGNED DEFAULT '0',
  `update_time` int UNSIGNED DEFAULT '0',
  `delete_time` int UNSIGNED NULL DEFAULT 0 COMMENT '删除状态',
  `login_ip` varchar(50) DEFAULT '',
  `is_expired` tinyint DEFAULT '0' COMMENT '0:正常 1:过期',
  `expired_time` bigint DEFAULT '0' COMMENT '过期时间',
  `status` tinyint DEFAULT '1' COMMENT '0:禁止登陆 1:正常',
  `type` tinyint DEFAULT '1' COMMENT '1:普通会员 ',
  `credit` int DEFAULT '0' COMMENT '积分',
  `money` int DEFAULT '0' COMMENT '余额',
  `reward` int DEFAULT '0' COMMENT '奖励,用于提现',
  `froze_money` int DEFAULT '0' COMMENT '冻结余额',
  `froze_credit` int DEFAULT '0' COMMENT '冻结积分',
  `froze_reward` int DEFAULT '0' COMMENT '冻结奖励',
  `total_cashin` int DEFAULT '0' COMMENT '总提现',
  `total_recharge` int DEFAULT '0' COMMENT '总充值',
  `total_consume` int DEFAULT '0' COMMENT '总消费',
  `referer` int DEFAULT '0' COMMENT '推荐人id',
  `is_agent` tinyint DEFAULT '0' COMMENT '代理级别,0为非代理',
  `agent_province` varchar(50) DEFAULT '' COMMENT '代理省',
  `agent_city` varchar(50) DEFAULT '' COMMENT '代理市',
  `agent_county` varchar(50) DEFAULT '' COMMENT '代理县区',
  `agentcode` varchar(10) DEFAULT '' COMMENT '代理推广码',
  `recom_total` int DEFAULT '0' COMMENT '直推总数',
  `recom_count` int DEFAULT '0' COMMENT '直推总数(仅代理)',
  `recom_performance` BIGINT DEFAULT 0 COMMENT '直推总业绩',
  `total_performance` BIGINT DEFAULT 0 COMMENT '推荐团队总业绩',
  `team_count` int DEFAULT '0' COMMENT '团队总数',
  `logintime` int UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `mobile` (`mobile`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `sa_member_login`;
CREATE TABLE `sa_member_login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int DEFAULT '0',
  `hash` varchar(50) NOT NULL,
  `create_time` int UNSIGNED DEFAULT '0',
  `update_time` int UNSIGNED DEFAULT '0',
  `device` varchar(50) DEFAULT '',
  `create_ip` varchar(50) DEFAULT '',
  `create_user_agent` varchar(300) DEFAULT '',
  `login_time` int UNSIGNED NULL DEFAULT 0,
  `login_ip` varchar(50) DEFAULT '',
  `login_user_agent` varchar(300) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_authen`;

CREATE TABLE `sa_member_authen` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL DEFAULT 0,
  `level_id` int(4) NOT NULL DEFAULT 0,
  `type` VARCHAR(20) NOT NULL DEFAULT '',
  `realname` VARCHAR(20) NOT NULL,
  `mobile` VARCHAR(20) NOT NULL,
  `province` VARCHAR(20) NOT NULL,
  `city` VARCHAR(20) NOT NULL,
  `id_no` VARCHAR(50) NOT NULL,
  `image` VARCHAR(150) NOT NULL,
  `image2` VARCHAR(150) NOT NULL,
  `validate_time` int UNSIGNED NOT NULL DEFAULT 0,
  `create_time` int UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int UNSIGNED NOT NULL DEFAULT 0,
  `authen_time` int UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0,
  `reason` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_pay_order`;
CREATE TABLE `sa_pay_order` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `member_id` INT NULL DEFAULT 0,
  `order_no` VARCHAR(30) NULL,
  `order_type` VARCHAR(20) DEFAULT '',
  `pay_data` TEXT,
  `pay_id` INT NULL DEFAULT '0',
  `pay_type` VARCHAR(20) NULL DEFAULT '',
  `appid` VARCHAR(30) NULL DEFAULT '',
  `prepay_id` VARCHAR(50) NULL DEFAULT '',
  `trade_type` VARCHAR(20) NULL DEFAULT '' COMMENT '交易类型',
  `order_id` INT NULL DEFAULT 0,
  `create_time` int UNSIGNED NULL DEFAULT 0,
  `pay_time` int UNSIGNED NULL DEFAULT 0,
  `pay_amount` INT NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0,
  `pay_bill` VARCHAR(40) NULL DEFAULT '',
  `time_end` VARCHAR(20) NULL DEFAULT '',
  `is_refund` TINYINT NULL DEFAULT 0 COMMENT '是否退款',
  `refund_fee` DECIMAL(10,2) NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sa_pay_order_refund` (
 `id` INT NOT NULL AUTO_INCREMENT,
 `member_id` int(10) unsigned NOT NULL,
 `order_id` int(10) unsigned NOT NULL,
 `refund_no` VARCHAR(20) NOT NULL,
 `refund_fee` DECIMAL(10,2) NULL DEFAULT 0,
 `status` TINYINT NULL DEFAULT 0 COMMENT '状态 0-未退款 1- 退款中 2- 已退款',
 `reason` VARCHAR(50) NOT NULL,
 `refund_result` VARCHAR(50) NOT NULL,
 `refund_time` int UNSIGNED NULL DEFAULT 0,
 `create_time` int UNSIGNED NULL DEFAULT 0,
 `update_time` int UNSIGNED NULL DEFAULT 0,
 PRIMARY KEY (`id`),
 UNIQUE KEY `refund_no`(`refund_no`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '订单退款记录';

DROP TABLE IF EXISTS `sa_member_freeze`;

CREATE TABLE `sa_member_freeze` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL DEFAULT 0,
  `award_log_id` int NOT NULL DEFAULT 0,
  `amount` int NOT NULL DEFAULT '0' COMMENT '金额 单位分',
  `create_time` int UNSIGNED NOT NULL DEFAULT 0,
  `freeze_time` int UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_token`;

CREATE TABLE `sa_member_token` (
  `token_id` BIGINT NOT NULL AUTO_INCREMENT,
  `member_id` INT UNSIGNED NULL DEFAULT 0,
  `platform` VARCHAR(30) NULL,
  `appid` VARCHAR(30) NULL,
  `token` VARCHAR(50) NULL,
  `create_time` int UNSIGNED NULL DEFAULT 0,
  `update_time` int UNSIGNED NULL DEFAULT 0,
  `expire_in` INT NULL DEFAULT 720,
  `refresh_token` VARCHAR(50) NULL,
  PRIMARY KEY (`token_id`),
  KEY `member_id` (`member_id`),
  KEY `token` (`token`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_oauth_app`;

CREATE TABLE `sa_oauth_app` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `platform` VARCHAR(20) NULL,
  `appid` VARCHAR(30) NULL,
  `appsecret` VARCHAR(50) NULL,
  `create_time` int UNSIGNED NULL DEFAULT 0,
  `update_time` int UNSIGNED NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `appid` (`appid`)
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
  `attachment` VARCHAR(150) NULL DEFAULT '',
  `content` TEXT NULL,
  `create_time` int UNSIGNED NULL DEFAULT 0,
  `update_time` int UNSIGNED NULL DEFAULT 0,
  `show_time` INT NULL,
  `read_time` INT NULL DEFAULT 0,
  `delete_time` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`),
  INDEX `member_id`(`member_id`),
  INDEX `type`(`type`),
  INDEX `show_time`(`show_time`),
  INDEX `delete_time`(`delete_time`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `sa_member_level`;

CREATE TABLE `sa_member_level` (
  `level_id` INT NOT NULL AUTO_INCREMENT,
  `level_name` VARCHAR(30) NULL,
  `short_name` VARCHAR(10) NULL,
  `style` VARCHAR(10) default 'secondary',
  `is_default` TINYINT NULL DEFAULT 0,
  `upgrade_type` TINYINT NULL DEFAULT 0 COMMENT '1: 累计消费升级, 2: 购买升级',
  `diy_price` tinyint NULL DEFAULT 0 COMMENT '自定义价格',
  `level_price` DECIMAL(10,2) NULL COMMENT '购买价格/累计金额',
  `discount` TINYINT NULL DEFAULT 100 COMMENT '会员折扣',
  `is_agent` TINYINT NULL DEFAULT 0 COMMENT '是否代理组',
  `sort` INT NULL DEFAULT 0,
  `commission_layer` INT NULL COMMENT '分佣层数',
  `commission_limit` INT NULL DEFAULT 0 COMMENT '分佣本金限制',
  `commission_percent` VARCHAR(200) NULL COMMENT '分佣奖励',
  PRIMARY KEY (`level_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_level_log`;
CREATE TABLE `sa_member_level_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `level_id` int NOT NULL,
  `amount` int DEFAULT '0' COMMENT '金额 单位分',
  `pay_type` VARCHAR(20) NULL COMMENT '付款方式',
  `create_time` int UNSIGNED DEFAULT NULL,
  `payed_time` int UNSIGNED DEFAULT NULL,
  `start_time` bigint DEFAULT NULL,
  `end_time` bigint DEFAULT NULL,
  `status` tinyint DEFAULT '0',
  `remark` varchar(45) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_agent`;

CREATE TABLE `sa_member_agent` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NULL,
  `short_name` VARCHAR(10) NULL,
  `style` VARCHAR(10) default 'secondary',
  `is_default` TINYINT NULL DEFAULT 0,
  `recom_count` INT NULL DEFAULT 0,
  `team_count` INT NULL DEFAULT 0,
  `recom_performance` BIGINT DEFAULT 0,
  `total_performance` BIGINT DEFAULT 0,
  `sale_award` INT NULL DEFAULT 0,
  `global_sale_award` INT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_agent_log`;

CREATE TABLE `sa_member_agent_log` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` INT NULL DEFAULT 0,
  `agent_id` INT NULL DEFAULT 0,
  `type` VARCHAR(10) NULL,
  `remark` VARCHAR(100) NULL DEFAULT '',
  `create_time` int UNSIGNED NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_invoice`;

CREATE TABLE `sa_member_invoice` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL DEFAULT 0,
  `type` tinyint NOT NULL DEFAULT 0,
  `title` VARCHAR(100) NOT NULL,
  `telephone` VARCHAR(50) NOT NULL,
  `address` VARCHAR(150) NOT NULL,
  `bank` VARCHAR(60) NOT NULL,
  `caedno` VARCHAR(50) NOT NULL,
  `tax_no` VARCHAR(50) NOT NULL,
  `create_time` int UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int UNSIGNED NOT NULL DEFAULT 0,
  `is_default` TINYINT NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_address`;

CREATE TABLE `sa_member_address` (
  `address_id` INT NOT NULL AUTO_INCREMENT,
  `member_id` INT NULL,
  `receive_name` VARCHAR(50) NULL,
  `mobile` VARCHAR(30) NULL,
  `province` VARCHAR(50) NULL,
  `city` VARCHAR(50) NULL,
  `area` VARCHAR(50) NULL,
  `street` VARCHAR(50) NULL,
  `address` VARCHAR(150) NULL,
  `code` VARCHAR(10) NULL,
  `locate` VARCHAR(100) NULL,
  `is_default` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`address_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_favourite`;

CREATE TABLE `sa_member_favourite` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL DEFAULT 0,
  `fav_type` varchar(30) DEFAULT NULL,
  `fav_id` int DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  `fav_title` varchar(50) DEFAULT '',
  `fav_image` varchar(150) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_member_log`;

CREATE TABLE `sa_member_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INT NULL DEFAULT 0,
  `other_id` int DEFAULT '0',
  `model` VARCHAR(45) NULL,
  `ip` VARCHAR(50) DEFAULT '',
  `create_time` int UNSIGNED NULL,
  `action` VARCHAR(45) NULL,
  `result` TINYINT NULL DEFAULT 1,
  `remark` VARCHAR(250) NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `action`(`action`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_card`;

CREATE TABLE `sa_member_card` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL DEFAULT 0,
  `cardno` varchar(30) DEFAULT NULL,
  `bankname` varchar(50) DEFAULT NULL,
  `cardname` varchar(50) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `is_default` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_member_recharge`
--

DROP TABLE IF EXISTS `sa_member_recharge`;
CREATE TABLE `sa_member_recharge` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `platform` VARCHAR(30) NULL,
  `paytype_id` int NOT NULL,
  `amount` int DEFAULT '0' COMMENT '金额 单位分',
  `create_time` int UNSIGNED DEFAULT NULL,
  `pay_bill` varchar(150) DEFAULT '',
  `status` tinyint DEFAULT '0',
  `remark` varchar(45) DEFAULT '',
  `audit_time` int UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `sa_member_cashin`
--

DROP TABLE IF EXISTS `sa_member_cashin`;
CREATE TABLE `sa_member_cashin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `platform` VARCHAR(30) NULL,
  `appid` varchar(30) DEFAULT '',
  `form_id` varchar(50) DEFAULT '',
  `cashtype` varchar(20) DEFAULT '',
  `paytype` varchar(20) DEFAULT '',
  `amount` int DEFAULT '0' COMMENT '金额 单位分',
  `cash_fee` int NULL DEFAULT '0',
  `real_amount` int DEFAULT '0',
  `audit_time` int UNSIGNED DEFAULT '0',
  `payment_time` int UNSIGNED DEFAULT 0,
  `fail_time` int UNSIGNED DEFAULT 0,
  `bank_id` int DEFAULT '0',
  `bank` varchar(50) DEFAULT '',
  `bank_name` varchar(40) DEFAULT '',
  `card_name` varchar(40) DEFAULT '',
  `cardno` varchar(40) DEFAULT '',
  `status` tinyint DEFAULT '0',
  `remark` varchar(50) DEFAULT '',
  `reason` varchar(100) NULL DEFAULT '',
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_member_money_log`
--

DROP TABLE IF EXISTS `sa_member_money_log`;
CREATE TABLE `sa_member_money_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `from_member_id` int NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL,
  `before` int DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `after` int DEFAULT NULL,
  `field` varchar(30) DEFAULT 'money',
  `reson` varchar(100) DEFAULT NULL,
  `create_time` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY  `type`(`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_award_log`;

CREATE TABLE `sa_award_log`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `order_id` int NOT NULL,
  `type` varchar(20) DEFAULT '',
  `field` varchar(20) DEFAULT '',
  `from_member_id` int NOT NULL,
  `amount` int DEFAULT '0' COMMENT '金额 单位分',
  `real_amount` int DEFAULT '0',
  `status` tinyint DEFAULT 0,
  `give_time` int UNSIGNED DEFAULT 0,
  `cancel_time` int UNSIGNED DEFAULT 0,
  `create_time` int UNSIGNED DEFAULT NULL,
  `remark` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sa_member_oauth`;

CREATE TABLE `sa_member_oauth` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL DEFAULT 0,
  `type` varchar(20) DEFAULT NULL COMMENT 'qq/sina/github/weixin/wxapp',
  `type_id` INT DEFAULT 0,
  `openid` varchar(64) DEFAULT '',
  `unionid` varchar(64) DEFAULT '',
  `subscribe_time` int UNSIGNED DEFAULT 0,
  `data` TEXT,
  `create_time` int UNSIGNED DEFAULT 0,
  `update_time` int UNSIGNED DEFAULT 0,
  `is_follow` tinyint DEFAULT 0,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `sa_invite_code`
--

DROP TABLE IF EXISTS `sa_invite_code`;
CREATE TABLE `sa_invite_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `create_time` int UNSIGNED DEFAULT 0,
  `invalid_time` int UNSIGNED DEFAULT 0,
  `member_id` int DEFAULT 0,
  `level_id` int DEFAULT 0,
  `member_use` int DEFAULT '0',
  `use_time` int UNSIGNED DEFAULT '0',
  `is_lock` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_checkcode`
--

DROP TABLE IF EXISTS `sa_checkcode`;
CREATE TABLE `sa_checkcode` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` tinyint NOT NULL DEFAULT '0' COMMENT '0-手机 1-邮箱',
  `sendto` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `create_time` int UNSIGNED DEFAULT NULL,
  `is_check` tinyint DEFAULT '0',
  `check_time` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sendto` (`sendto`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_checkcode_limit`
--

DROP TABLE IF EXISTS `sa_checkcode_limit`;
CREATE TABLE `sa_checkcode_limit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '限制类型 ip,mobile',
  `key` varchar(100) NOT NULL,
  `create_time` int UNSIGNED DEFAULT NULL,
  `count` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_page`
--

DROP TABLE IF EXISTS `sa_page`;
CREATE TABLE `sa_page` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int DEFAULT NULL COMMENT '主id',
  `title` varchar(100) NOT NULL,
  `vice_title` varchar(100) NOT NULL DEFAULT '' COMMENT '副标题',
  `group` varchar(50) NOT NULL DEFAULT '',
  `icon` varchar(150) NOT NULL DEFAULT '',
  `image` varchar(150) NOT NULL DEFAULT '',
  `name` varchar(50) DEFAULT NULL,
  `sort` int DEFAULT 0,
  `status` tinyint DEFAULT 0,
  `use_template` TINYINT NULL DEFAULT 0,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_page_images`
--

DROP TABLE IF EXISTS `sa_page_images`;
CREATE TABLE `sa_page_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `page_id` int DEFAULT NULL,
  `sort` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_page_group`
--

DROP TABLE IF EXISTS `sa_page_group`;
CREATE TABLE `sa_page_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int DEFAULT NULL COMMENT '主id',
  `group_name` varchar(100) NOT NULL,
  `group` varchar(50) NOT NULL DEFAULT '',
  `sort` int DEFAULT 0,
  `use_template` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_article`
--

DROP TABLE IF EXISTS `sa_article`;
CREATE TABLE `sa_article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int DEFAULT NULL COMMENT '主id',
  `user_id` int DEFAULT 0,
  `copyright_id` int DEFAULT 0,
  `name` varchar(100) DEFAULT '',
  `title` varchar(100) DEFAULT '',
  `vice_title` varchar(200) DEFAULT '',
  `cover` varchar(100) DEFAULT '' COMMENT '封面图',
  `keywords` varchar(150) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `source` varchar(150) DEFAULT '',
  `prop_data` text,
  `content` text,
  `create_time` int UNSIGNED DEFAULT '0',
  `update_time` int UNSIGNED DEFAULT '0',
  `cate_id` int DEFAULT 0,
  `digg` int DEFAULT '0',
  `v_digg` int DEFAULT '0',
  `close_comment` tinyint NOT NULL DEFAULT '0',
  `comment` int DEFAULT '0',
  `views` int DEFAULT '0',
  `v_views` int DEFAULT '0',
  `type` tinyint UNSIGNED DEFAULT '1' COMMENT '1:普通,2:置顶,4:热门,8:推荐',
  `template` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_article_images`
--

DROP TABLE IF EXISTS `sa_article_images`;
CREATE TABLE `sa_article_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `article_id` int DEFAULT NULL,
  `sort` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_article_digg`
--

DROP TABLE IF EXISTS `sa_article_digg`;
CREATE TABLE `sa_article_digg` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `article_id` int DEFAULT NULL,
  `member_id` int DEFAULT NULL,
  `create_time` int UNSIGNED NOT NULL DEFAULT '0',
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
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL DEFAULT '0',
  `article_id` int NOT NULL DEFAULT '0',
  `nickname` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `create_time` int UNSIGNED NOT NULL DEFAULT '0',
  `device` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '0',
  `is_anonymous` tinyint NOT NULL DEFAULT '0',
  `content` text,
  `reply_id` int DEFAULT '0',
  `group_id` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_setting`
--

DROP TABLE IF EXISTS `sa_setting`;
CREATE TABLE `sa_setting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `group` varchar(50) NOT NULL DEFAULT '',
  `sort` int NOT NULL DEFAULT '0',
  `is_sys` tinyint NOT NULL DEFAULT '0',
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
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `qrcode` varchar(150) DEFAULT NULL,
  `cardno` varchar(30) DEFAULT NULL,
  `cardname` varchar(50) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sa_feedback`
--

DROP TABLE IF EXISTS `sa_feedback`;
CREATE TABLE `sa_feedback` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL DEFAULT '0',
  `realname` varchar(30) NOT NULL DEFAULT '',
  `mobile` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `type` tinyint DEFAULT '0',
  `create_time` int UNSIGNED NOT NULL DEFAULT '0',
  `update_time` int UNSIGNED NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '0',
  `content` text,
  `reply` varchar(255) DEFAULT '',
  `manager_id` int DEFAULT '0',
  `reply_time` int UNSIGNED DEFAULT '0',
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
  `sort_id` INT NULL,
  `disable` TINYINT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

