ALTER TABLE `sa_member_cashin`
  ADD `form_id` varchar(50) DEFAULT '' AFTER `member_id`;

ALTER  TABLE `sa_wechat_template_message`
ADD `content` TEXT AFTER `keywords`;