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

class Request{
	
	public static function getQuantity($key = 'quantity', $fix = 0){
		$jshopConfig = \JSFactory::getConfig();
		$app = \JFactory::getApplication();
		if ($jshopConfig->use_decimal_qty){
            $quantity = floatval(str_replace(",", ".", $app->input->getVar($key, 1)));
            $quantity = round($quantity, $jshopConfig->cart_decimal_qty_precision);
        }else{
            $quantity = (int)$app->input->getInt($key, 1);
        }
		if ($fix && $quantity < 0){
			$quantity = 0;
		}
		return $quantity;
	}
	
	public static function getAttribute($key = 'jshop_attr_id'){
		$attribut = \JFactory::getApplication()->input->getVar($key);
        if (!is_array($attribut)) $attribut = array();
        foreach($attribut as $k=>$v){
			$attribut[intval($k)] = intval($v);
		}
		return $attribut;
	}
	
	public static function getFreeAttribute($key = 'freeattribut'){
		$attribut = \JFactory::getApplication()->input->getVar($key);
        if (!is_array($attribut)) $attribut = array();
		return $attribut;
	}
    
    public static function getCartTo(){
        $to = \JFactory::getApplication()->input->getCmd('to', "cart");
        return $to;
    }

}