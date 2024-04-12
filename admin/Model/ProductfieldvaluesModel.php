<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class ProductFieldValuesModel extends BaseadminModel{
    
    protected $nameTable = 'productFieldValue';

	function getList($field_id, $order = null, $orderDir = null, $filter=array()){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $ordering = 'ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $where = '';
		if ($filter['text_search']){
            $text_search = $filter['text_search'];
            $word = addcslashes($db->escape($text_search), "_%");
            $where =  " and (LOWER(`".$lang->get('name')."`) LIKE '%".$word."%' OR id LIKE '%".$word."%')";
        }
        $query = "SELECT id, `".$lang->get("name")."` as name, ordering FROM `#__jshopping_products_extra_field_values` where field_id='$field_id' ".$where." order by ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getAllList($display = 0){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT id, `".$lang->get("name")."` as name, field_id FROM `#__jshopping_products_extra_field_values` order by ordering";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        if ($display==0){
            return $db->loadObjectList();
        }elseif($display==1){
            $rows = $db->loadObjectList();
            $list = array();
            foreach($rows as $k=>$row){
                $list[$row->id] = $row->name;
                unset($rows[$k]);
            }
            return $list;
        }else{
            $rows = $db->loadObjectList();
            $list = array();
            foreach($rows as $k=>$row){
                $list[$row->field_id][$row->id] = $row->name;
                unset($rows[$k]);
            }
            return $list;
        }
    }

    public function save(array $post){
        $productfieldvalue = \JSFactory::getTable('productFieldValue');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveProductFieldValue', array(&$post));
        if( !$productfieldvalue->bind($post) ) {
            \JSError::raiseWarning("",\JText::_('JSHOP_ERROR_BIND'));
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues");
            return 0;
        }
        if( !$post['id'] ) {
            $productfieldvalue->ordering = null;
            $productfieldvalue->ordering = $productfieldvalue->getNextOrder('field_id="' . $post['field_id'] . '"');
        }
        if( !$productfieldvalue->store() ) {
            \JSError::raiseWarning("",\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
            $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues");
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveProductFieldValue', array(&$productfieldvalue));
        return $productfieldvalue;
    }

    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $db = \JFactory::getDBO();
        foreach($cid as $value) {
            $query = "DELETE FROM `#__jshopping_products_extra_field_values` WHERE `id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->execute();
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
            }
        }
        \JFactory::getApplication()->triggerEvent('onAfterRemoveProductFieldValue', array(&$cid));
    }

}