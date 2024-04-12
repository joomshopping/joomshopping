<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die();

class ProductAttribut2Table extends ShopbaseTable{
    
    var $_price_mod_allow = array("+","-","*","/","=","%");
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_attr2', 'id', $_db);
    }
    
    function check(){        
        if (!in_array($this->price_mod, $this->_price_mod_allow)){
             $this->price_mod = $this->_price_mod_allow[0];
        }
        if (!$this->product_id){
            return 0;
        }
        if (!$this->attr_id){
            return 0;
        }
        if (!$this->attr_value_id){
            return 0;
        }
        return 1;
    }
    
    function deleteAttributeForProduct(){
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_products_attr2` WHERE `product_id` = '".$db->escape($this->product_id)."'";
        $db->setQuery($query);
        $db->execute();
    }
    
}