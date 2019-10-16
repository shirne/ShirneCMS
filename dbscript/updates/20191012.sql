
ALTER TABLE `sa_order_product` 
ADD `product_cost_price` DECIMAL(10,2) DEFAULT 0 AFTER `product_price`;

ALTER TABLE `sa_order` 
ADD `cost_amount` DECIMAL(10,2) DEFAULT 0 AFTER `product_amount`;

ALTER TABLE `sa_order` 
ADD UNIQUE INDEX `order_no_unique` (`order_no` ASC);

update sa_order_product set  product_cost_price = (select cost_price from sa_product_sku where sku_id=sa_order_product.sku_id) where product_cost_price=0 and id>0;

update sa_order set  cost_amount = (select sum(product_cost_price) from sa_order_product where order_id=sa_order.order_id) where cost_amount=0 and order_id>0;