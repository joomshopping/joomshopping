<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;

defined('_JEXEC') or die();

class AliasModel{
    
    function checkExistAlias1Group($alias, $lang, $category_id, $manufacture_id){
        $db = \JFactory::getDBO();
        $query = "select category_id as id from #__jshopping_categories where `alias_".$lang."` = '".$db->escape($alias)."' and category_id!='".$db->escape($category_id)."' 
                  union
                  select manufacturer_id as id from #__jshopping_manufacturers where `alias_".$lang."` = '".$db->escape($alias)."' and manufacturer_id!='".$db->escape($manufacture_id)."'
                  ";
        $db->setQuery($query);
        $res = $db->loadResult();
        $reservedFirstAlias = \JSFactory::getReservedFirstAlias();
        if ($res || in_array($alias, $reservedFirstAlias)){
            return 0;//error
        }else{
            return 1;//ok
        }
    }
    
    function checkExistAlias2Group($alias, $lang, $product_id){
        $db = \JFactory::getDBO();
        $query = "select product_id from #__jshopping_products where `alias_".$lang."` = '".$db->escape($alias)."' and product_id!='".$db->escape($product_id)."'";
        $db->setQuery($query);
        $res = $db->loadResult();        
        if ($res){
            return 0;//error
        }else{
            return 1;//ok
        }
    }
    
}