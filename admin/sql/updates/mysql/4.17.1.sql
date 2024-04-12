ALTER TABLE `#__jshopping_orders`
ADD `coupon_free_discount` decimal(14,4) NOT NULL
AFTER `coupon_id`;