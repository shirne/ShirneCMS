TRUNCATE TABLE `sa_permission`;

INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES
  (1,0,'主面板','Index/index','Board','ion-md-speedometer',0,0),
  (2,0,'内容','','Content','ion-md-apps',1,0),
  (7,0,'其它','','Other','ion-md-cube',7,0),
  (8,0,'会员','','Member','ion-md-person',8,0),
  (9,0,'系统','','System','ion-md-cog',9,0),
  (11,2,'分类管理','article.category/index','category_index','ion-md-medical',0,0),
  (12,2,'文章管理','article.index/index','article_index','ion-md-paper',3,0),
  (13,2,'单页管理','Page/index','page_index','ion-md-document',5,0),
  (14,2,'评论管理','article.index/comments','article_comments','ion-md-chatboxes',3,0),
  (70,7,'公告管理','other.notice/index','notice_index','ion-md-megaphone',0,0),
  (71,7,'广告管理','other.adv/index','adv_index','ion-md-aperture',3,0),
  (72,7,'链接管理','other.links/index','links_index','ion-md-link',5,0),
  (73,7,'留言管理','other.feedback/index','feedback_index','ion-md-chatbubbles',7,0),
  (74,7,'订阅管理','other.subscribe/index','subscribe_index','ion-md-mail',9,0),
  (75,7,'展位管理','other.booth/index','booth_index','ion-md-easel',9,0),
  (76,7,'关键字管理','other.keywords/index','keywords_index','ion-md-search',9,0),
  (77,7,'版权署名','other.copyrights/index','copyrights_index','ion-logo-closed-captioning',9,0),
  (80,8,'会员管理','member.index/index','member_index','ion-md-person',0,0),
  (81,8,'升级申请','member.authen/index','member_authen_index','ion-md-ribbon',2,0),
  (82,8,'邀请码','member.invite/index','invite_index','ion-md-pricetags',3,0),
  (83,8,'会员组','member.level/index','member_level_index','ion-md-people',5,0),
  (84,8,'佣金明细','member.index/award_log','member_award_log','ion-md-paper',7,0),
  (85,8,'余额明细','member.index/money_log','member_money_log','ion-md-paper',9,0),
  (86,8,'支付明细','member.paylog/index','paylog_index','ion-md-wallet',11,0),
  (87,8,'充值记录','member.paylog/recharge','paylog_recharge','ion-md-log-in',11,0),
  (88,8,'提现记录','member.paylog/cashin','paylog_cashin','ion-md-log-out',13,0),
  (89,8,'操作日志','member.index/log','member_log','ion-md-clipboard',15,0),
  (91,9,'配置管理','Setting/index','setting_index','ion-md-options',0,0),
  (92,9,'管理员','manager.index/index','manager_index','ion-md-person',3,0),
  (93,9,'菜单管理','Permission/index','permission_index','ion-md-code-working',5,0),
  (94,9,'导航管理','Navigator/index','navigator_index','ion-md-reorder',7,0),
  (95,9,'操作日志','manager.index/log','manager_log','ion-md-clipboard',9,0),
  (96,9,'付款方式','Paytype/index','paytype_index','ion-md-card',11,0);

TRUNCATE TABLE `sa_manager_role`;

INSERT INTO `sa_manager_role` (`id`,`type`, `role_name`,`global`, `detail`, `create_time`, `update_time`)
VALUES
  (1,1,'系统管理员','','','1436679338','1436935104'),
  (2,5,'网站管理员','','','1436679338','1436935104'),
  (3,9,'网站编辑','','','1436679338','1436935104');

TRUNCATE TABLE `sa_manager`;

INSERT INTO `sa_manager` (`id`,`pid`, `username`,`realname`, `email`, `password`, `salt`, `avatar`, `create_time`, `update_time`, `login_ip`, `status`, `type`)
VALUES
  (1,0,'administrator','','79099818@qq.com','60271966bbad6ead5faa991772a9277f', 'z5La7s0P',NULL,'1436679338','1436935104','0.0.0.0',1,1);


TRUNCATE TABLE `sa_setting`;

INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'site-webname','站点名','text','common',0,1,'ShirneCMS','站点名',''),
  ( 'site-keywords','关键词','text','common',0,1,'关键词1,关键词2','关键词',''),
  ( 'site-description','站点描述','text','common',0,1,'站点描述信息','站点描述',''),
  ( 'site-weblogo','站点logo','image','common',0,1,'','站点logo',''),
  ( 'site-close','关闭站点','radio','common',0,1,'0','是否关闭站点','0:开启\r\n1:关闭'),
  ( 'site-close-desc','关闭说明','text','common',0,1,'系统维护中','关闭站点的说明',''),
  ( 'site-shareimg','默认分享图','image','common',0,1,'','默认分享图',''),
  ( 'site-tongji','统计代码','textarea','common',0,1,'','统计代码',''),
  ( 'site-icp','ICP备案号','text','common',0,1,'','ICP备案号',''),
  ( 'gongan-icp','公安备案号','text','common',0,1,'','公安备案号',''),
  ( 'site-url','站点网址','text','common',0,1,'http://www.shirne.cn','站点地址',''),
  ( 'site-name','公司名','text','common',0,1,'ShirneCMS','公司名',''),
  ( 'site-400','400电话','text','common',0,1,'','400电话',''),
  ( 'site-email','公司邮箱','text','common',0,1,'','公司邮箱',''),
  ( 'site-telephone','公司电话','text','common',0,1,'','公司电话',''),
  ( 'site-address','公司地址','text','common',0,1,'','公司地址',''),
  ( 'site-location','公司位置','location','common',0,1,'','location',''),
  ( 'wechat_autologin', '微信自动登录', 'radio', 'third', '0',1, '0', '必须在配置了服务号的情况下能有效', '1:开启\r\n2:关闭'),
  ( 'mail_host', '邮箱主机', 'text', 'third', '0',1, '', '', ''),
  ( 'mail_port', '邮箱端口', 'text', 'third', '0',1, '', '', ''),
  ( 'mail_user', '邮箱账户', 'text', 'third', '0',1, '', '', ''),
  ( 'mail_pass', '邮箱密码', 'text', 'third', '0',1, '', '', ''),
  ( 'sms_code', '短信验证', 'radio', 'third', '0',1, '0', '', '1:开启\r\n2:关闭'),
  ( 'sms_spcode', '企业编号', 'text', 'third', '0',1, '', '', ''),
  ( 'sms_loginname', '登录名称', 'text', 'third', '0',1, '', '', ''),
  ( 'sms_password', '登录密码', 'text', 'third', '0',1, '', '', ''),
  ( 'kd_userid', '快递鸟用户ID', 'text', 'third', '0',1, '', '', ''),
  ( 'kd_apikey', '快递鸟API Key', 'text', 'third', '0',1, '', '', ''),
  ( 'mapkey_baidu', '百度地图密钥', 'text', 'third', '0',1, '', '', ''),
  ( 'mapkey_google', '谷哥地图密钥y', 'text', 'third', '0',1, '', '', ''),
  ( 'mapkey_tencent', '腾讯地图密钥', 'text', 'third', '0',1, '', '', ''),
  ( 'mapkey_gaode', '高德地图密钥', 'text', 'third', '0',1, '', '', ''),
  ( 'captcha_mode', '验证码模式', 'radio', 'third', '0',1, '0', '', '0:图形验证\r\n1:极验验证'),
  ( 'captcha_geeid', '极验ID', 'text', 'third', '0',1, '', '', ''),
  ( 'captcha_geekey', '极验密钥', 'text', 'third', '0',1, '', '', ''),
  ( 'accesskey_id', '阿里云ID', 'text', 'third', '0',1, '', '', ''),
  ( 'accesskey_secret', '阿里云Key', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_oss', 'OSS Buket', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_oss_ssl', '是否ssl', 'radio', 'third', '0',1, '1', '', '0:否\r\n1:是'),
  ( 'aliyun_oss_domain', 'OSS 域名', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_dysms_sign', '短信签名', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_dysms_register', '注册短信CODE', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_dysms_login', '登录短信CODE', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_dysms_forget', '找回密码短信CODE', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_dysms_verify', '通用短信CODE', 'text', 'third', '0',1, '', '', ''),
  ( 'kd_apikey', '快递鸟API Key', 'text', 'third', '0',1, '', '', ''),
  ( 'm_open', '会员系统', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用'),
  ( 'm_register_open', '开启注册', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用'),
  ( 'm_register', '强制注册', 'radio', 'member', '0',1, '0', '', '0:关闭\r\n1:启用'),
  ( 'm_invite', '邀请注册', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用\r\n2:强制'),
  ( 'm_checkcode', '验证码', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用'),
  ( 'anonymous_comment', '匿名评论', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用'),
  ( 'autoaudit', '订单自动审核', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用'),
  ( 'agent_lock', '锁客模式', 'radio', 'member', '0',1, '1', '', '0:关闭\r\n1:启用'),
  ( 'commission_type', '佣金本金计算', 'radio', 'member', '0',1, '0', '', '0:购买价-成本价\r\n1:销售价-成本价\r\n2:购买价\r\n3:销售价'),
  ( 'agent_start', '分销员佣金', 'radio', 'member', '0',1, '0', '', '0:从上级起返\r\n1:从自己起返'),
  ( 'commission_delay', '佣金到账时机', 'radio', 'member', '0',1, '0', '', '0:订单完成\r\n1:订单支付\r\n2:订单完成后'),
  ( 'commission_delay_days', '佣金到账延迟', 'text', 'member', '0',1, '0', '', ''),
  ( 'cash_types', '提现方式', 'array', 'member', '0',1, '', '', 'unioncard:银行卡\r\nwechat:微信企业付款\r\nwechatpack:微信红包\r\nwechatminipack:小程序红包\r\nalipay:支付宝转账'),
  ( 'cash_fee', '提现手续费', 'text', 'member', '0',1, '10', '', ''),
  ( 'cash_fee_min', '最低手续费', 'text', 'member', '0',1, '1', '', ''),
  ( 'cash_fee_max', '封顶手续费', 'text', 'member', '0',1, '50', '', ''),
  ( 'cash_limit', '最低提现金额', 'text', 'member', '0',1, '10', '', ''),
  ( 'cash_max', '最高提现金额', 'text', 'member', '0',1, '100000', '', ''),
  ( 'cash_power', '提现倍数', 'text', 'member', '0',1, '100', '', ''),
  ( 'share_product', '推广产品', 'text', 'member','0', '0', '', '','');


TRUNCATE TABLE `sa_member_level`;

INSERT INTO `sa_member_level`(`level_id`,`level_name`,`short_name`,`is_default`,`level_price`,`sort`,`commission_layer`,`commission_percent`) VALUES (1,'普通会员','普',1,0.00,0,3,'[\"0\",\"0\",\"0\"]');

TRUNCATE TABLE `sa_oauth_app`;

INSERT INTO `sa_oauth_app`(`platform`, `appid`, `appsecret`) VALUES ('web', 'web', '111111');

