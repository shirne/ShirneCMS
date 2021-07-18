
ALTER TABLE `sa_article` ADD `close_comment` tinyint(4) NOT NULL DEFAULT '0' AFTER `v_digg`;

ALTER TABLE sa_member_address CHANGE COLUMN `recive_name` `receive_name` VARCHAR(50) NOT NULL DEFAULT '' ;
ALTER TABLE sa_order CHANGE COLUMN `recive_name` `receive_name` VARCHAR(50) NOT NULL DEFAULT '' ;


ALTER TABLE sa_member_address add `street` VARCHAR(50) NOT NULL DEFAULT '' after `area`;