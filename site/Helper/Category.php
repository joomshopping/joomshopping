<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
defined('_JEXEC') or die();

class Category{
	
	public static function getListSubCatsId($ids = array()){
        $db = \JFactory::getDBO();
        if (!count($ids)){
            return array();
        }
        $ids = \JSHelper::filterAllowValue($ids, 'int+');
        $query = "select category_id from `#__jshopping_categories` where category_parent_id in (".implode(',', $ids).")";
        $db->setQuery($query);
        $list = $db->loadObjectList();
        $rows = array();
        foreach($list as $v){
            $rows[] = $v->category_id;
        }
        return $rows;
    }
    
    public static function getAllChildrenCatsId($id){        
        $rows = array();       
        $list = array($id);
        while(count($list)){            
            $list = self::getListSubCatsId($list);
            $rows = array_merge($rows, $list);
        }
        return array_unique($rows);
    }
	
}