
ALTER TABLE `sa_article` ADD `close_comment` tinyint(4) NOT NULL DEFAULT '0' AFTER `v_digg`;

ALTER TABLE sa_member_address CHANGE COLUMN `recive_name` `receive_name` VARCHAR(50) NOT NULL DEFAULT '' ;
ALTER TABLE sa_order CHANGE COLUMN `recive_name` `receive_name` VARCHAR(50) NOT NULL DEFAULT '' ;


ALTER TABLE sa_member_address add `street` VARCHAR(50) NOT NULL DEFAULT '' after `area`;


INSERT INTO `sa_permission` (`id`, `parent_id`,`name`, `url`,`key`, `icon`, `sort_id`, `disable`)
VALUES (77,7,'版权署名','Copyrights/index','copyrights_index','ion-logo-closed-captioning',9,0);

DROP TABLE IF EXISTS `sa_copyrights`;
CREATE TABLE `sa_copyrights` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) DEFAULT NULL COMMENT '语言',
  `main_id` int(11) DEFAULT NULL COMMENT '主id',
  `title` varchar(100) DEFAULT '',
  `name` varchar(100) DEFAULT '',
  `content` TEXT,
  `sort` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter TABLE `sa_article` add `copyright_id` INT(11) DEFAULT 0 after user_id;