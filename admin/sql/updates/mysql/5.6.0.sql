ALTER TABLE `#__jshopping_addons` ADD `publish` TINYINT(1) NOT NULL default -1;
ALTER TABLE `#__jshopping_addons` ADD `config` text NOT NULL;
ALTER TABLE `#__jshopping_categories` ADD `product_sorting` VARCHAR(4) NOT NULL AFTER `access`, ADD `product_sorting_direction` TINYINT NOT NULL DEFAULT '-1' AFTER `product_sorting`;

ALTER TABLE `#__jshopping_taxes` ADD `ordering` INT NOT NULL;
update `#__jshopping_taxes` set `ordering`=`tax_id`;

CREATE TABLE IF NOT EXISTS `#__jshopping_addons_dependencies` (
`id` int(11) NOT NULL auto_increment,
`alias` VARCHAR(255) NOT NULL default '',
`name` VARCHAR(255) NOT NULL default '',
`parent` VARCHAR(255) NOT NULL default '',
`version` VARCHAR(255) NOT NULL default '',
`installed` TINYINT(1) NOT NULL default 0,
`error` TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;