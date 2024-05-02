<?php
/**
* @version      5.2.1 11.08.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Factory;
defined('_JEXEC') or die();

class ProductOptionTable extends ShopbaseTable{

    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_option', 'id', $_db);
    }
    
    function getProductOption($product_id, $key){
        $db = Factory::getDBO();
        $query = "SELECT `value` FROM `#__jshopping_products_option` WHERE product_id = '".$db->escape($product_id)."' AND `key`='".$db->escape($key)."' ";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function getProductOptions($product_id){
        $db = Factory::getDBO();
        $query = "SELECT `key`, `value` FROM `#__jshopping_products_option` WHERE product_id='".$db->escape($product_id)."'";
        $db->setQuery($query);
        $list = $db->loadObJectList();
        $rows = array();
        foreach($list as $k=>$v){
            $rows[$v->key] = $v->value;
        }
    return $rows;
    }
    
    function getProductOptionList($array_product_id, $key, $setforallproducts = 1){
        $db = Factory::getDBO();
        if (!count($array_product_id) || !is_array($array_product_id)){
            return array();
        }
		$ids = implode(',', $db->q($array_product_id));
        $query = "SELECT `product_id`, `value` FROM `#__jshopping_products_option` WHERE product_id IN (".$ids.") AND `key`=".$db->q($key);
        $db->setQuery($query);
        $list = $db->loadObJectList('product_id');         
        $rows = array();
        foreach($array_product_id as $pid){
            if (isset($list[$pid])){
                $rows[$pid] = $list[$pid]->value;
            }else{
                if ($setforallproducts){
                    $rows[$pid] = '';
                }
            }
        }
        return $rows;
    }    
   
} 