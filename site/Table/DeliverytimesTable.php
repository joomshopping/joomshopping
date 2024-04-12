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

class DeliveryTimesTable extends MultilangTable{
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_delivery_times', 'id', $_db );
    }
    
    function getDeliveryTimes(){
        $db = \JFactory::getDBO();    
        $lang = \JSFactory::getLang();     
        $query = "SELECT id, `".$lang->get('name')."` as name FROM `#__jshopping_delivery_times` ORDER BY name";
        $db->setQuery($query);
        return $db->loadObJectList();
    }
        
}