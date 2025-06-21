<?php
/**
* @version      5.6.2 26.02.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die();

class ProductfieldsModel extends BaseadminModel{
    
    protected $nameTable = 'productfield';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getList($params['groupordering'] ?? 0, $orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters, $params['printCatName'] ?? 0);
	}
	
	public function getList($groupordering = 0, $order = null, $orderDir = null, $filter=array(), $printCatName = 0){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
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
        $query = "SELECT F.id, F.`".$lang->get("name")."` as name, F.`".$lang->get("description")."` as description, F.allcats, F.type, F.cats, F.ordering, F.`group`, G.`".$lang->get("name")."` as groupname, multilist, publish 
                    FROM `#__jshopping_products_extra_fields` as F 
                    left join `#__jshopping_products_extra_field_groups` as G on G.id=F.group ".
                    $where." order by ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
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
            $_categories = JSFactory::getModel("categories");
            $listCats = $_categories->getAllList(1);
            foreach($rows as $k=>$v){
                if ($v->allcats){
                    $rows[$k]->printcat = Text::_('JSHOP_ALL');
                }else{
                    $catsnames = array();
                    $_cats = unserialize($v->cats);
                    foreach($_cats as $cat_id){
                        $catsnames[] = $listCats[$cat_id] ?? '';
                        $rows[$k]->printcat = implode(", ", $catsnames);
                    }
                }
            }
        }
        return $rows;
    }
	
	public function getPrepareDataSave($input){
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $post = $input->post->getArray();
        foreach($languages as $lang){
            $post['description_'.$lang->language] = $input->get('description_'.$lang->language, '', 'RAW');
        }
        return $post;
    }
    
    public function save(array $post){
        $id = (int)$post["id"];
        $productfield = JSFactory::getTable('productfield');
        $post['multilist'] = 0;
        if ($post['type']==-1){
            $post['type'] = 0;
            $post['multilist'] = 1;
        }
        $app = Factory::getApplication();
        $app->triggerEvent('onBeforeSaveProductField', array(&$post));
        if ($id) {
            $productfield->load($id);
            $old_type = $productfield->type;
            $old_multilist = $productfield->multilist;
            if ($old_type == 1 && $post['type'] != 1) {
                $this->converProductDataDeprecatedTextToList($id, $post['type']);
            }
        }
        $productfield->bind($post);
        $categorys = $post['category_id'] ?? [];
        $productfield->setCategorys($categorys);
        if (!$id){
            $productfield->ordering = null;
            $productfield->ordering = $productfield->getNextOrder();
        }
        if (!$productfield->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE')." ".$productfield->getError());
            return 0; 
        }
        if (!$id){
            $productfield->addNewFieldProducts();
        } else {
            if ($old_type != $productfield->type || $old_multilist != $productfield->multilist) {
                $productfield->updateFieldProducts();
            }
        }
        $app->triggerEvent('onAfterSaveProductField', array(&$productfield));
        return $productfield;
    }
    
    public function deleteList(array $cid, $msg = 1) {
        $app = Factory::getApplication();
        $res = array();
        $app->triggerEvent('onBeforeRemoveProductField', array(&$cid));
        foreach($cid as $id){
            $this->deleteAllValueFromField($id);
            $productfield = JSFactory::getTable('productfield');
            $productfield->id = $id;
            $productfield->deleteFieldProducts();
            if ($productfield->delete()) {
                if ($msg){
                    $app->enqueueMessage(Text::_('JSHOP_ITEM_DELETED'), 'message');
                }
                $res[$id] = true;
            } else {
                $res[$id] = false;
            }
        }
        $app->triggerEvent('onAfterRemoveProductField', array(&$cid));
        return $res;
    }

    public function deleteAllValueFromField($field_id) {
        $db = Factory::getDBO();
        $query = "DELETE FROM `#__jshopping_products_extra_field_values` WHERE `field_id`=".$db->q($field_id);
        $db->setQuery($query);
        $db->execute();
    }

    public function getTypes($show_deprecated = 0){
        $types = [
            0 => Text::_('JSHOP_LIST'),
            -1 => Text::_('JSHOP_MULTI_LIST'),
            3 => Text::_('JSHOP_TEXT')." (".Text::_('JSHOP_SAVE_UNIQUE').")",
            2 => Text::_('JSHOP_TEXT'),
            1 => Text::_('JSHOP_TEXT')." (".Text::_('JSHOP_DEPRECATED').")",
        ];
        if (!$show_deprecated) {
            unset($types[1]);
        }
        $app = Factory::getApplication();
        $app->triggerEvent('onBeforeGetTypesProductField', array(&$types, &$show_deprecated));
        return $types;
    }

    public function getListProducsValueByExtraFieldId($id) {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $field = 'extra_field_'.(int)$id;
        $query->select($db->qn(['product_id', $field]))
            ->from($db->qn('#__jshopping_products_to_extra_fields'));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function converProductDataDeprecatedTextToList($id, $new_type) {
        $modelVal = JSFactory::getModel('ProductFieldValues');
        $field = 'extra_field_'.(int)$id;
        $list = $this->getListProducsValueByExtraFieldId($id);
        $langs = JSFactory::getModel('Languages')->getAllTags();
        foreach ($list as $item) {
            if ($item->$field !== '') {
                Helper::saveToLog('convert_extrafield_dep_text.log', 'pid: '.$item->product_id.' efid: '.$id.' val: '.$item->$field);
                $data = ['id' => 0, 'field_id' => $id];
                foreach($langs as $lang) {
                    $data['name_'.$lang] = $item->$field;
                }
                if ($new_type != 2) {
                    $data['_save_unique'] = 1;
                }
                $productfieldvalue = $modelVal->save($data);
                $modelVal->updateProductValue($item->product_id, $id, $productfieldvalue->id);
                Helper::saveToLog('convert_extrafield_dep_text.log', 'new vid: '.$productfieldvalue->id);
            }
        }
    }

    public function getListForCats($categorys = []) {
        $list = $this->getList(1);
        $res = [];
        foreach($list as $v){
            $insert = 0;
            if ($v->allcats==1){
                $insert = 1;
            } else {
                $cats = unserialize($v->cats);
                foreach($categorys as $catid){
                    if (in_array($catid, $cats)) $insert = 1;
                }
            }
            if ($insert) {
                $res[$v->id] = $v;
            }
        }
        return $res;
    }

    public function getListIdByType($type) {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from($db->qn('#__jshopping_products_extra_fields'))
            ->where($db->qn('type').' = '.(int)$type);
        $db->setQuery($query);
        return $db->loadColumn();
    }

	public function getProductCount($field_id) {
		$productfield = JSFactory::getTable('productfield');
		$productfield->load($field_id);
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$field = 'extra_field_' . $field_id;

		$query->select('COUNT(product_id)')
		      ->from($db->quoteName('#__jshopping_products_to_extra_fields'))
		      ->where($db->quoteName($field) . ' != ' . $db->quote(''));

		if ($productfield->type != 1) {
			$query->where($db->quoteName($field) . ' != 0');
		}
		$db->setQuery($query);
		return $db->loadResult();
	}

}