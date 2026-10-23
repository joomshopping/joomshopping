ALTER TABLE `#__jshopping_delivery_times` ADD `min_days` decimal(8,2) NOT NULL default 0 AFTER `days`;
ALTER TABLE `#__jshopping_addons_dependencies` ADD `required` TINYINT(1) NOT NULL default 1 AFTER `version`;
ALTER TABLE `#__jshopping_addons_dependencies` ADD `display_count` INT(11) NOT NULL default 0 AFTER `error`;
ALTER TABLE `#__jshopping_addons_dependencies` ADD `scriptinstall` VARCHAR(255) NOT NULL default '' AFTER `display_count`;
