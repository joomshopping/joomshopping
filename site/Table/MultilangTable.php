<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

abstract class MultilangTable extends ShopbaseTable{

    public function getName($id = null){        
		if (!is_null($id)){
			return $this->getNameForId($id);
		}
		$lang = \JSFactory::getLang();
        $field = $lang->get("name");
    return $this->$field;
    }
	
	public function getDescription(){
        $lang = \JSFactory::getLang();
        $field = $lang->get("description");
    return $this->$field;
    }
	
	public function getNameForId($id){
		$db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();		
        $query = "SELECT `".$lang->get("name")."` as name FROM `".$this->_tbl."` WHERE `".$this->_tbl_key."` = '".$db->escape($id)."'";
        $db->setQuery($query);
        return $db->loadResult();
	}
	
}