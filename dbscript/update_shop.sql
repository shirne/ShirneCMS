
INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `order_id`, `disable`)
VALUES
  (6,0,'商城','','Shop','ion-md-cart',0,0),
  (61,2,'分类管理','ProductCategory/index','product_category_index','ion-md-medical',0,0),
  (62,2,'商品管理','Product/index','product_index','ion-md-gift',0,0),
  (63,2,'订单管理','Order/index','order_index','ion-md-list-box',0,0),
  (64,2,'订单统计','OrderStatics/index','order_statics_index','ion-md-list-box',0,0);


CREATE TABLE `sa_product_category` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sa_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `price` DECIMAL(10,2) NULL COMMENT '购买价格',
  `image` varchar(150) DEFAULT NULL,
  `content` text,
  `create_at` int(11) DEFAULT '0',
  `update_at` int(11) DEFAULT '0',
  `level_id` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sa_product_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sa_product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `sa_order` (
  `order_id` INT NOT NULL AUTO_INCREMENT,
  `order_no` VARCHAR(30) NULL,
  `member_id` INT NULL,
  `payamount` DECIMAL(10,2) NULL DEFAULT 0,
  `level_id` INT NULL,
  `create_at` INT NULL DEFAULT 0,
  `pay_at` INT NULL DEFAULT 0,
  `confirm_at` INT NULL DEFAULT 0,
  `rebated` INT NULL DEFAULT 0,
  `rebate_at` INT NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0 COMMENT '订单状态',
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
  PRIMARY KEY (`order_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sa_order_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) DEFAULT '0',
  `product_id` INT(11) DEFAULT '0',
  `product_title` varchar(100) DEFAULT NULL,
  `product_image` varchar(150) DEFAULT NULL,
  `product_price` DECIMAL(10,2) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;