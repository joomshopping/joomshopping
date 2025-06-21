<?php
/**
* @version      5.3.4 26.02.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

class ProductFieldTable extends MultilangTable{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_extra_fields', 'id', $_db );
    }
    
    /**
    * set categorys
    * 
    * @param array $cats
    */
    function setCategorys($cats){
        $this->cats = serialize($cats);
    }
    
    /**
    * get gategoryd
    * 
    * @return array
    */    
    function getCategorys(){
        if ($this->cats!=""){
            return unserialize($this->cats);
        }else{
            return array();
        }
    }
    
    function getList($groupordering = 1, $filter = []){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $ordering = "F.ordering";
        if ($groupordering){
            $ordering = "G.ordering, F.ordering";
        }
        $where = '';
        if (isset($filter['publish'])) {
            $where .= ' AND F.publish='.$db->q($filter['publish']);
        }
        $query = "SELECT F.id, F.`".$lang->get("name")."` as name, F.`".$lang->get("description")."` as description, F.allcats, F.type, F.cats, F.ordering, F.`group`, G.`".$lang->get("name")."` as groupname, multilist, publish
                  FROM `#__jshopping_products_extra_fields` as F 
                  LEFT JOIN `#__jshopping_products_extra_field_groups` as G on G.id=F.group 
                  WHERE 1 ".$where."
                  ORDER BY ".$ordering;
        $db->setQuery($query);
        $rows = $db->loadObJectList();
        $list = array();        
        foreach($rows as $k=>$v){
            $list[$v->id] = $v;
            if ($v->allcats){
                $list[$v->id]->cats = array();
            }else{
                $list[$v->id]->cats = unserialize($v->cats);
            }            
        }
        unset($rows);
        return $list;
    }
    
    function addNewFieldProducts(){
        $jshopConfig = JSFactory::getConfig();
        $db = Factory::getDBO();
        $field = 'extra_field_'.(int)$this->id;
        $fl_type = $jshopConfig->new_extra_field_type_list;
        if ($this->type == 1) {
            $fl_type = $jshopConfig->new_extra_field_type;
        }
        if ($this->multilist == 1) {
            $fl_type = $jshopConfig->new_extra_field_type_multilist;
        }
        $query = "ALTER TABLE `#__jshopping_products_to_extra_fields` ADD `".$field."` ".$fl_type." NOT NULL";
        $db->setQuery($query);
        $db->execute();
    }

    function updateFieldProducts() {
        $jshopConfig = JSFactory::getConfig();
        $db = Factory::getDBO();
        $field = 'extra_field_'.(int)$this->id;
        $fl_type = $jshopConfig->new_extra_field_type_list;
        if ($this->type == 1) {
            $fl_type = $jshopConfig->new_extra_field_type;
        }
        if ($this->multilist == 1) {
            $fl_type = $jshopConfig->new_extra_field_type_multilist;
        }
        $query = "ALTER TABLE `#__jshopping_products_to_extra_fields` CHANGE `".$field."` `".$field."` ".$fl_type." NOT NULL";
        $db->setQuery($query);
        $db->execute();
    }

    function deleteFieldProducts(){
        $db = Factory::getDBO();
        $field = 'extra_field_'.(int)$this->id;
        $query = "ALTER TABLE `#__jshopping_products_to_extra_fields` DROP `".$field."`";
        $db->setQuery($query);
        $db->execute();
    }

    function clearValueFromFieldProduct($field_id, $value_id) {
        $db = Factory::getDBO();
        $field = "extra_field_".(int)$field_id;
        $query = "UPDATE `#__jshopping_products_to_extra_fields` SET ".$db->qn($field)."=0 WHERE ".$db->qn($field)."=".$db->q($value_id);
        $db->setQuery($query);
        $db->execute();
    }
    
}