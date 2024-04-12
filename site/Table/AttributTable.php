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

class AttributTable extends MultilangTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_attr', 'attr_id', $_db);
    }

    function getAllAttributes($groupordering = 1){
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        $ordering = "A.attr_ordering";
        if ($groupordering){
            $ordering = "G.ordering, A.attr_ordering";
        }        
        $query = "SELECT A.attr_id, A.`".$lang->get("name")."` as name, A.`".$lang->get("description")."` as description, A.attr_type, A.independent, A.allcats, A.cats, A.attr_ordering, G.`".$lang->get("name")."` as groupname
                  FROM `#__jshopping_attr` as A left Join `#__jshopping_attr_groups` as G on A.`group`=G.id
                  ORDER BY ".$ordering;
        $db->setQuery($query);
        $rows = $db->loadObJectList();
        foreach($rows as $k=>$v){
            if ($v->allcats){
                $rows[$k]->cats = array();
            }else{
                $rows[$k]->cats = unserialize($v->cats);
            }
        }
    return $rows;
    }
    
    function getTypeAttribut($attr_id){
        $db = \JFactory::getDBO();
        $query = "select attr_type from #__jshopping_attr where `attr_id`='".$db->escape($attr_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function setCategorys($cats){
        $this->cats = serialize($cats);
    }
      
    function getCategorys(){
        if ($this->cats!=""){
            return unserialize($this->cats);
        }else{
            return array();
        }
    }
    
    public function getNextOrder($where = ''){
        $db = \JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('MAX(attr_ordering)')
			->from($this->_tbl);

		if ($where){
			$query->where($where);
		}
		$db->setQuery($query);
		$max = (int) $db->loadResult();
		return ($max + 1);
	}
    
    public function addNewFieldProductsAttr(){
        $db = \JFactory::getDBO();
        $query = "ALTER TABLE `#__jshopping_products_attr` ADD `attr_".$this->attr_id."` INT(11) NOT NULL";
        $db->setQuery($query);
        $db->execute();
    }
    
    public function reorder($where = '', $fieldordering = 'ordering'){
		return parent::reorder($where, 'attr_ordering');
    }

}