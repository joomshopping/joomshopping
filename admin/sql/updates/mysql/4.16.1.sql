ALTER TABLE `#__jshopping_orders` CHANGE `ip_address` `ip_address` VARCHAR(64) NOT NULL;
ALTER TABLE `#__jshopping_products` CHANGE `product_url` `product_url` VARCHAR(128) NOT NULL;
ALTER TABLE `#__jshopping_products` ADD `manufacturer_code` VARCHAR(32) NOT NULL AFTER `product_ean`;
ALTER TABLE `#__jshopping_order_item` ADD `manufacturer_code` VARCHAR(32) NOT NULL AFTER `product_ean`;
ALTER TABLE `#__jshopping_products_attr` ADD `manufacturer_code` VARCHAR(32) NOT NULL AFTER `ean`;