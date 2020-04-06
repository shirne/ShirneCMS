ALTER TABLE `sa_award_log` ADD `field` varchar(20) DEFAULT AFTER `type`;

CREATE TABLE `sa_keywords` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) DEFAULT '',
  `description` varchar(200) DEFAULT '',
  `group` varchar(20) DEFAULT '',
  `v_hot` int(11) DEFAULT 0,
  `hot` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sa_product` 
 ADD `unit` varchar(10) DEFAULT NULL AFTER `vice_title`;