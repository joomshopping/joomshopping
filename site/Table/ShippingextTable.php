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

Jimport('Joomla.filesystem.folder');
include_once(JPATH_ROOT."/components/com_jshopping/shippings/shippingext.php");

class ShippingExtTable extends ShopbaseTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_shipping_ext_calc', 'id', $_db);
    }
    
    function loadFromAlias($alias){
        $db = \JFactory::getDBO();
        $query = "SELECT id FROM `#__jshopping_shipping_ext_calc` WHERE `alias`='".$db->escape($alias)."'";
        extract(\JSHelper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $id = $db->loadResult();
        return $this->load($id);
    }
    
    function load($id = null, $reset = true){
        $return = parent::load($id, $reset);
        $JshopConfig = \JSFactory::getConfig();
        $path = $JshopConfig->path."shippings";
        $extname = $this->alias;
        $filepatch = $path."/".$extname."/".$extname.".php";
        if (file_exists($filepatch)){
            include_once($filepatch);
            $this->exec = new $extname();
        }else{
            \JSError::raiseWarning("","Load ShippingExt ".$extname." error.");
        }
        
        return $return;
    }
    
    function getList($active = 0){
        $db = \JFactory::getDBO();
        $adv_query = "";
        if ($active==1){
            $adv_query = "where `published`='1'";
        }
        $query = "select * from `#__jshopping_shipping_ext_calc` ".$adv_query." order by `ordering`";
        $db->setQuery($query);
        return $db->loadObJectList();
    }
    
    function setShippingMethod($data){
        $this->shipping_method = serialize($data);
    }
    
    function getShippingMethod(){
        if ($this->shipping_method=="") return array();
        return unserialize($this->shipping_method);
    }
    
    function setParams($data){
        $this->params = serialize($data);
    }
    
    function getParams(){        
        if ($this->params=="") return array();
        return unserialize($this->params);
    }
}