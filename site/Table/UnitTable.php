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

class UnitTable extends MultilangTable{
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_unit', 'id', $_db );
    }
    
    function getAllUnits(){
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO(); 
        $query = "SELECT id, `".$lang->get("name")."` as name, qty FROM `#__jshopping_unit` ORDER BY id";
        $db->setQuery($query);
        $list = $db->loadObJectList();
        $rows = array();
        foreach($list as $row){
             $rows[$row->id] = $row;
        }
        return $rows;
    }
        
}