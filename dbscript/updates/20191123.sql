ALTER TABLE `sa_member_level_log` 
ADD `pay_time` int(11) DEFAULT 0 AFTER `create_time`,
ADD `cancel_time` int(11) DEFAULT 0 AFTER `pay_time`;

ALTER TABLE `sa_pay_order` ADD `appid` VARCHAR(30) NULL DEFAULT '' after `pay_type`;

alter table sa_credit_order add `reason` VARCHAR(100) NULL after address;

ALTER TABLE `sa_credit_order`
ADD `deliver_time` INT NULL DEFAULT 0 AFTER `pay_time`,
ADD `comment_time` INT NULL DEFAULT 0 AFTER `deliver_time`,
ADD `refund_time` INT NULL DEFAULT 0 AFTER `comment_time`;

 ALTER TABLE `sa_goods`
 ADD `unit` varchar(10) DEFAULT NULL;

 ALTER TABLE `sa_member`
ADD `secpassword` varchar(32) NOT NULL default '' after `salt`,
 ADD `secsalt` varchar(8) NOT NULL default '' after `secpassword`;