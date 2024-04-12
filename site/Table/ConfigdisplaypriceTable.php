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

class ConfigdisplaypriceTable extends ShopbaseTable{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_config_display_prices', 'id', $_db);
    }
    
    function setZones($zones){
        $this->zones = serialize($zones);
    }
    
    function getZones(){
        if ($this->zones!=""){
            return unserialize($this->zones);
        }else{
            return array();
        }
    }
    
    function getList(){
        $db = \JFactory::getDBO();        
        $query = "SELECT * FROM `#__jshopping_config_display_prices`";
        $db->setQuery($query);
        $list = $db->loadObJectList();
        foreach($list as $k=>$v){
            $list[$k]->countries = unserialize($v->zones);
        }
        return $list;
    }
}