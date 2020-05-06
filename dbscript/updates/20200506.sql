ALTER TABLE `sa_invite_code` 
CHANGE COLUMN `invalid_at` `invalid_time` INT(11) NULL DEFAULT '0' ,
CHANGE COLUMN `use_at` `use_time` INT(11) NULL DEFAULT '0' ;
