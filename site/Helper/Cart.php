<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
defined('_JEXEC') or die();

class Cart{
		
	public static function checkAdd(){
		$jshopConfig = \JSFactory::getConfig();
        $to = Request::getCartTo();
		return ((!$jshopConfig->user_as_catalog && \JSHelper::getDisplayPriceShop()) || $to=='wishlist');
	}
	
	public static function checkView(){
		return !\JSFactory::getConfig()->user_as_catalog;
	}
	
}