ALTER TABLE `#__jshopping_products_images` ADD `title` varchar(255) NOT NULL;
ALTER TABLE `#__jshopping_products` ADD `main_category_id` INT NOT NULL default 0 AFTER `vendor_id`;
ALTER TABLE `#__jshopping_categories` ADD `img_alt` varchar(255) NOT NULL default '';
ALTER TABLE `#__jshopping_categories` ADD `img_title` varchar(255) NOT NULL default '';
ALTER TABLE `#__jshopping_manufacturers` ADD  `img_alt` varchar(255) NOT NULL default '';
ALTER TABLE `#__jshopping_manufacturers` ADD `img_title` varchar(255) NOT NULL default '';