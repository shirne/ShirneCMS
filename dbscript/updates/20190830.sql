ALTER TABLE `sa_member_token` ADD `appid` VARCHAR(30) NULL AFTER `platform`;

CREATE TABLE `sa_oauth_app` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `platform` VARCHAR(20) NULL,
  `appid` VARCHAR(30) NULL,
  `appsecret` VARCHAR(50) NULL,
  `create_time` INT NULL DEFAULT 0,
  `update_time` INT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `appid` (`appid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;