<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
defined('_JEXEC') or die();

class ProductFieldsModel extends BaseadminModel{
    
    protected $nameTable = 'productFieldTable';
	
	function getList($groupordering = 0, $order = null, $orderDir = null, $filter=array(), $printCatName = 0){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $ordering = "F.ordering";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        if ($groupordering){
            $ordering = "G.ordering, ".$ordering;
        }        
        $where = '';
		$_where = array();		
		if (isset($filter['group']) && $filter['group']){
            $_where[] = " F.group = '".$db->escape($filter['group'])."' ";    
        }		
		if (isset($filter['text_search']) && $filter['text_search']){
            $text_search = $filter['text_search'];
            $word = addcslashes($db->escape($text_search), "_%");
            $_where[]=  "(LOWER(F.`".$lang->get('name')."`) LIKE '%" . $word . "%' OR LOWER(F.`".$lang->get('description')."`) LIKE '%" . $word . "%' OR F.id LIKE '%" . $word . "%')";            
        }		
		if (count($_where)>0){
			$where = " WHERE ".implode(" AND ",$_where);
		}
        $query = "SELECT F.id, F.`".$lang->get("name")."` as name, F.`".$lang->get("description")."` as description, F.allcats, F.type, F.cats, F.ordering, F.`group`, G.`".$lang->get("name")."` as groupname, multilist FROM `#__jshopping_products_extra_fields` as F left join `#__jshopping_products_extra_field_groups` as G on G.id=F.group ".$where." order by ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (isset($filter['category_id']) && $filter['category_id']){
            foreach($rows as $k=>$v){
                if (!$v->allcats){
                    $_cats = unserialize($v->cats);
                    if (!in_array($filter['category_id'], $_cats)){
                        unset($rows[$k]);
                    }
                }
            }
        }
        if ($printCatName){
            $_categories = \JSFactory::getModel("categories");
            $listCats = $_categories->getAllList(1);
            foreach($rows as $k=>$v){
                if ($v->allcats){
                    $rows[$k]->printcat = \JText::_('JSHOP_ALL');
                }else{
                    $catsnames = array();
                    $_cats = unserialize($v->cats);
                    foreach($_cats as $cat_id){
                        $catsnames[] = $listCats[$cat_id];
                        $rows[$k]->printcat = implode(", ", $catsnames);
                    }
                }
            }
        }
        return $rows;
    }
    
    function save(array $post){
        $id = (int)$post["id"];
        $productfield = \JSFactory::getTable('productField');        
        if ($post['type']==-1){
            $post['type'] = 0;
            $post['multilist'] = 1;
        }else{
            $post['multilist'] = 0;
        }
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveProductField', array(&$post));
        $productfield->bind($post);        
        $categorys = $post['category_id'];
        if (!is_array($categorys)){
            $categorys = array();
        }        
        $productfield->setCategorys($categorys);
        if (!$id){
            $productfield->ordering = null;
            $productfield->ordering = $productfield->getNextOrder();            
        }
        if (!$productfield->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));            
            return 0; 
        }
        if (!$id){            
            $productfield->addNewFieldProducts();
        }
        $dispatcher->triggerEvent('onAfterSaveProductField', array(&$productfield));        
        return $productfield;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = \JFactory::getDBO();
        $app = \JFactory::getApplication();
        $res = array();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveProductField', array(&$cid));
        foreach($cid as $value){
            $query = "DELETE FROM `#__jshopping_products_extra_fields` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            if ($db->execute()){
                if ($msg){
                    $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
                }
                $res[$value] = true;
            }else{
                $res[$value] = false;
            }
            
            $query = "DELETE FROM `#__jshopping_products_extra_field_values` WHERE `field_id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            $db->execute();
            
            $query = "ALTER TABLE `#__jshopping_products_to_extra_fields` DROP `extra_field_".(int)$value."`";
            $db->setQuery($query);
            $db->execute();
        }
        $dispatcher->triggerEvent('onAfterRemoveProductField', array(&$cid));
        return $res;
    }

}