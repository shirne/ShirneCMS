update `sa_permission` set icon=replace(icon,'ion-','ion-md-') where id>0;

update `sa_permission` set icon='ion-md-apps' where 'key'='Content';
update `sa_permission` set icon='ion-md-cog' where 'key'='System';
update `sa_permission` set icon='ion-md-medical' where 'key'='category_index';
update `sa_permission` set icon='ion-md-paper' where 'key'='article_index';
update `sa_permission` set icon='ion-md-megaphone' where 'key'='notice_index';
update `sa_permission` set icon='ion-md-chatbubbles' where 'key'='feedback_index';
update `sa_permission` set icon='ion-md-people' where 'key'='member_level_index';
update `sa_permission` set icon='ion-md-options' where 'key'='setting_index';

ALTER TABLE `sa_article` ADD `digg` INT(11) DEFAULT '0',ADD `comment` INT(11) DEFAULT '0',ADD `views` INT(11) DEFAULT '0';

CREATE TABLE `sa_article_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sa_article_digg` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `device` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;