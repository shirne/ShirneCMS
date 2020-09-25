ALTER TABLE `sa_adv_group` ADD COLUMN `type` tinyint(4) DEFAULT '0' AFTER `flag`;

ALTER TABLE `sa_adv_item` ADD COLUMN `video` VARCHAR(150) NULL AFTER `image`,
ADD COLUMN `content` TEXT NULL AFTER `video`,
ADD COLUMN `elements` TEXT AFTER `url`;
