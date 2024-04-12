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

class AttributValueTable extends MultilangTable{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_attr_values', 'value_id', $_db);
    }
    
    function getAllValues($attr_id) {
        $db = \JFactory::getDBO(); 
        $lang = \JSFactory::getLang();
        $query = "SELECT value_id, image, `".$lang->get("name")."` as name, value_ordering, attr_id FROM `#__jshopping_attr_values` "
                . "where attr_id=".(int)$attr_id." ORDER BY value_ordering, value_id";
        $db->setQuery($query);
        return $db->loadObJectList();
    }
    
    /**
    * get All Atribute value
    * @param $resulttype (0 - ObJectList, 1 - array {id->name}, 2 - array(id->obJect) )
    * 
    * @param mixed $resulttype
    */
    function getAllAttributeValues($resulttype=0){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT value_id, image, `".$lang->get("name")."` as name, attr_id, value_ordering FROM `#__jshopping_attr_values` ORDER BY value_ordering, value_id";
        $db->setQuery($query);
        $attribs = $db->loadObJectList();

        if ($resulttype==2){
            $rows = array();
            foreach($attribs as $k=>$v){
                $rows[$v->value_id] = $v;    
            }
            return $rows;
        }elseif ($resulttype==1){
            $rows = array();
            foreach($attribs as $k=>$v){
                $rows[$v->value_id] = $v->name;    
            }
            return $rows;
        }else{
            return $attribs;
        }        
    }
    
    public function getNextOrder($where = ''){
        $db = \JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('MAX(value_ordering)')
			->from($this->_tbl);
		if ($where){
			$query->where($where);
		}
		$db->setQuery($query);
		$max = (int) $db->loadResult();
		return ($max + 1);
	}

    public function reorder($where = '', $fieldordering = 'ordering'){
		return parent::reorder($where, 'value_ordering');
    }
  
}