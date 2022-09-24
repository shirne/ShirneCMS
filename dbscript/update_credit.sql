
INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES
  (4,0,'积分商城','','CreditShop','ion-md-cart',2,0),
  (41,4,'分类管理','credit.category/index','credit_category_index','ion-md-medical',0,0),
  (42,4,'商品管理','credit.goods/index','credit_goods_index','ion-md-gift',0,0),
  (43,4,'积分策略','credit.promotion/index','credit_promotion_index','ion-md-cog',0,0),
  (44,4,'订单管理','credit.order/index','credit_order_index','ion-md-list-box',0,0);

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'credit_pagetitle', '商城标题', 'text', 'credit', '0', 1 , '积分商城', '', ''),
  ( 'credit_keyword', '关键字', 'text', 'credit', '0', 1 , '', '积分商城', ''),
  ( 'credit_description', '积分商城简介', 'text', 'credit', '0', 1 , '', '', ''),
  ( 'credit_rate', '下单赠送积分', 'text', 'credit', '0', 1 , '100', '', '');


DROP TABLE IF EXISTS `sa_goods_category`;
CREATE TABLE `sa_goods_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL COMMENT '父分类ID',
  `title` varchar(100) DEFAULT NULL COMMENT '分类名称',
  `short` varchar(20) DEFAULT NULL COMMENT '分类简称',
  `name` varchar(50) DEFAULT NULL COMMENT '分类别名',
  `icon` varchar(100) DEFAULT NULL COMMENT '图标',
  `image` varchar(100) DEFAULT NULL COMMENT '大图',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `keywords` varchar(255) DEFAULT NULL COMMENT '分类关键词',
  `description` varchar(255) DEFAULT NULL COMMENT '分类描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_goods`;
CREATE TABLE `sa_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `vice_title` varchar(200) DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL,
  `goods_no` varchar(50) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `prop_data` text,
  `price` DECIMAL(10,2) DEFAULT 0 COMMENT '兑换价格',
  `market_price` DECIMAL(10,2) DEFAULT 0 COMMENT '市场价格',
  `content` text,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `levels` varchar(100) DEFAULT '',
  `storage` int(11) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  `sale` int(11) DEFAULT '0' COMMENT '总销量',
  `type` tinyint(4) DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_goods_images`;
CREATE TABLE `sa_goods_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_credit_order`;
CREATE TABLE `sa_credit_order` (
  `order_id` INT NOT NULL AUTO_INCREMENT,
  `order_no` VARCHAR(30) NULL,
  `member_id` INT NULL,
  `paycredit` DECIMAL(10,2) NULL DEFAULT 0,
  `payamount` DECIMAL(10,2) NULL DEFAULT 0,
  `create_time` INT NULL DEFAULT 0,
  `pay_type` VARCHAR(20) NULL COMMENT '付款方式',
  `pay_time` INT NULL DEFAULT 0,
  `deliver_time` INT NULL DEFAULT 0,
  `confirm_time` INT NULL DEFAULT 0,
  `comment_time` INT NULL DEFAULT 0,
  `refund_time` INT NULL DEFAULT 0,
  `cancel_time` INT NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0 COMMENT '订单状态',
  `delete_time` INT NULL DEFAULT 0 COMMENT '删除状态',
  `remark` VARCHAR(250) NULL,
  `address_id` INT NULL,
  `recive_name` VARCHAR(45) NULL,
  `mobile` VARCHAR(45) NULL,
  `province` VARCHAR(50) NULL,
  `city` VARCHAR(50) NULL,
  `area` VARCHAR(50) NULL,
  `address` VARCHAR(150) NULL,
  `reason` VARCHAR(100) NULL,
  `express_no` VARCHAR(100) NULL,
  `express_code` VARCHAR(20) NULL,
  `express_data` TEXT NULL,
  `express_time` INT NULL DEFAULT 0,
  PRIMARY KEY (`order_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_credit_order_goods`;
CREATE TABLE `sa_credit_order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) DEFAULT '0',
  `goods_id` INT(11) DEFAULT '0',
  `goods_title` varchar(100) DEFAULT NULL,
  `goods_image` varchar(150) DEFAULT NULL,
  `goods_orig_price` DECIMAL(10,2) DEFAULT NULL,
  `goods_price` DECIMAL(10,2) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;