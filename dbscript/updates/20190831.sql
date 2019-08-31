CREATE TABLE `sa_manager_role` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type` TINYINT NOT NULL DEFAULT 0,
  `label_type` VARCHAR(20) NOT NULL DEFAULT '',
  `role_name` VARCHAR(50) NOT NULL DEFAULT '',
  `global` VARCHAR(200) NULL DEFAULT '',
  `detail` TEXT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `type_UNIQUE` (`type` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sa_manager_role` (`id`,`type`,`label_type` ,`role_name`,`global`, `detail`, `create_time`, `update_time`)
VALUES
  (1,1,'danger','系统管理员','','','1436679338','1436935104'),
  (2,5,'warning','网站管理员','','','1436679338','1436935104'),
  (3,9,'info','网站编辑','','','1436679338','1436935104');

UPDATE `sa_manager` SET `type`=5 WHERE `type`=2 and id>0;