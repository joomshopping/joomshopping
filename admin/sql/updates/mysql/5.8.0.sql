ALTER TABLE `#__jshopping_attr` ADD `required` tinyint(1) NOT NULL default 1;
ALTER TABLE `#__jshopping_attr` ADD `publish` tinyint(1) NOT NULL default 1;
ALTER TABLE `#__jshopping_attr_values` ADD `publish` tinyint(1) NOT NULL default 1;
ALTER TABLE `#__jshopping_free_attr` ADD `publish` tinyint(1) NOT NULL default 1;
ALTER TABLE `#__jshopping_products_extra_fields` ADD `publish` tinyint(1) NOT NULL default 1;
ALTER TABLE `#__jshopping_products_extra_field_values` ADD `publish` tinyint(1) NOT NULL default 1;