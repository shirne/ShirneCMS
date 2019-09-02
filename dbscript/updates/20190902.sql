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