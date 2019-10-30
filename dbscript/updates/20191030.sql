
INSERT INTO `sa_setting` ( `key`,`title`,`type`,`group`,`sort`,`is_sys`, `value`, `description`,`data`)
VALUES
  ( 'accesskey_id', '阿里云ID', 'text', 'third', '0',1, '', '', ''),
  ( 'accesskey_secret', '阿里云Key', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_oss', 'OSS Buket', 'text', 'third', '0',1, '', '', ''),
  ( 'aliyun_oss_ssl', '是否ssl', 'radio', 'third', '0',1, '1', '', '0:否\r\n1:是'),
  ( 'aliyun_oss_domain', 'OSS 域名', 'text', 'third', '0',1, '', '', ''),
  ( 'anonymous_comment', '匿名评论', 'radio', 'member', '0',1, '0', '', '0:关闭\r\n1:启用');