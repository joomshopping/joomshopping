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

class AttributesGroupTable extends MultilangTable{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_attr_groups', 'id', $_db );
    }
    
    function getList(){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang(); 
        $query = "SELECT id, `".$lang->get("name")."` as name, ordering FROM `#__jshopping_attr_groups` order by ordering";
        $db->setQuery($query);
        return $db->loadObJectList();
    }

}