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

class ProductLabelTable extends MultilangTable{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_product_labels', 'id', $_db);
    }
    
	function getListLabels(){
		$lang = \JSFactory::getLang();
		$db = \JFactory::getDBO();
		$query = "SELECT id, image, `".$lang->get("name")."` as name FROM `#__jshopping_product_labels` ORDER BY name";
		$db->setQuery($query);
		$list = $db->loadObJectList();
		$rows = array();
		foreach($list as $row){
			$rows[$row->id] = $row;
		}
	return $rows;
    }

}