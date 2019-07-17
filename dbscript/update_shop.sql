
INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES
  (6,0,'商城','','Shop','ion-md-cart',2,0),
  (61,6,'分类管理','ProductCategory/index','product_category_index','ion-md-medical',0,0),
  (62,6,'品牌管理','ProductBrand/index','product_brand_index','ion-md-bookmark',0,0),
  (63,6,'商品管理','Product/index','product_index','ion-md-gift',0,0),
  (64,6,'优惠券管理','ProductCoupon/index','product_coupon_index','ion-md-pricetags',0,0),
  (65,6,'订单管理','Order/index','order_index','ion-md-list-box',0,0),
  (66,6,'订单统计','OrderStatics/index','order_statics_index','ion-md-stats',0,0);

DROP TABLE IF EXISTS `sa_member_cart`;

CREATE TABLE `sa_member_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` INT(11) DEFAULT '0',
  `product_id` INT(11) DEFAULT '0',
  `sku_id` INT(11) DEFAULT '0',
  `product_title` varchar(100) DEFAULT NULL,
  `product_image` varchar(150) DEFAULT NULL,
  `product_price` DECIMAL(10,2) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_specifications`;

CREATE TABLE `sa_specifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '规格名称',
  `data` text COMMENT '规格数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `sa_product_category`;

CREATE TABLE `sa_product_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL COMMENT '父分类ID',
  `title` varchar(100) DEFAULT NULL COMMENT '分类名称',
  `short` varchar(20) DEFAULT NULL COMMENT '分类简称',
  `name` varchar(50) DEFAULT NULL COMMENT '分类别名',
  `icon` varchar(100) DEFAULT NULL COMMENT '图标',
  `image` varchar(100) DEFAULT NULL COMMENT '大图',
  `specs` varchar(200) DEFAULT NULL COMMENT '绑定规格',
  `props` TEXT COMMENT '绑定属性',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `keywords` varchar(255) DEFAULT NULL COMMENT '分类关键词',
  `description` varchar(255) DEFAULT NULL COMMENT '分类描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_brand`;

CREATE TABLE `sa_product_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT NULL,
  `logo` varchar(150) DEFAULT '',
  `url` varchar(150) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_category_brand`;

CREATE TABLE `sa_product_category_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) DEFAULT 0,
  `brand_id` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_coupon`;

CREATE TABLE `sa_product_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT NULL,
  `bind_type` tinyint(11) DEFAULT 0 COMMENT '0-通用 1-类目 2-品牌 3-指定商品 4-指定sku',
  `cate_id` int(11) DEFAULT 0,
  `brand_id` int(11) DEFAULT 0,
  `product_id` int(11) DEFAULT 0,
  `sku_id` int(11) DEFAULT 0,
  `type` tinyint(4) DEFAULT 0 COMMENT '0- 满减 1-折扣',
  `limit` int(11) DEFAULT 0,
  `amount` int(11) DEFAULT 0,
  `discount` int(11) DEFAULT 0,
  `start_time` int(11) DEFAULT 0,
  `end_time` int(11) DEFAULT 0,
  `status` tinyint(11) DEFAULT 1,
  `stock` int(11) DEFAULT 1000 COMMENT '-1 无限库存',
  `receive` int(11) DEFAULT 0,
  `expiry_type` tinyint(11) DEFAULT 0,
  `expiry_time` int(11) DEFAULT 0,
  `expiry_day` int(11) DEFAULT 0,
  `cost_credit` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_coupon`;

CREATE TABLE `sa_member_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) DEFAULT 0,
  `member_id` int(11) DEFAULT 0,
  `title` varchar(100) DEFAULT '',
  `cate_id` int(11) DEFAULT 0,
  `brand_id` int(11) DEFAULT 0,
  `product_id` int(11) DEFAULT 0,
  `sku_id` int(11) DEFAULT 0,
  `create_time` int(11) DEFAULT 0,
  `expiry_time` int(11) DEFAULT 0,
  `status` tinyint(11) DEFAULT 1,
  `use_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product`;

CREATE TABLE `sa_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `cate_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `vice_title` varchar(200) DEFAULT NULL,
  `goods_no` varchar(50) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `spec_data` text,
  `prop_data` text,
  `max_price` DECIMAL(10,2) DEFAULT 0 COMMENT '最高价格',
  `min_price` DECIMAL(10,2) DEFAULT 0 COMMENT '最低价格',
  `content` text,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `level_id` int(11) DEFAULT 0,
  `levels` varchar(100) DEFAULT '',
  `storage` int(11) DEFAULT '0',
  `sale` int(11) DEFAULT '0' COMMENT '总销量',
  `type` tinyint(4) DEFAULT '1',
  `is_commission` tinyint(4) DEFAULT '1',
  `commission_percent`  VARCHAR(200) DEFAULT '',
  `is_discount` tinyint(4) DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_sku`;

CREATE TABLE `sa_product_sku` (
  `sku_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `specs` text,
  `image` varchar(150) DEFAULT NULL,
  `goods_no` varchar(50) DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT 0 COMMENT '购买价格',
  `market_price` DECIMAL(10,2) DEFAULT 0 COMMENT '市场价格',
  `cost_price` DECIMAL(10,2) DEFAULT 0 COMMENT '成本价格',
  `weight` int(11) DEFAULT '0',
  `storage` int(11) DEFAULT '0',
  `sale` int(11) DEFAULT '0',
  PRIMARY KEY (`sku_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `sa_product_comment`;

CREATE TABLE `sa_product_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `sku_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `device` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `is_anonymous` tinyint(4) NOT NULL DEFAULT '0',
  `content` text,
  `reply_time` int(11) NOT NULL DEFAULT '0',
  `reply_user_id` int(11) NOT NULL DEFAULT '0',
  `reply` text,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_images`;

CREATE TABLE `sa_product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `sku_id` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `sa_order`;

CREATE TABLE `sa_order` (
  `order_id` INT NOT NULL AUTO_INCREMENT,
  `order_no` VARCHAR(30) NULL,
  `member_id` INT NULL,
  `payamount` DECIMAL(10,2) NULL DEFAULT 0,
  `commission_amount` DECIMAL(10,2) NULL DEFAULT 0,
  `commission_special` TEXT NULL,
  `level_id` INT NULL,
  `create_time` INT NULL DEFAULT 0,
  `pay_time` INT NULL DEFAULT 0,
  `confirm_time` INT NULL DEFAULT 0,
  `cancel_time` INT NULL DEFAULT 0,
  `rebated` INT NULL DEFAULT 0,
  `rebate_time` INT NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0 COMMENT '订单状态',
  `isaudit` TINYINT NULL DEFAULT 0 COMMENT '审核状态',
  `delete_time` INT NULL DEFAULT 0 COMMENT '删除状态',
  `remark` VARCHAR(250) NULL,
  `address_id` INT NULL,
  `recive_name` VARCHAR(45) NULL,
  `mobile` VARCHAR(45) NULL,
  `province` VARCHAR(45) NULL,
  `city` VARCHAR(45) NULL,
  `area` VARCHAR(45) NULL,
  `address` VARCHAR(150) NULL,
  `express_no` VARCHAR(100) NULL,
  `express_code` VARCHAR(20) NULL,
  `type` TINYINT NULL DEFAULT 1,
  PRIMARY KEY (`order_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_order_product`;

CREATE TABLE `sa_order_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) DEFAULT '0',
  `member_id` INT NULL,
  `product_id` INT(11) DEFAULT '0',
  `sku_id` INT(11) DEFAULT '0',
  `sku_specs` text,
  `product_title` varchar(100) DEFAULT NULL,
  `product_image` varchar(150) DEFAULT NULL,
  `product_orig_price` DECIMAL(10,2) DEFAULT NULL,
  `product_price` DECIMAL(10,2) DEFAULT NULL,
  `product_weight` INT(11) DEFAULT 0,
  `count` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;