<?php
/**
* @version      5.9.2 25.04.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Factory;
defined('_JEXEC') or die();

class ProductAttribut2Table extends ShopbaseTable{
    
    public $_price_mod_allow = array("+","-","*","/","=","%");
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_attr2', 'id', $_db);
    }
    
    function check(){
        if ((!$this->id || $this->price_mod !== null) && !in_array($this->price_mod, $this->_price_mod_allow)){
            $this->price_mod = $this->_price_mod_allow[0];
        }
        if (!$this->id || $this->product_id !== null) {
            if (!$this->product_id) {
                return 0;
            }
        }
        if (!$this->id || $this->attr_id !== null) {
            if (!$this->attr_id) {
                return 0;
            }
        }
        if (!$this->id || $this->attr_value_id !== null) {
            if (!$this->attr_value_id) {
                return 0;
            }
        }
        return 1;
    }
    
    function deleteAttributeForProduct(){
        $db = Factory::getDBO();
        $query = "DELETE FROM `#__jshopping_products_attr2` WHERE `product_id`=".intval($this->product_id);
        $db->setQuery($query);
        $db->execute();
    }
    
}