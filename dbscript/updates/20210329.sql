ALTER TABLE `sa_permission`
add `is_sys` INT DEFAULT '0' after `sort_id`;

UPDATE `sa_permission` set `is_sys`=1 where `id` in (1,2,7,8,9);