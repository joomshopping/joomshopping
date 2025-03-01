ALTER TABLE `#__jshopping_addons` ADD `publish` TINYINT(1) NOT NULL default -1;

CREATE TABLE IF NOT EXISTS `#__jshopping_addons_dependencies` (
`id` int(11) NOT NULL auto_increment,
`alias` VARCHAR(255) NOT NULL default '',
`name` VARCHAR(255) NOT NULL default '',
`parent` VARCHAR(255) NOT NULL default '',
`version` VARCHAR(255) NOT NULL default '',
`installed` TINYINT(1) NOT NULL default 0,
`autoinstall` TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;