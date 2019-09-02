ALTER TABLE `sa_product_coupon`
ADD  `levels_limit` varchar(100) DEFAULT '' AFTER `stock`,
ADD  `count_limit` int(11) DEFAULT 0 COMMENT '0 不限制领取数量' AFTER `levels_limit`;

ALTER TABLE `sa_member_level`
    ADD `upgrade_type` tinyint NULL DEFAULT 0 COMMENT '升级方式' AFTER `is_default`,
    ADD `diy_price` tinyint NULL DEFAULT 0 COMMENT '自定义价格' AFTER `upgrade_type`;

ALTER TABLE `sa_product_sku`
    ADD `ext_price` varchar(300) DEFAULT '' COMMENT '独立价格' AFTER `price`;

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
    ( 'm_open', '会员系统', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用'),
  ( 'm_register_open', '开启注册', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用'),
  ( 'commission_type', '佣金本金计算', 'radio', 'member', '0',1, '0', '', '0:销售价-成本价\r\n1:销售价'),
  ( 'commission_delay', '佣金到账时机', 'radio', 'member', '0',1, '0', '', '0:订单完成\r\n1:订单支付\r\n2:订单完成后'),
  ( 'commission_delay_days', '佣金到账延迟', 'text', 'member', '0',1, '0', '', '');

INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES
  (37,3,'运费模板','ProductPostage/index','product_postage_index','ion-md-train',0,0);

ALTER TABLE `sa_product`
  ADD `postage_id` int(11) DEFAULT '0' AFTER `storage`,
  ADD `postage` DECIMAL(10,2) DEFAULT '0' AFTER `postage_id`;

CREATE TABLE `sa_postage` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(50) NULL,
  `is_default` TINYINT NULL DEFAULT 0,
  `calc_type` TINYINT NULL DEFAULT 0 COMMENT '0-按重  1-按件 2-按体积',
  `area_type` TINYINT NULL DEFAULT 0,
  `specials` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sa_postage_area` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `postage_id` INT NOT NULL,
  `sort` INT NULL DEFAULT 0,
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