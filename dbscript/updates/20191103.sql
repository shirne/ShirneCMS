ALTER TABLE `sa_order` ADD `pay_type` VARCHAR(20) NULL COMMENT '付款方式' AFTER `create_time`;

ALTER TABLE `sa_credit_order` ADD `pay_type` VARCHAR(20) NULL COMMENT '付款方式' AFTER `create_time`;