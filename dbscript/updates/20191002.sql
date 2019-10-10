ALTER TABLE `sa_article` 
ADD  `v_digg` INT(11) DEFAULT '0' AFTER  `digg`,
ADD `v_views` INT(11) DEFAULT '0' AFTER  `views`;

ALTER TABLE `sa_product` 
ADD  `v_sale` int(11) DEFAULT '0' AFTER  `sale`;