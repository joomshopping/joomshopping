<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
defined('_JEXEC') or die();

class Cart{
		
	public static function checkAdd(){
		$jshopConfig = JSFactory::getConfig();
        $to = Request::getCartTo();
		return ((!$jshopConfig->user_as_catalog && Helper::getDisplayPriceShop()) || $to=='wishlist');
	}
	
	public static function checkView(){
		return !JSFactory::getConfig()->user_as_catalog;
	}

	public static function hasAllAttributeRequired($required_attr_id, $attr_active_vals) {
        $attr_id_not_0 = array_keys(array_filter($attr_active_vals, function($value) {
            return $value != 0;
        }));
        return empty(array_diff($required_attr_id, $attr_id_not_0));
	}

}