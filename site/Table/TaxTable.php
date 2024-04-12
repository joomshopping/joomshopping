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

class TaxTable extends ShopbaseTable{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_taxes', 'tax_id', $_db);
    }
    
    function getAllTaxes(){
        $db = \JFactory::getDBO();                
        $query = "SELECT tax_id, tax_name, tax_value FROM `#__jshopping_taxes`";
        $db->setQuery($query);
        return $db->loadObJectList();
    }
    
    function getExtTaxes($tax_id = 0){
        $db = \JFactory::getDBO();
        $where = "";
        if ($tax_id) $where = " where tax_id=".(int)$tax_id;
        $query = "SELECT * FROM `#__jshopping_taxes_ext` ".$where;
        $db->setQuery($query);
        $list = $db->loadObJectList();
        foreach($list as $k=>$v){
            $list[$k]->countries = (array)unserialize($v->zones);
        }
        return $list;
    }

}