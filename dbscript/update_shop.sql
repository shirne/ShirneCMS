
INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES
  (3,0,'商城','','Shop','ion-md-cart',2,0),
  (31,3,'分类管理','shop.category/index','shop_category_index','ion-md-medical',0,0),
  (32,3,'品牌管理','shop.brand/index','shop_brand_index','ion-md-bookmark',0,0),
  (33,3,'商品管理','shop.product/index','shop_product_index','ion-md-gift',0,0),
  (34,3,'优惠券管理','shop.coupon/index','shop_coupon_index','ion-md-pricetags',0,0),
  (35,3,'订单管理','shop.order/index','shop_order_index','ion-md-list-box',0,0),
  (36,3,'订单统计','shop.orderStatics/index','shop_order_statics_index','ion-md-stats',0,0),
  (37,3,'运费模板','shop.postage/index','shop_postage_index','ion-md-train',0,0),
  (38,3,'帮助中心','shop.help/index','shop_help_index','ion-md-help-circle',0,0),
  (39,3,'商城配置','shop.promotion/index','shop_promotion_index','ion-md-cog',0,0);

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'shop_pagetitle', '商城标题', 'text', 'shop', '0', 1 , '商城', '', ''),
  ( 'shop_keyword', '商城关键字', 'text', 'shop', '0', 1 , '商城', '', ''),
  ( 'shop_description', '商城简介', 'text', 'shop', '0', 1 , '', '', ''),
  ( 'shop_close', '关闭下单', 'radio', 'shop', '0',1, '1', '', '0:关闭\r\n1:开启'),
  ( 'shop_close_desc', '关闭说明', 'text', 'shop', '0', 1 , '暂时不支持下单', '', ''),
  ( 'shop_order_pay_limit', '订单支付超时', 'text', 'shop', '0', 1 , '', '', ''),
  ( 'shop_order_refund_limit', '订单退款限时', 'text', 'shop', '0', 1 , '', '', ''),
  ( 'shop_order_receive_limit', '订单默认收货', 'text', 'shop', '0', 1 , '', '', ''),
  ( 'shop_order_notice', '下单说明', 'text', 'shop', '0', 1 , '', '', '');

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'poster_background', '分享图背景', 'image', 'poster', '0', 1 , '', 'png格式，建议尺寸 1080px x 1920px', ''),
  ( 'poster_bgset','二维码/头像遮掩', 'radio', 'poster', 0, 1, '0', '需要背景图对应二维码和头像的位置设计为透明，可将周边裁为圆形或其它形状', '1:开启\r\n0:关闭'),
  ( 'poster_avatar', '用户头像', 'json', 'poster', '0', 1 , '', '', ''),
  ( 'poster_nickname', '用户昵称', 'json', 'poster', '0', 1 , '', '', ''),
  ( 'poster_agentcode', '推荐码', 'json', 'poster', '0', 1 , '', '', ''),
  ( 'poster_qrcode', '二维码位置', 'json', 'poster', '0', 1 , '', '', ''),
  ( 'poster_qrlogo', '二维码LOGO', 'image', 'poster', '0', 1 , '', '', '');

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'share_background', '分享图背景', 'image', 'share', '0', 1 , '', 'png格式，建议尺寸 1080px x 1920px', ''),
  ( 'share_bgset','二维码/头像遮掩', 'radio', 'share', 0, 1, '0', '需要背景图对应二维码和头像，产品图的位置设计为透明，可将周边裁为圆形或其它形状', '1:开启\r\n0:关闭'),
  ( 'share_avatar', '用户头像', 'json', 'share', '0', 1 , '', '', ''),
  ( 'share_nickname', '用户昵称', 'json', 'share', '0', 1 , '', '', ''),
  ( 'share_qrcode', '二维码位置', 'json', 'share', '0', 1 , '', '', ''),
  ( 'share_qrlogo', '二维码LOGO', 'image', 'share', '0', 1 , '', '', ''),
  ( 'share_image', '产品图', 'json', 'share', '0', 1 , '', '', ''),
  ( 'share_title', '产品名称', 'json', 'share', '0', 1 , '', '', ''),
  ( 'share_vice_title', '产品规格', 'json', 'share', '0', 1 , '', '', ''),
  ( 'share_price', '产品价格', 'json', 'share', '0', 1 , '', '', '');

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'message_bind_agent', '绑定推荐人', 'text', 'message', '0', 1 , '', '可用变量 用户昵称:[username] 代理昵称:[agent] 用户ID:[userid] 代理ID:[agentid]', ''),
  ( 'message_become_agent', '成为代理', 'text', 'message', '0', 1 , '', '可用变量 用户昵称:[username] 用户ID:[userid]', ''),
  ( 'message_upgrade_agent', '升级代理', 'text', 'message', '0', 1 , '', '可用变量 用户昵称:[username] 用户ID:[userid] 代理等级:[agent]', ''),
  ( 'message_commission', '佣金消息', 'text', 'message', '0', 1 , '', '可用变量 用户昵称:[username] 用户ID:[userid] 购买人:[buyer] 订单金额:[amount] 佣金类型:[type], 佣金:[commission]', '');

DROP TABLE IF EXISTS `sa_member_cart`;

CREATE TABLE `sa_member_cart` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `member_id` BIGINT DEFAULT '0',
  `product_id` BIGINT DEFAULT '0',
  `sku_id` BIGINT DEFAULT '0',
  `product_title` varchar(100) DEFAULT NULL,
  `product_image` varchar(150) DEFAULT NULL,
  `product_price` DECIMAL(10,2) DEFAULT NULL,
  `product_weight` INT DEFAULT 0,
  `count` INT DEFAULT NULL,
  `sort` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_specifications`;

CREATE TABLE `sa_specifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '规格名称',
  `data` text COMMENT '规格数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `sa_product_category`;

CREATE TABLE `sa_product_category` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `pid` BIGINT DEFAULT NULL COMMENT '父分类ID',
  `title` varchar(100) DEFAULT NULL COMMENT '分类名称',
  `short` varchar(20) DEFAULT NULL COMMENT '分类简称',
  `name` varchar(50) DEFAULT NULL COMMENT '分类别名',
  `icon` varchar(100) DEFAULT NULL COMMENT '图标',
  `image` varchar(100) DEFAULT NULL COMMENT '大图',
  `specs` varchar(200) DEFAULT NULL COMMENT '绑定规格',
  `props` TEXT COMMENT '绑定属性',
  `sort` INT DEFAULT NULL COMMENT '排序',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1为正常 0为关闭',
  `is_hot` TINYINT DEFAULT NULL COMMENT '热门',
  `is_lock` TINYINT DEFAULT NULL COMMENT '是否锁定',
  `keywords` varchar(255) DEFAULT NULL COMMENT '分类关键词',
  `description` varchar(255) DEFAULT NULL COMMENT '分类描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_brand`;

CREATE TABLE `sa_product_brand` (
  `id` INT unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` INT DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT NULL,
  `logo` varchar(150) DEFAULT '',
  `url` varchar(150) DEFAULT NULL,
  `sort` INT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_category_brand`;

CREATE TABLE `sa_product_category_brand` (
  `id` BIGINT  NOT NULL AUTO_INCREMENT,
  `cate_id` BIGINT DEFAULT 0,
  `brand_id` BIGINT DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_coupon`;

CREATE TABLE `sa_product_coupon` (
  `id` BIGINT  NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` BIGINT DEFAULT NULL COMMENT '主id',
  `title` varchar(100) NOT NULL DEFAULT '',
  `bind_type` TINYINT DEFAULT 0 COMMENT '0-通用 1-类目 2-品牌 3-指定商品 4-指定sku',
  `cate_id` BIGINT DEFAULT 0,
  `brand_id` BIGINT DEFAULT 0,
  `product_id` BIGINT DEFAULT 0,
  `sku_id` BIGINT DEFAULT 0,
  `type` TINYINT DEFAULT 0 COMMENT '0- 满减 1-折扣',
  `limit` INT DEFAULT 0,
  `amount` INT DEFAULT 0,
  `discount` INT DEFAULT 0,
  `start_time` BIGINT DEFAULT 0,
  `end_time` BIGINT DEFAULT 0,
  `status` tinyINT DEFAULT 1,
  `stock` INT DEFAULT 1000 COMMENT '-1 无限库存',
  `levels_limit` varchar(100) DEFAULT '',
  `count_limit` INT DEFAULT 0 COMMENT '0 不限制领取数量',
  `receive` INT DEFAULT 0,
  `expiry_type` tinyINT DEFAULT 0,
  `expiry_time` BIGINT DEFAULT 0,
  `expiry_day` INT DEFAULT 0,
  `cost_credit` INT DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_member_coupon`;

CREATE TABLE `sa_member_coupon` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `coupon_id` BIGINT DEFAULT 0,
  `member_id` BIGINT DEFAULT 0,
  `title` varchar(100) DEFAULT '',
  `bind_type` tinyINT DEFAULT 0 COMMENT '0-通用 1-类目 2-品牌 3-指定商品 4-指定sku',
  `cate_id` BIGINT DEFAULT 0,
  `brand_id` BIGINT DEFAULT 0,
  `product_id` BIGINT DEFAULT 0,
  `sku_id` BIGINT DEFAULT 0,
  `type` TINYINT DEFAULT 0 COMMENT '0- 满减 1-折扣',
  `limit` INT DEFAULT 0,
  `amount` INT DEFAULT 0,
  `discount` INT DEFAULT 0,
  `create_time` BIGINT DEFAULT 0,
  `expiry_time` BIGINT DEFAULT 0,
  `status` TINYINT DEFAULT 1,
  `use_time` BIGINT DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product`;

CREATE TABLE `sa_product` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT 'cn' COMMENT '语言',
  `main_id` BIGINT DEFAULT 0 COMMENT '主id',
  `admin_id` BIGINT DEFAULT 0 COMMENT '管理员id',
  `member_id` BIGINT DEFAULT 0 COMMENT '会员id',
  `store_id` BIGINT DEFAULT 0 COMMENT '店铺id',
  `cate_id` BIGINT DEFAULT 0 COMMENT '商品类目',
  `brand_id` BIGINT DEFAULT 0 COMMENT '商品品牌',
  `title` varchar(150) DEFAULT '' COMMENT '商品名称',
  `vice_title` varchar(200) DEFAULT '',
  `unit` varchar(10) DEFAULT '件',
  `goods_no` varchar(50) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `spec_data` text COMMENT '规格数据',
  `prop_data` text COMMENT '属性数据(产品参数,不影响价格)',
  `max_price` DECIMAL(10,2) DEFAULT 0 COMMENT '最高价格',
  `min_price` DECIMAL(10,2) DEFAULT 0 COMMENT '最低价格',
  `market_price` DECIMAL(10,2) DEFAULT 0 COMMENT '市场价格',
  `province` varchar(50) DEFAULT '' COMMENT '发布地',
  `city` varchar(50) DEFAULT '' COMMENT '发布地',
  `county` varchar(50) DEFAULT '' COMMENT '发布地',
  `content` text COMMENT '产品详情',
  `level_id` INT DEFAULT 0 COMMENT '升级的产品绑定的会员组',
  `levels` varchar(100) DEFAULT '' COMMENT '允许购买的会员组',
  `storage` INT DEFAULT '0' COMMENT '总库存',
  `postage_id` INT DEFAULT '0' COMMENT '邮费设置',
  `postage` DECIMAL(10,2) DEFAULT '0' COMMENT '固定邮费',
  `sale` BIGINT DEFAULT '0' COMMENT '总销量',
  `v_sale` BIGINT DEFAULT '0' COMMENT '虚拟销量',
  `comment` BIGINT DEFAULT '0' COMMENT '评论数量',
  `type` TINYINT DEFAULT '0' COMMENT '商品类型,参见后台编辑页',
  `is_commission` TINYINT(1) DEFAULT '1' COMMENT '是否启用分佣',
  `commission_percent`  text COMMENT '独立的分佣设置',
  `is_coupon` TINYINT(1) DEFAULT '1' COMMENT '是否可用优惠券',
  `is_discount` TINYINT(1) DEFAULT '1' COMMENT '是否启用折扣',
  `max_buy` INT DEFAULT '0' COMMENT '会员可购买数量 0为不限制',
  `max_buy_cycle` varchar(10) DEFAULT '' COMMENT '限制周期',
  `status` TINYINT NOT NULL DEFAULT '1',
  `delete_time` BIGINT DEFAULT '0',
  `create_time` BIGINT DEFAULT '0',
  `update_time` BIGINT DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_flash`;

CREATE TABLE `sa_product_flash`(
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT DEFAULT NULL,
  `timestamp` BIGINT DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `product` TEXT,
  `brand` TEXT,
  `skus` TEXT,
  `images` TEXT,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_sku`;

CREATE TABLE `sa_product_sku` (
  `sku_id` BIGINT NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT DEFAULT NULL,
  `specs` text,
  `image` varchar(150) DEFAULT NULL,
  `goods_no` varchar(50) DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT 0 COMMENT '购买价格',
  `ext_price` varchar(300) DEFAULT '' COMMENT '独立价格',
  `market_price` DECIMAL(10,2) DEFAULT 0 COMMENT '市场价格',
  `cost_price` DECIMAL(10,2) DEFAULT 0 COMMENT '成本价格',
  `weight` INT DEFAULT '0',
  `size` varchar(50) DEFAULT '0',
  `storage` INT DEFAULT '0',
  `sale` INT DEFAULT '0',
  PRIMARY KEY (`sku_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `sa_product_comment`;

CREATE TABLE `sa_product_comment` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `member_id` BIGINT NOT NULL DEFAULT '0',
  `product_id` BIGINT NOT NULL DEFAULT '0',
  `sku_id` BIGINT NOT NULL DEFAULT '0',
  `order_id` BIGINT NOT NULL DEFAULT '0',
  `status` TINYINT NOT NULL DEFAULT '0',
  `is_anonymous` TINYINT(1) NOT NULL DEFAULT '0',
  `stars` TINYINT NOT NULL DEFAULT '0',
  `content` text,
  `reply_time` BIGINT NOT NULL DEFAULT '0',
  `reply_user_id` BIGINT NOT NULL DEFAULT '0',
  `reply` text,
  `create_time` BIGINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_product_images`;

CREATE TABLE `sa_product_images` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `product_id` BIGINT DEFAULT NULL,
  `sku_id` BIGINT DEFAULT NULL,
  `sort` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `sa_order`;

CREATE TABLE `sa_order` (
  `order_id` BIGINT NOT NULL AUTO_INCREMENT,
  `platform` VARCHAR(30) NULL DEFAULT '',
  `appid` varchar(30) DEFAULT '',
  `order_no` VARCHAR(30) NOT NULL,
  `member_id` BIGINT NULL DEFAULT 0,
  `store_id` BIGINT NULL DEFAULT 0,
  `store_ids` VARCHAR(120) NULL DEFAULT '',
  `payamount` DECIMAL(10,2) NULL DEFAULT 0,
  `payedamount` DECIMAL(10,2) NULL DEFAULT 0,
  `product_amount` DECIMAL(10,2) NULL DEFAULT 0,
  `cost_amount` DECIMAL(10,2) NULL DEFAULT 0,
  `discount_amount` DECIMAL(10,2) NULL DEFAULT 0,
  `commission_amount` DECIMAL(10,2) NULL DEFAULT 0,
  `commission_special` TEXT NULL,
  `level_id` INT NULL DEFAULT 0,
  `create_time` BIGINT NULL DEFAULT 0,
  `debit_type` VARCHAR(20) NULL COMMENT '抵扣付款方式',
  `pay_type` VARCHAR(20) NULL COMMENT '付款方式',
  `pay_time` BIGINT NULL DEFAULT 0,
  `deliver_time` BIGINT NULL DEFAULT 0,
  `receive_time` BIGINT NULL DEFAULT 0,
  `confirm_time` BIGINT NULL DEFAULT 0,
  `comment_time` BIGINT NULL DEFAULT 0,
  `reason` VARCHAR(50) NULL COMMENT '取消/退款原因',
  `cancel_time` BIGINT NULL DEFAULT 0,
  `refund_time` BIGINT NULL DEFAULT 0,
  `rebated` BIGINT NULL DEFAULT 0,
  `rebate_time` BIGINT NULL DEFAULT 0,
  `rebate_total` DECIMAL(10,2) NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0 COMMENT '订单状态',
  `refund_status` TINYINT NULL DEFAULT 0,
  `noticed` TINYINT NULL DEFAULT 0 COMMENT '通知状态',
  `isaudit` TINYINT NULL DEFAULT 0 COMMENT '审核状态',
  `delete_time` BIGINT NULL DEFAULT 0 COMMENT '删除状态',
  `form_id` VARCHAR(45) NULL,
  `subscribe` VARCHAR(50) NULL,
  `remark` VARCHAR(250) NULL,
  `deliver_type` TINYINT NULL DEFAULT 0 COMMENT '发货方式',
  `address_id` INT NULL DEFAULT 0,
  `pickup_store_id` INT NULL DEFAULT 0,
  `receive_name` VARCHAR(45) NULL,
  `mobile` VARCHAR(45) NULL,
  `province` VARCHAR(45) NULL,
  `city` VARCHAR(45) NULL,
  `area` VARCHAR(45) NULL,
  `address` VARCHAR(150) NULL,
  `postage_area_id` INT DEFAULT '0',
  `postage` DECIMAL(10,2) DEFAULT '0',
  `invoice_id` INT DEFAULT '0',
  `express_no` VARCHAR(100) NULL,
  `express_code` VARCHAR(20) NULL,
  `type` TINYINT NULL DEFAULT 1,
  `islock` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`order_id`),
  UNIQUE INDEX `orderno_index` (`order_no` ASC),
  INDEX `memberid_index` (`member_id` ASC)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_order_store`;
CREATE TABLE `sa_order_store` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT DEFAULT '0',
  `store_id` BIGINT NULL DEFAULT 0,
  `member_id` BIGINT NULL,
  `amount` DECIMAL(10,2) NULL DEFAULT 0,
  `cost_amount` DECIMAL(10,2) NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0 COMMENT '结算状态',
  `refund_status` TINYINT NULL DEFAULT 0,
  `deliver_time` BIGINT NULL DEFAULT 0 COMMENT '发货时间',
  `settle_time` BIGINT NULL DEFAULT 0 COMMENT '发放时间',
  `confirm_time` BIGINT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_order_product`;
CREATE TABLE `sa_order_product` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT DEFAULT '0',
  `member_id` BIGINT NULL,
  `store_id` BIGINT NULL DEFAULT 0,
  `product_id` BIGINT DEFAULT '0',
  `sku_id` BIGINT DEFAULT '0',
  `sku_specs` text,
  `product_title` varchar(100) DEFAULT '',
  `product_image` varchar(150) DEFAULT '',
  `product_orig_price` DECIMAL(10,2) DEFAULT 0,
  `product_price` DECIMAL(10,2) DEFAULT 0,
  `product_cost_price` DECIMAL(10,2) DEFAULT 0,
  `product_weight` INT DEFAULT 0,
  `count` INT DEFAULT 0,
  `sort` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_order_refund`;
CREATE TABLE `sa_order_refund` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT DEFAULT '0',
  `member_id` BIGINT DEFAULT '0',
  `type` TINYINT DEFAULT '0',
  `reason` varchar(30) DEFAULT '',
  `remark` varchar(200) DEFAULT '',
  `amount` decimal(10,2) DEFAULT '0',
  `image` text,
  `product` text,
  `address` text,
  `express` text,
  `reply` varchar(200) DEFAULT '',
  `status` TINYINT NULL DEFAULT 0,
  `create_time` BIGINT DEFAULT '0',
  `update_time` BIGINT DEFAULT '0',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_order_comment`;

CREATE TABLE `sa_order_comment` (
  `id` BIGINT unsigned NOT NULL AUTO_INCREMENT,
  `member_id` BIGINT NOT NULL DEFAULT '0',
  `order_id` BIGINT NOT NULL DEFAULT '0',
  `client` varchar(50) NOT NULL DEFAULT '',
  `device` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` TINYINT NOT NULL DEFAULT '0',
  `is_anonymous` TINYINT(1) NOT NULL DEFAULT '0',
  `service_stars` TINYINT NOT NULL DEFAULT '0',
  `express_stars` TINYINT NOT NULL DEFAULT '0',
  `delivery_stars` TINYINT NOT NULL DEFAULT '0',
  `content` text,
  `reply_time` BIGINT NOT NULL DEFAULT '0',
  `reply_user_id` BIGINT NOT NULL DEFAULT '0',
  `reply` text,
  `create_time` BIGINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_order_log`;
CREATE TABLE `sa_order_log` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT DEFAULT '0',
  `member_id` BIGINT NULL,
  `type` varchar(20) DEFAULT '',
  `remark` varchar(255) DEFAULT '',
  `create_time` BIGINT DEFAULT '0',
  `update_time` BIGINT DEFAULT '0',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_help_category`;
CREATE TABLE `sa_help_category` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `pid` BIGINT DEFAULT NULL COMMENT '父分类ID',
  `title` varchar(100) DEFAULT NULL COMMENT '分类名称',
  `short` varchar(20) DEFAULT NULL COMMENT '分类简称',
  `name` varchar(50) DEFAULT NULL COMMENT '分类别名',
  `icon` varchar(100) DEFAULT NULL COMMENT '图标',
  `image` varchar(100) DEFAULT NULL COMMENT '大图',
  `sort` INT DEFAULT NULL COMMENT '排序',
  `status` tinyint DEFAULT 1 COMMENT '状态 1为正常 0为关闭',
  `keywords` varchar(255) DEFAULT NULL COMMENT '分类关键词',
  `description` varchar(255) DEFAULT NULL COMMENT '分类描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_help`;
CREATE TABLE `sa_help` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `cate_id` BIGINT DEFAULT NULL,
  `user_id` BIGINT DEFAULT '0',
  `title` varchar(150) DEFAULT NULL,
  `vice_title` varchar(200) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `digg` BIGINT DEFAULT '0',
  `v_digg` BIGINT DEFAULT '0',
  `views` BIGINT DEFAULT '0',
  `v_views` BIGINT DEFAULT '0',
  `prop_data` text,
  `content` text,
  `create_time` BIGINT DEFAULT '0',
  `update_time` BIGINT DEFAULT '0',
  `sort` INT DEFAULT '0',
  `type` TINYINT DEFAULT '1',
  `status` TINYINT NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_express_code`;

CREATE TABLE `sa_express_code` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '',
  `express` varchar(50) DEFAULT '',
  `status` tinyint(1) DEFAULT '1',
  `use_times` INT DEFAULT '0',
  `create_time` BIGINT NULL DEFAULT 0,
  `update_time` BIGINT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_express_cache`;

CREATE TABLE `sa_express_cache` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `express_no` VARCHAR(100) NULL,
  `express_code` VARCHAR(20) NULL,
  `create_time` BIGINT NULL DEFAULT 0,
  `update_time` BIGINT NULL DEFAULT 0,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `express_no` (`express_no`),
  KEY `express_code` (`express_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_postage`;
CREATE TABLE `sa_postage` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT 0 COMMENT '店铺id',
  `title` VARCHAR(50) NULL,
  `is_default` TINYINT NULL DEFAULT 0,
  `status` tinyint DEFAULT '1' COMMENT '状态',
  `calc_type` TINYINT NULL DEFAULT 0 COMMENT '0-按重  1-按件 2-按体积',
  `area_type` TINYINT NULL DEFAULT 0,
  `specials` TEXT,
  `regions` VARCHAR(100) NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sa_postage_area`;
CREATE TABLE `sa_postage_area` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `postage_id` INT NOT NULL,
  `sort` INT NULL DEFAULT 0,
  `weight_lower` INT NULL DEFAULT 0,
  `weight_upper` INT NULL DEFAULT 0,
  `length_upper` INT NULL DEFAULT 0,
  `first` INT NULL DEFAULT 0 COMMENT '0-按重  1-按件 2-按体积',
  `first_fee` DECIMAL(10,2) NULL DEFAULT 0,
  `extend` INT NULL DEFAULT 0,
  `extend_fee` DECIMAL(10,2) NULL DEFAULT 0,
  `ceiling` DECIMAL(10,2) NULL DEFAULT 0,
  `free_limit` DECIMAL(10,2) NULL DEFAULT 0,
  `expresses` VARCHAR(200) NULL DEFAULT '',
  `areas` TEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

