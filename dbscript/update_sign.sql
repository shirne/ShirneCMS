INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'sign_open', '开启签到', 'radio', 'sign', '0', 1 , '0', '', '0:关闭\r\n1:开启'),
  ( 'sup_sign_open', '开启补签', 'radio', 'sign', '0', 1 , '0', '', '0:关闭\r\n1:开启'),
  ( 'sup_sign_rule', '补签规则', 'json', 'sign', '0', 1 , '', '', ''),
  ( 'sign_cycle', '签到周期', 'radio', 'sign', '0', 1 , 'month', '', 'long:不限\r\nmonth:按月'),
  ( 'sign_award', '签到奖励', 'json', 'sign', '0', 1 , '', '', ''),
  ( 'sign_keep_award', '连续签到奖励', 'array', 'sign', '0', 1 , '', '', ''),
  ( 'sign_description', '签到说明', 'text', 'sign', '0', 1 , '', '', '');

DROP TABLE IF EXISTS `sa_sign_log`;
CREATE TABLE `sa_sign_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `is_sup` tinyint(11) NOT NULL DEFAULT '0',
  `ranking_day` int(11) NOT NULL DEFAULT '0',
  `keep_days` int(11) NOT NULL DEFAULT '0',
  `signdate` varchar(10) DEFAULT '0',
  `signtime` int(11) DEFAULT '0',
  `mood` int(11) NOT NULL DEFAULT '0',
  `remark` varchar(50) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `key_member_id`(`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;