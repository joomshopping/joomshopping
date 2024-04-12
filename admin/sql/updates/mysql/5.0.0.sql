

CREATE TABLE IF NOT EXISTS `#__jshopping_products_to_extra_fields` (
`product_id` int(11) NOT NULL,
PRIMARY KEY (`product_id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__jshopping_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;