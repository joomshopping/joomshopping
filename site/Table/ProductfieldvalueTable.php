<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

class ProductFieldValueTable extends MultilangTable{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_extra_field_values', 'id', $_db );
    }
    
    function getAllList($display = 0, $filter = []){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $where = '';
        if (isset($filter['publish'])) {
            $where .= ' AND publish='.$db->q($filter['publish']);
        }
        $query = "SELECT id, `".$lang->get("name")."` as name, field_id 
                  FROM `#__jshopping_products_extra_field_values` 
                  WHERE 1 ".$where."
                  order by ordering";
        $db->setQuery($query);
        if ($display==0){
            return $db->loadObJectList();
        }elseif($display==1){
            $rows = $db->loadObJectList();
            $list = array();
            foreach($rows as $k=>$row){
                $list[$row->id] = $row->name;
                unset($rows[$k]);    
            }
            return $list;
        }else{
            $rows = $db->loadObJectList();
            $list = array();
            foreach($rows as $k=>$row){
                $list[$row->field_id][$row->id] = $row->name;
                unset($rows[$k]);    
            }
            return $list;
        }
    }

}