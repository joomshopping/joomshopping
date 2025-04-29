ALTER TABLE `#__jshopping_categories` MODIFY `category_add_date` datetime DEFAULT NULL;
ALTER TABLE `#__jshopping_categories` MODIFY `product_sorting` VARCHAR(4) NOT NULL DEFAULT '';

ALTER TABLE `#__jshopping_products` MODIFY `product_date_added` datetime DEFAULT NULL;
ALTER TABLE `#__jshopping_products` MODIFY `date_modify` datetime DEFAULT NULL;

ALTER TABLE `#__jshopping_products_videos` MODIFY `video_code` text DEFAULT NULL;

ALTER TABLE `#__jshopping_products_reviews` MODIFY `time` datetime DEFAULT NULL;
ALTER TABLE `#__jshopping_products_reviews` MODIFY `review` text DEFAULT NULL;

ALTER TABLE `#__jshopping_configs` MODIFY `value` text DEFAULT NULL;

ALTER TABLE `#__jshopping_coupons` MODIFY `coupon_start_date` date DEFAULT NULL;
ALTER TABLE `#__jshopping_coupons` MODIFY `coupon_expire_date` date DEFAULT NULL;

ALTER TABLE `#__jshopping_users` MODIFY `birthday` DATE DEFAULT NULL;
ALTER TABLE `#__jshopping_users` MODIFY `delivery_adress` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__jshopping_users` MODIFY `d_birthday` DATE DEFAULT NULL;

ALTER TABLE `#__jshopping_payment_method` MODIFY `access` int(3) NOT NULL DEFAULT 1;
ALTER TABLE `#__jshopping_payment_method` MODIFY `payment_params` text DEFAULT NULL;
ALTER TABLE `#__jshopping_payment_method` MODIFY `order_description` text DEFAULT NULL;

ALTER TABLE `#__jshopping_usergroups` MODIFY `usergroup_description` text DEFAULT NULL;

ALTER TABLE `#__jshopping_shipping_method` MODIFY `params` LONGTEXT DEFAULT NULL;

ALTER TABLE `#__jshopping_shipping_ext_calc` MODIFY `description` text DEFAULT NULL;
ALTER TABLE `#__jshopping_shipping_ext_calc` MODIFY `params` LONGTEXT DEFAULT NULL;
ALTER TABLE `#__jshopping_shipping_ext_calc` MODIFY `shipping_method` text DEFAULT NULL;

ALTER TABLE `#__jshopping_shipping_method_price` MODIFY `params` LONGTEXT DEFAULT NULL;

ALTER TABLE `#__jshopping_order_history` MODIFY `status_date_added` datetime DEFAULT NULL;
ALTER TABLE `#__jshopping_order_history` MODIFY `comments` text DEFAULT NULL;
ALTER TABLE `#__jshopping_order_history` MODIFY `include_comment` TINYINT NOT NULL DEFAULT 0;

ALTER TABLE `#__jshopping_order_item` MODIFY `product_attributes` text DEFAULT NULL;
ALTER TABLE `#__jshopping_order_item` MODIFY `product_freeattributes` text DEFAULT NULL;
ALTER TABLE `#__jshopping_order_item` MODIFY `attributes` text DEFAULT NULL;
ALTER TABLE `#__jshopping_order_item` MODIFY `freeattributes` text DEFAULT NULL;
ALTER TABLE `#__jshopping_order_item` MODIFY `extra_fields` text DEFAULT NULL;
ALTER TABLE `#__jshopping_order_item` MODIFY `files` text DEFAULT NULL;
ALTER TABLE `#__jshopping_order_item` MODIFY `params` text DEFAULT NULL;

ALTER TABLE `#__jshopping_orders` MODIFY `order_tax_ext` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `shipping_tax_ext` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `payment_tax_ext` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `package_tax_ext` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `order_date` DATETIME DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `invoice_date` datetime DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `order_m_date` DATETIME DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `payment_params` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `payment_params_data` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `shipping_params` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `shipping_params_data` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `delivery_date` datetime DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `order_add_info` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `birthday` DATE DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `d_birthday` DATE DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `file_stat_downloads` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `order_custom_info` text DEFAULT NULL;
ALTER TABLE `#__jshopping_orders` MODIFY `transaction` text DEFAULT NULL;

ALTER TABLE `#__jshopping_attr` MODIFY `cats` text DEFAULT NULL;

ALTER TABLE `#__jshopping_cart_temp` MODIFY `cart` text DEFAULT NULL;
ALTER TABLE `#__jshopping_cart_temp` MODIFY `type_cart` varchar(32) NOT NULL DEFAULT '';

ALTER TABLE `#__jshopping_import_export` MODIFY `description` text DEFAULT NULL;
ALTER TABLE `#__jshopping_import_export` MODIFY `params` text DEFAULT NULL;

ALTER TABLE `#__jshopping_taxes_ext` MODIFY `zones` text DEFAULT NULL;

ALTER TABLE `#__jshopping_config_display_prices` MODIFY `zones` text DEFAULT NULL;

ALTER TABLE `#__jshopping_products_extra_fields` MODIFY `cats` text DEFAULT NULL;

ALTER TABLE `#__jshopping_vendors` MODIFY `additional_information` text DEFAULT NULL;

ALTER TABLE `#__jshopping_addons` MODIFY `key` text DEFAULT NULL;
ALTER TABLE `#__jshopping_addons` MODIFY `params` longtext DEFAULT NULL;
ALTER TABLE `#__jshopping_addons` MODIFY `config` text DEFAULT NULL;

ALTER TABLE `#__jshopping_products_option` MODIFY `value` text DEFAULT NULL;

ALTER TABLE `#__jshopping_payment_trx` MODIFY `date` datetime DEFAULT NULL;

ALTER TABLE `#__jshopping_payment_trx_data` MODIFY `value` text DEFAULT NULL;