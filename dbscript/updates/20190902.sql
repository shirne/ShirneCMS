ALTER TABLE `sa_product_coupon`
ADD  `levels_limit` varchar(100) DEFAULT '' AFTER `stock`,
ADD  `count_limit` int(11) DEFAULT 0 COMMENT '0 不限制领取数量' AFTER `levels_limit`;