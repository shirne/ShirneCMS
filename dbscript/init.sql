TRUNCATE TABLE `sa_permission`;

INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES
  (1,0,'主面板','Index/index','Board','ion-md-speedometer',0,0),
  (2,0,'内容','','Content','ion-md-apps',1,0),
  (7,0,'其它','','Other','ion-md-cube',7,0),
  (8,0,'会员','','Member','ion-md-person',8,0),
  (9,0,'系统','','System','ion-md-cog',9,0),
  (11,2,'分类管理','Category/index','category_index','ion-md-medical',0,0),
  (12,2,'文章管理','Article/index','article_index','ion-md-paper',0,0),
  (13,2,'单页管理','Page/index','page_index','ion-md-document',0,0),
  (70,7,'公告管理','Notice/index','notice_index','ion-md-megaphone',0,0),
  (71,7,'广告管理','Adv/index','adv_index','ion-md-aperture',0,0),
  (72,7,'链接管理','Links/index','links_index','ion-md-link',0,0),
  (73,7,'留言管理','Feedback/index','feedback_index','ion-md-chatbubbles',0,0),
  (80,8,'会员管理','Member/index','member_index','ion-md-person',0,0),
  (81,8,'邀请码','Invite/index','invite_index','ion-md-pricetags',0,0),
  (82,8,'会员组','MemberLevel/index','member_level_index','ion-md-people',0,0),
  (83,8,'余额明细','Member/money_log','member_money_log','ion-md-paper',0,0),
  (84,8,'充值记录','Paylog/recharge','paylog_recharge','ion-md-log-in',0,0),
  (85,8,'提现记录','Paylog/cashin','paylog_cashin','ion-md-log-out',0,0),
  (86,8,'操作日志','Member/log','member_log','ion-md-clipboard',0,0),
  (91,9,'配置管理','Setting/index','setting_index','ion-md-options',0,0),
  (92,9,'管理员','Manager/index','manager_index','ion-md-person',0,0),
  (93,9,'菜单管理','Permission/index','permission_index','ion-md-code-working',0,0),
  (94,9,'导航管理','Navigator/index','navigator_index','ion-md-reorder',0,0),
  (95,9,'操作日志','Manager/log','manager_log','ion-md-clipboard',0,0),
  (96,9,'付款方式','Paytype/index','paytype_index','ion-md-card',0,0),
  (97,9,'公众号管理','Wechat/index','wechat_index','ion-md-chatboxes',0,0);


TRUNCATE TABLE `sa_manager`;

INSERT INTO `sa_manager` (`id`, `username`,`realname`, `email`, `password`, `salt`, `avatar`, `create_time`, `update_time`, `login_ip`, `status`, `type`)
VALUES
  (1,'admin','','79099818@qq.com','60271966bbad6ead5faa991772a9277f', 'z5La7s0P',NULL,'1436679338','1436935104','0.0.0.0',1,1);


TRUNCATE TABLE `sa_setting`;

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`, `value`, `description`,`data`)
VALUES
  ('site-webname','站点名','text','common',0,'ShirneCMS','站点名',''),
  ('site-keywords','关键词','text','common',0,'关键词1,关键词2','关键词',''),
  ('site-description','站点描述','text','common',0,'站点描述信息','站点描述',''),
  ('site-weblogo','站点logo','image','common',0,'','站点logo',''),
  ('site-tongji','统计代码','textarea','common',0,'','统计代码',''),
  ('site-icp','ICP备案号','text','common',0,'123456','ICP备案号',''),
  ('site-url','站点网址','text','common',0,'http://www.shirne.cn','站点地址',''),
  ('site-name','公司名','text','common',0,'ShirneCMS','公司名',''),
  ('site-address','公司地址','text','common',0,'','公司地址',''),
  ('site-location','公司位置','location','common',0,'','location',''),
  ( 'wechat_autologin', '微信自动登录', 'radio', 'third', '0', '1', '必须在配置了服务号的情况下能有效', '1:开启\r\n2:关闭'),
  ( 'mail_host', '邮箱主机', 'text', 'advance', '0', '', '', ''),
  ( 'mail_port', '邮箱端口', 'text', 'advance', '0', '', '', ''),
  ( 'mail_user', '邮箱账户', 'text', 'advance', '0', '', '', ''),
  ( 'mail_pass', '邮箱密码', 'text', 'advance', '0', '', '', ''),
  ( 'sms_code', '短信验证', 'radio', 'third', '0', '1', '', '1:开启\r\n2:关闭'),
  ( 'sms_spcode', '企业编号', 'text', 'third', '0', '', '', ''),
  ( 'sms_loginname', '登录名称', 'text', 'third', '0', '', '', ''),
  ( 'sms_password', '登录密码', 'text', 'third', '0', '', '', ''),
  ( 'kd_userid', '快递鸟用户ID', 'text', 'third', '0', '', '', ''),
  ( 'kd_apikey', '快递鸟API Key', 'text', 'third', '0', '', '', ''),
  ( 'mapkey_baidu', '百度地图密钥', 'text', 'third', '0', 'rO9tOdEWFfvyGgDkiWqFjxK6', '', ''),
  ( 'mapkey_google', '谷哥地图密钥y', 'text', 'third', '0', 'AIzaSyB8lorvl6EtqIWz67bjWBruOhm9NYS1e24', '', ''),
  ( 'mapkey_tencent', '腾讯地图密钥', 'text', 'third', '0', '7I5BZ-QUE6R-JXLWV-WTVAA-CJMYF-7PBBI', '', ''),
  ( 'mapkey_gaode', '高德地图密钥', 'text', 'third', '0', '3ec311b5db0d597e79422eeb9a6d4449', '', ''),
  ( 'captcha_mode', '验证码模式', 'radio', 'third', '0', '0', '', '0:图形验证\r\n1:极验验证'),
  ( 'captcha_geeid', '极验ID', 'text', 'third', '0', '', '', ''),
  ( 'captcha_geekey', '极验密钥', 'text', 'third', '0', '', '', ''),
  ( 'kd_apikey', '快递鸟API Key', 'text', 'third', '0', '', '', ''),
  ( 'm_invite', '邀请注册', 'radio', 'member', '0', '1', '', '0:关闭\r\n1:启用\r\n2:强制'),
  ( 'm_register', '强制注册', 'radio', 'member', '0', '1', '', '0:关闭\r\n1:启用'),
  ( 'm_checkcode', '验证码', 'radio', 'member', '0', '1', '', '0:关闭\r\n1:启用'),
  ( 'autoaudit', '订单自动审核', 'radio', 'member', '0', '1', '', '0:关闭\r\n1:启用'),
  ( 'cash_fee', '提现手续费', 'text', 'member', '0', '10', '', ''),
  ( 'cash_limit', '最低提现金额', 'text', 'member', '0', '10', '', ''),
  ( 'cash_max', '最高提现金额', 'text', 'member', '0', '100000', '', ''),
  ( 'cash_power', '提现倍数', 'text', 'member', '0', '100', '', '');


TRUNCATE TABLE `sa_member_level`;

INSERT INTO `sa_member_level`(`level_id`,`level_name`,`short_name`,`is_default`,`level_price`,`sort`,`commission_layer`,`commission_percent`) VALUES (1,'普通会员','普',1,0.00,0,3,'[\"0\",\"0\",\"0\"]');


TRUNCATE TABLE `sa_category`;

INSERT INTO `sa_category`(`id`,`pid`,`title`,`short`,`name`,`icon`,`image`,`sort`,`keywords`,`description`)VALUES
(1,0,'新闻动态','新闻','news','','',0,'',''),
(2,0,'案例中心','案例','cases','','',0,'',''),
(3,1,'行业新闻','行业','industry','','',0,'',''),
(4,1,'公司新闻','公司','company','','',0,'',''),
(5,1,'常见问题','FAQ','faq','','',0,'',''),
(6,2,'网站建设','网站','web','','',0,'',''),
(7,2,'微信平台','微信','wechat','','',0,'',''),
(8,2,'企业APP','APP','app','','',0,'',''),
(9,2,'画册/LOGO','AI','design','','',0,'','');
