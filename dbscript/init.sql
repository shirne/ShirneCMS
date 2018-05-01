TRUNCATE TABLE `sa_permission`;
LOCK TABLES `sa_permission` WRITE;
/*!40000 ALTER TABLE `sa_permission` DISABLE KEYS */;

INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `order_id`, `disable`)
VALUES
  (1,0,'主面板','Index/index','Board','ion-speedometer',0,0),
  (2,0,'内容','','Content','ion-grid',0,0),
  (3,0,'其它','','Other','ion-cube',0,0),
  (4,0,'会员','','Member','ion-person',0,0),
  (5,0,'系统','','System','ion-gear-a',0,0),
  (6,2,'分类管理','Category/index','category_index','ion-asterisk',0,0),
  (7,2,'文章管理','Post/index','post_index','ion-document-text',0,0),
  (8,2,'单页管理','Page/index','page_index','ion-document',0,0),
  (9,3,'公告管理','Notice/index','notice_index','ion-speakerphone',0,0),
  (10,3,'广告管理','Adv/index','adv_index','ion-aperture',0,0),
  (11,3,'链接管理','Links/index','links_index','ion-link',0,0),
  (12,3,'留言管理','Feedback/index','feedback_index','ion-chatbox-working',0,0),
  (13,4,'会员管理','Member/index','member_index','ion-person',0,0),
  (14,4,'邀请码','Invite/index','invite_index','ion-pricetags',0,0),
  (15,4,'会员等级','MemberLevel/index','member_level_index','ion-person-stalker',0,0),
  (16,4,'充值记录','Paylog/recharge','paylog_recharge','ion-log-in',0,0),
  (17,4,'提现记录','Paylog/cashin','paylog_cashin','ion-log-out',0,0),
  (18,4,'操作日志','Member/log','member_log','ion-clipboard',0,0),
  (19,5,'配置管理','Setting/index','setting_index','ion-gear-b',0,0),
  (20,5,'管理员','Manager/index','manager_index','ion-person',0,0),
  (21,5,'菜单管理','Permission/index','permission_index','ion-code-working',0,0),
  (22,5,'操作日志','Manager/log','manager_log','ion-clipboard',0,0),
  (23,5,'付款方式','Paytype/index','paytype_index','ion-card',0,0);

/*!40000 ALTER TABLE `sa_permission` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `sa_manager` WRITE;
/*!40000 ALTER TABLE `sa_manager` DISABLE KEYS */;

INSERT INTO `sa_manager` (`id`, `username`,`realname`, `email`, `password`, `salt`, `avatar`, `create_time`, `update_time`, `login_ip`, `status`, `type`)
VALUES
  (1,'admin','','515343908@qq.com','60271966bbad6ead5faa991772a9277f', 'z5La7s0P',NULL,'1436679338','1436935104','0.0.0.0',1,1);

/*!40000 ALTER TABLE `sa_manager` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `sa_setting` WRITE;
/*!40000 ALTER TABLE `sa_setting` DISABLE KEYS */;

INSERT INTO `sa_setting` (`id`, `key`,`title`,`type`,`group`,`sort`, `value`, `description`,`data`)
VALUES
  (1,'site-name','站点名','text','common',0,'SimpleCMS','站点名',''),
  (2,'site-keywords','关键词','text','common',0,'关键词1,关键词2','关键词',''),
  (3,'site-description','站点描述','text','common',0,'站点描述信息','站点描述',''),
  (4,'site-tongji','统计代码','text','common',0,'&lt;script&gt; console.log(&quot;统计代码&quot;)&lt;/script&gt;','统计代码',''),
  (5,'site-icp','ICP备案号','text','common',0,'123456','ICP备案号',''),
  (6,'site-url','站点地址','text','common',0,'http://www.shirne.cn','站点地址',''),
  (10, 'appid', 'APPID', 'text', 'wechat', '0', 'wx8deb07601b20866f', '', ''),
  (11, 'appsecret', 'APPSecret', 'text', 'wechat', '0', '95673c3602bfdbf4612ee96b5ca024fd', '', ''),
  (12, 'token', 'Token', 'text', 'wechat', '0', 'hfwechatToken', '', ''),
  (13, 'encodingaeskey', 'AES密钥', 'text', 'wechat', '0', '', '', ''),
  (14, 'debug', '调试模式', 'radio', 'wechat', '0', '1', '', '1:开启\r\n2:关闭'),
  (21, 'mail_host', '邮箱主机', 'text', 'advance', '0', '', '', ''),
  (22, 'mail_port', '邮箱端口', 'text', 'advance', '0', '', '', ''),
  (23, 'mail_user', '邮箱账户', 'text', 'advance', '0', '', '', ''),
  (24, 'mail_pass', '邮箱密码', 'text', 'advance', '0', '', '', ''),
  (31, 'm_invite', '邀请注册', 'radio', 'member', '0', '1', '', '0:关闭\r\n1:启用\r\n2:强制'),
  (32, 'm_register', '强制注册', 'radio', 'member', '0', '1', '', '0:关闭\r\n1:启用'),
  (26, 'cash_fee', '提现手续费', 'text', 'member', '0', '10', '', ''),
  (27, 'cash_limit', '最低提现金额', 'text', 'member', '0', '10', '', ''),
  (28, 'cash_max', '最高提现金额', 'text', 'member', '0', '100000', '', '');

/*!40000 ALTER TABLE `sa_setting` ENABLE KEYS */;
UNLOCK TABLES;