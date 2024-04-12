ALTER TABLE `#__jshopping_products_extra_fields` ADD `product_uniq_val` TINYINT NOT NULL default 0;
ALTER TABLE `#__jshopping_products` ADD `real_ean` varchar(16) NOT NULL default '';
ALTER TABLE `#__jshopping_order_item` ADD `real_ean` varchar(16) NOT NULL default '';
ALTER TABLE `#__jshopping_products_attr` ADD `real_ean` varchar(16) NOT NULL default '';
ALTER TABLE `#__jshopping_cart_temp` ADD `user_id` INT NOT NULL default 0 AFTER `id_cookie`; 