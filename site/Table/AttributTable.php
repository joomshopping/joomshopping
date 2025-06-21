<?php
/**
* @version      5.0.7 19.08.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
defined('_JEXEC') or die();

class AttributTable extends MultilangTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_attr', 'attr_id', $_db);
    }

    function getAllAttributes($groupordering = 1, $filter = []){
        $lang = JSFactory::getLang();
        $db = Factory::getDBO();
        $ordering = "A.attr_ordering";
        if ($groupordering){
            $ordering = "G.ordering, A.attr_ordering";
        }
        $where = '';
        if (isset($filter['publish'])) {
            $where .= ' AND A.publish='.$db->q($filter['publish']);
        }
        $query = "SELECT A.attr_id, A.`".$lang->get("name")."` as name, A.`".$lang->get("description")."` as description, A.attr_type, A.independent, A.allcats, A.cats, A.attr_ordering, G.`".$lang->get("name")."` as groupname, G.id as group_id, required, publish
                  FROM `#__jshopping_attr` as A left Join `#__jshopping_attr_groups` as G on A.`group`=G.id
                  WHERE 1 ".$where."
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
        $db = Factory::getDBO();
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
        $db = Factory::getDBO();
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
        $db = Factory::getDBO();
        $table = "#__jshopping_products_attr";
        $field = "attr_".$this->attr_id;
        $listfields = $db->getTableColumns($table);
        if (!isset($listfields[$field])){
            $query = "ALTER TABLE ".$db->qn($table)." ADD ".$db->qn($field)."  INT(11) NOT NULL";
            $db->setQuery($query);
            $db->execute();
        }
    }
    
    public function reorder($where = '', $fieldordering = 'ordering'){
		return parent::reorder($where, 'attr_ordering');
    }

}