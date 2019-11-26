ALTER TABLE `sa_member_level_log` 
ADD `pay_time` int(11) DEFAULT 0 AFTER `create_time`,
ADD `cancel_time` int(11) DEFAULT 0 AFTER `pay_time`;

ALTER TABLE `sa_pay_order` ADD `appid` VARCHAR(30) NULL DEFAULT '' after `pay_type`;

alter table sa_credit_order add `reason` VARCHAR(100) NULL after address;