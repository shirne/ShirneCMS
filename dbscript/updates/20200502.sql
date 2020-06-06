CREATE TABLE `sa_member_agent_log` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` INT NULL DEFAULT 0,
  `agent_id` INT NULL DEFAULT 0,
  `type` VARCHAR(10) NULL,
  `remark` VARCHAR(100) NULL DEFAULT '',
  `create_time` INT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sa_manager_role` ADD `label_type` VARCHAR(20) NOT NULL DEFAULT '' AFTER `type`;

CREATE TABLE `sa_member_authen` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT 0,
  `level_id` int(4) NOT NULL DEFAULT 0,
  `realname` VARCHAR(20) NOT NULL,
  `mobile` VARCHAR(20) NOT NULL,
  `province` VARCHAR(20) NOT NULL,
  `city` VARCHAR(20) NOT NULL,
  `id_no` VARCHAR(50) NOT NULL,
  `image` VARCHAR(150) NOT NULL,
  `validate_time` int(11) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `authen_time` int(11) NOT NULL DEFAULT 0,
  `status` TINYINT NULL DEFAULT 0,
  `reason` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sa_wechat` ADD COLUMN `share_poster_url` VARCHAR(150) DEFAULT '' AFTER `shareimg`;
