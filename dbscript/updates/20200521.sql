ALTER TABLE `sa_order_refund` 
CHANGE COLUMN `type` `type` TINYINT(4) NULL DEFAULT 0 ,
CHANGE COLUMN `reason` `reason` VARCHAR(30) NULL DEFAULT '' ,
ADD COLUMN `amount` DECIMAL(10,2) NULL DEFAULT 0 AFTER `remark`;


ALTER TABLE `sa_product` ADD `comment` int(11) DEFAULT '0' COMMENT '评论数量' AFTER `v_sale`;