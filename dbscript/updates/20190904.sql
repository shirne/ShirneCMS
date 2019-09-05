ALTER TABLE `sa_order`
  ADD `appid` varchar(30) NULL DEFAULT 0 AFTER `platform`;

ALTER TABLE `sa_member_cashin`
  ADD `platform` VARCHAR(30) NULL AFTER `member_id`,
  ADD `appid` varchar(30) NULL DEFAULT '' AFTER `platform`,
  ADD `update_time` int(11) DEFAULT 0 AFTER `create_time`,
  ADD `payment_time` int(11) DEFAULT 0 AFTER `audit_time`,
  ADD `fail_time` int(11) DEFAULT 0 AFTER `payment_time`,
  ADD `cash_fee` int(11) NULL DEFAULT '0' AFTER `amount`,
  ADD `reason` varchar(100) NULL DEFAULT '' AFTER `remark`;

ALTER TABLE `sa_member_recharge`
  ADD `platform` VARCHAR(30) NULL AFTER `member_id`,
  ADD `form_id` varchar(50) DEFAULT '' AFTER `member_id`;

ALTER TABLE `sa_member`
  ADD `froze_credit` int(11) DEFAULT '0' AFTER `froze_money`,
  ADD `froze_reward` int(11) DEFAULT '0' AFTER `froze_credit`;

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'cash_types', '提现方式', 'array', 'member', '0',1, '', '', 'unioncard:银行卡\r\nwechat:微信企业付款\r\nwechatpack:微信红包\r\nwechatminipack:小程序红包\r\nalipay:支付宝转账'),
  ( 'cash_fee_min', '最低手续费', 'array', 'member', '0',1, '1', '', ''),
  ( 'cash_fee_max', '封顶手续费', 'array', 'member', '0',1, '50', '', '');