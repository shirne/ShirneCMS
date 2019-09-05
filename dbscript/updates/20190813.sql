
CREATE TABLE IF NOT EXISTS `sa_pay_order_refund` (
 `id` INT NOT NULL AUTO_INCREMENT,
 `member_id` int(10) unsigned NOT NULL,
 `order_id` int(10) unsigned NOT NULL,
 `refund_no` VARCHAR(20) NOT NULL,
 `refund_fee` DECIMAL(10,2) NULL DEFAULT 0,
 `status` TINYINT NULL DEFAULT 0 COMMENT '状态 0-未退款 1- 退款中 2- 已退款',
 `reason` VARCHAR(50) NOT NULL,
 `refund_result` VARCHAR(50) NOT NULL,
 `refund_time` INT NULL DEFAULT 0,
 `create_time` INT NULL DEFAULT 0,
 `update_time` INT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
 UNIQUE KEY `refund_no`(`refund_no`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '订单退款记录';

ALTER TABLE `sa_pay_order`
ADD `pay_data` TEXT AFTER `order_type`,
ADD `trade_type` VARCHAR(20) NULL DEFAULT '' COMMENT '交易类型' AFTER `pay_type`,
ADD `is_refund` TINYINT NULL DEFAULT 0 COMMENT '是否退款' AFTER `time_end`,
ADD `refund_fee` DECIMAL(10,2) NULL DEFAULT 0 AFTER `is_refund`;

ALTER TABLE `sa_member`
ADD `nickname` varchar(50) NOT NULL DEFAULT '' AFTER `username`;

ALTER TABLE `sa_member_cart`
ADD `product_weight` INT(11) DEFAULT 0 AFTER `product_price`;

ALTER TABLE `sa_product`
ADD `market_price` DECIMAL(10,2) DEFAULT 0 COMMENT '市场价格' AFTER `min_price`;