<?php
/**
* @version      5.5.6 24.02.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;

class ProductfieldvaluesModel extends BaseadminModel{
    
    protected $nameTable = 'productfieldvalue';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getList($filters['field_id'] ?? null, $orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters);
	}

	public function getList($field_id = null, $order = null, $orderDir = null, $filter = []){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $ordering = 'ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $where = '';
        if (isset($field_id)) {
            $where .= ' AND field_id='.$db->q($field_id);
        }
		if (isset($filter['text_search']) && $filter['text_search']) {
            $text_search = $filter['text_search'];
            $word = addcslashes($db->escape($text_search), "_%");
            $where .= " and (LOWER(`".$lang->get('name')."`) LIKE '%".$word."%' OR id LIKE '%".$word."%')";
        }
        $query = "SELECT id, `".$lang->get("name")."` as name, ordering, publish
                  FROM `#__jshopping_products_extra_field_values` 
                  WHERE 1 ".$where." order by ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getAllList($display = 0, $ordering = 'ordering'){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT id, `".$lang->get("name")."` as name, field_id FROM `#__jshopping_products_extra_field_values` order by ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
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
        }elseif ($display==10){
            return $db->loadObjectList('id');
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

    public function getListRaw($field_id, $db_filed_select = '*') {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db_filed_select)
            ->from($db->qn('#__jshopping_products_extra_field_values'))
            ->where($db->qn('field_id') . '=' . $db->q($field_id))
            ->order('id');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function save(array $post){
        $db = Factory::getDBO();
        $productfieldvalue = JSFactory::getTable('productFieldValue');
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveProductFieldValue', array(&$post));
        if (!$post['id']) {
            $productfield = JSFactory::getTable('productfield');
            $productfield->load($post['field_id']);
            if ($productfield->type == 3 || (isset($post['_save_unique']) && $post['_save_unique'])) {
                $post['id'] = $this->getIdByNames($post['field_id'], $post);
                if ($post['id']) {
                    $productfieldvalue->load($post['id']);
                    return $productfieldvalue;
                }
            }
        }
        $productfieldvalue->bind($post);
        if (!$post['id']) {
            $productfieldvalue->ordering = null;
            $productfieldvalue->ordering = $productfieldvalue->getNextOrder('field_id='.$db->q($post['field_id']));
        }
        $productfieldvalue->store();
        $dispatcher->triggerEvent('onAfterSaveProductFieldValue', array(&$productfieldvalue));
        return $productfieldvalue;
    }

    public function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();        
        $productfield = JSFactory::getTable('productField');
        foreach($cid as $id) {
            $productfieldvalue = JSFactory::getTable('productFieldValue');
            $productfieldvalue->load($id);
            $productfield->clearValueFromFieldProduct($productfieldvalue->field_id, $id);
            $productfieldvalue->delete();
            if ($msg){
                $app->enqueueMessage(Text::_('JSHOP_ITEM_DELETED'), 'message');
            }
        }
        Factory::getApplication()->triggerEvent('onAfterRemoveProductFieldValue', array(&$cid));
    }

    public function getIdByNames($field_id, $names) {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn('id'))
            ->from($db->qn('#__jshopping_products_extra_field_values'))
            ->where($db->qn('field_id')."=".$db->q($field_id))
            ->order('id');
        $count_name = 0;
        foreach($names as $k => $v) {
            if ($v != '' && preg_match('/name_([a-z]{2})-([A-Z]{2})/', $k)) {
                $query->where($db->qn($k)."=".$db->q($v));
                $count_name++;
            }
        }
        if ($count_name == 0) {
            return 0;
        } else {
            $db->setQuery($query);
            return (int)$db->loadResult();
        }        
    }

    public function clearDoubleValues($field_id) {
        $list = $this->getListRaw($field_id);
        $langs = JSFactory::getModel('Languages')->getAllTags();
        $updated = 0;
        foreach($list as $item) {
            $names = [];
            foreach($langs as $lang) {
                $field = 'name_'.$lang;
                $names[$field] = $item->$field;
            }
            $first_id = $this->getIdByNames($field_id, $names);
            if ($first_id && $first_id != $item->id) {
                Helper::saveToLog('extrafield_clear_double.log', 'efid: '.$field_id.' oldval: '.$item->id.' newid: '.$first_id);
                $this->deleteById($item->id);
                $this->updateAllProductsValue($field_id, $item->id, $first_id);
                $updated++;
            }
        }
        return $updated;
    }

    public function deleteById($id) {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->delete($db->qn('#__jshopping_products_extra_field_values'))
            ->where($db->qn('id') . '=' .$db->q($id));
        $db->setQuery($query);
        $db->execute();
    }

    public function updateAllProductsValue($field_id, $old_val_id, $new_val_id) {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $field = 'extra_field_'.(int)$field_id;
        $query->update($db->qn('#__jshopping_products_to_extra_fields'))
            ->set($db->qn($field) . "=" . $db->q($new_val_id))
            ->where($db->qn($field) . '=' . $db->q($old_val_id));
        $db->setQuery($query);
        $db->execute();
    }

    public function updateProductValue($product_id, $field_id, $val_id) {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $field = 'extra_field_'.(int)$field_id;
        $query->update($db->qn('#__jshopping_products_to_extra_fields'))
            ->set($db->qn($field) . "=" . $db->q($val_id))
            ->where($db->qn('product_id') . '=' . $db->q($product_id));
        $db->setQuery($query);
        $db->execute();
    }

    public function copy($id) {
        $db = Factory::getDBO();
        $table = JSFactory::getTable('productfieldvalue');
        $table->load($id);
        $table->id = null;
        $table->ordering = null;
        $table->ordering = $table->getNextOrder('field_id='.$db->q($table->field_id));
        $table->store();
        return $table->id;
    }

	public function getProductCount($field_id, $value_id) {
		$productfield = JSFactory::getTable('productfield');
		$productfield->load($field_id);
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$field = 'extra_field_' . $field_id;
		if ($productfield->multilist){
			$query->select('COUNT(product_id)')
			      ->from($db->quoteName('#__jshopping_products_to_extra_fields'))
			      ->where('FIND_IN_SET(' . $db->quote($value_id) . ', ' . $db->quoteName($field) . ')');
		} else {
			$query->select('COUNT(product_id)')
			      ->from($db->quoteName('#__jshopping_products_to_extra_fields'))
			      ->where($db->quoteName($field) . ' = ' . $db->quote($value_id));
		}
		$db->setQuery($query);
		return $db->loadResult();
	}
}