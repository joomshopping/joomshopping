<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model; 
defined('_JEXEC') or die;

class VendorsModel extends BaseadminModel{
    
    protected $nameTable = 'vendor';

    function getNamesVendors() {
        $db = \JFactory::getDBO();
        $query = "SELECT id, f_name, l_name FROM `#__jshopping_vendors` ORDER BY f_name, l_name DESC";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getAllVendors($limitstart, $limit, $text_search="") {
        $db = \JFactory::getDBO();
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (f_name like '%".$search."%' or l_name like '%".$search."%' or email like '%".$search."%') ";
        }
        $query = "SELECT * FROM `#__jshopping_vendors` where 1 ".$where." ORDER BY id DESC";
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function getCountAllVendors($text_search = "") {
        $db = \JFactory::getDBO();
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (f_name like '%".$search."%' or l_name like '%".$search."%' or email like '%".$search."%') ";
        }
        $query = "SELECT COUNT(id) FROM `#__jshopping_vendors` where 1 ".$where;
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAllVendorsNames($main_id_null = 0){
        $db = \JFactory::getDBO();
        $query = "SELECT id, concat(f_name, ' ', l_name) as name, `main` FROM `#__jshopping_vendors` ORDER BY name";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($main_id_null){
            foreach($rows as $k=>$v){
                if ($v->main) $rows[$k]->id = 0;
            }
        }
        return $rows;
    }

    function getIdVendorForUserId($id){
        $db = \JFactory::getDBO();
        $query = "SELECT id FROM `#__jshopping_vendors` where user_id='".$db->escape($id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }

    function save(array $post){
        $vendor = \JSFactory::getTable('vendor');
        $dispatcher = \JFactory::getApplication();
        $vendor->load($post["id"]);
        $post['publish'] = (int) $post['publish'];
        if (isset($post['user_id'])){
            $post['user_id'] = (int)$post['user_id'];
        }
        $dispatcher->triggerEvent('onBeforeSaveVendor', array(&$post));
        $vendor->bind($post);
        \JSFactory::loadLanguageFile();
        if (!$vendor->check()){            
            $this->setError($vendor->getError());
            return 0;
        }
        if (!$vendor->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE').' '.$vendor->getError());
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveVendor', array(&$vendor));
        return $vendor;
    }

    function deleteList(array $cid, $msg = 1){
        $res = array();
        $app = \JFactory::getApplication();
        $vendor = \JSFactory::getTable('vendor');
        $db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveVendor', array(&$cid));
        foreach($cid as $id) {
            $query = "select count(*) from `#__jshopping_products` where `vendor_id`=" . intval($id);
            $db->setQuery($query);
			if( !$db->loadResult() ){
                $query = "delete from `#__jshopping_vendors` where id='" . $db->escape($id) . "' and main=0";
                $db->setQuery($query);
                $db->execute();
                $res[ $id ] = true;
            } else {
                $vendor->load($id);
                if ($msg){
                    $app->enqueueMessage(sprintf(\JText::_('JSHOP_ITEM_ALREADY_USE'), $vendor->f_name . " " . $vendor->l_name), 'error');
                }
                $res[ $id ] = false;
            }
        }
        $dispatcher->triggerEvent('onAfterRemoveVendor', array(&$cid));
        return $res;
    }

}