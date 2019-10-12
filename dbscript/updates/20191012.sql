
ALTER TABLE `sa_order_product` 
ADD `product_cost_price` DECIMAL(10,2) DEFAULT NULL AFTER `product_price`;

ALTER TABLE `sa_order` 
ADD `cost_amount` DECIMAL(10,2) DEFAULT NULL AFTER `product_amount`;

ALTER TABLE `sa_order` 
ADD UNIQUE INDEX `order_no_unique` (`order_no` ASC);
