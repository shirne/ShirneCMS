
ALTER TABLE `sa_adv_group` ADD `locked` tinyint(11) DEFAULT '0' AFTER `create_time`;

INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES
  (75,7,'展位管理','Booth/index','booth_index','ion-md-easel',9,0);

CREATE TABLE `sa_booth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT '',
  `flag` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(30) NOT NULL DEFAULT '',
  `data` TEXT,
  `locked` tinyint(11) DEFAULT '0',
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  `status` tinyint(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `flag_UNIQUE` (`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
