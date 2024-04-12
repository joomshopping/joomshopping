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

class FreeAttributTable extends MultilangTable{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_free_attr', 'id', $_db );
    }
    
    function getAll() {
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO(); 
        $query = "SELECT id, `".$lang->get("name")."` as name, required, ordering FROM `#__jshopping_free_attr` ORDER BY `ordering`";
        $db->setQuery($query);
        return $db->loadObJectList();
    }
    
    function getAllNames(){
        $rows = array();
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO(); 
        $query = "SELECT id, `".$lang->get("name")."` as name FROM `#__jshopping_free_attr` ORDER BY `ordering`";
        $db->setQuery($query);
        $list = $db->loadObJectList();        
        foreach($list as $v){
            $rows[$v->id] = $v->name;
        }
        return $rows;
    }
}