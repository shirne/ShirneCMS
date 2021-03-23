alter TABLE `sa_adv_group` add `width` int(11) DEFAULT 0 after `flag`,
add  `height` int(11) DEFAULT 0 after `width`,
add `update_time` int(11) DEFAULT 0 after `create_time`;

alter TABLE `sa_page`
  add `image` varchar(150) NOT NULL DEFAULT '' after `icon`;


alter TABLE `sa_order` add `refund_status` TINYINT NULL DEFAULT 0 after `status`,
add `payedamount` DECIMAL(10,2) NULL DEFAULT 0 after `payamount`;


alter TABLE `sa_order_refund`
  add `reply` varchar(200) DEFAULT '' after `express`;

alter table `sa_product_comment` add `stars` tinyint(4) NOT NULL DEFAULT '0' after `is_anonymous`;

CREATE TABLE `sa_order_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `client` varchar(50) NOT NULL DEFAULT '',
  `device` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `is_anonymous` tinyint(4) NOT NULL DEFAULT '0',
  `service_stars` tinyint(4) NOT NULL DEFAULT '0',
  `express_stars` tinyint(4) NOT NULL DEFAULT '0',
  `delivery_stars` tinyint(4) NOT NULL DEFAULT '0',
  `content` text,
  `reply_time` int(11) NOT NULL DEFAULT '0',
  `reply_user_id` int(11) NOT NULL DEFAULT '0',
  `reply` text,
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;