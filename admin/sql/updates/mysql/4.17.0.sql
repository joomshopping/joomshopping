ALTER TABLE `#__jshopping_users` CHANGE `phone` `phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_users` CHANGE `mobil_phone` `mobil_phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_users` CHANGE `fax` `fax` VARCHAR(24) NOT NULL;

ALTER TABLE `#__jshopping_users` CHANGE `d_phone` `d_phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_users` CHANGE `d_mobil_phone` `d_mobil_phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_users` CHANGE `d_fax` `d_fax` VARCHAR(24) NOT NULL;

ALTER TABLE `#__jshopping_orders` CHANGE `phone` `phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_orders` CHANGE `mobil_phone` `mobil_phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_orders` CHANGE `fax` `fax` VARCHAR(24) NOT NULL;

ALTER TABLE `#__jshopping_orders` CHANGE `d_phone` `d_phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_orders` CHANGE `d_mobil_phone` `d_mobil_phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_orders` CHANGE `d_fax` `d_fax` VARCHAR(24) NOT NULL;

ALTER TABLE `#__jshopping_vendors` CHANGE `phone` `phone` VARCHAR(24) NOT NULL;
ALTER TABLE `#__jshopping_vendors` CHANGE `fax` `fax` VARCHAR(24) NOT NULL;
