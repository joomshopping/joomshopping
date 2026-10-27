<?php
/**
* @version      5.10.0 27.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\UploadFile;
defined('_JEXEC') or die();

class AttributValueModel extends BaseadminModel{
    
    protected $tableFieldOrdering = 'value_ordering';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []) {
		return $this->getAllValues($filters['attr_id'] ?? null, $orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters);
	}
    
    function getNameValue($value_id) {
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_attr_values` WHERE value_id = '".$db->escape($value_id)."'";
        $db->setQuery($query);        
        return $db->loadResult();
    }

    function getAllValues($attr_id = null, $order = null, $orderDir = null, $filter = [], $params = []) {
        $db = Factory::getDBO(); 
        $lang = JSFactory::getLang();
        $ordering = 'value_ordering, value_id';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $where = '';
        if (isset($attr_id)) {
            $where .= " AND attr_id=".$db->q($attr_id);
        }
        if (isset($filter['text_search'])) {
            $word = addcslashes($db->escape($filter['text_search']), "_%");
            $where .= " AND (LOWER(`".$lang->get("name")."`) LIKE ".$db->q('%'.$word.'%').")";
        }
        if (isset($filter['publish'])) {
            $where .= ' AND publish='.$db->q($filter['publish']);
        }
        $query = "SELECT value_id, image, `".$lang->get("name")."` as name, attr_id, value_ordering, publish
        FROM `#__jshopping_attr_values` where 1 ".$where."
        ORDER BY ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($params['calculate_product_count'] ?? 0) {
            foreach ($rows as $k => $row) {
                $rows[$k]->count_products = $this->getProductCount($attr_id, $row->value_id);
            }
            if (isset($filter['used'])) {
                foreach ($rows as $k => $row){
                    if ($filter['used'] && $row->count_products == 0) {
                        unset($rows[$k]);
                    } elseif (!$filter['used'] && $row->count_products > 0) {
                        unset($rows[$k]);
                    }
                }
            }
        }
        return $rows;
    }
    
    /**
    * get All Atribute value
    * @param $resulttype (0 - ObjectList, 1 - array {id->name}, 2 - array(id->object) )
    * 
    * @param mixed $resulttype
    */
    function getAllAttributeValues($resulttype=0, $publish = null){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        if (isset($publish)) {
            $adv_query = " WHERE `publish`=".$db->q($publish);
        } else {
            $adv_query = "";
        }
        $query = "SELECT value_id, image, `".$lang->get("name")."` as name, attr_id, value_ordering, publish FROM `#__jshopping_attr_values` ".$adv_query." ORDER BY value_ordering, value_id";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $attribs = $db->loadObjectList();

        if ($resulttype==2){
            $rows = array();
            foreach($attribs as $k=>$v){
                $rows[$v->value_id] = $v;    
            }
            return $rows;
        }elseif ($resulttype==1){
            $rows = array();
            foreach($attribs as $k=>$v){
                $rows[$v->value_id] = $v->name;    
            }
            return $rows;
        }else{
            return $attribs;
        }        
    }
    
    public function save(array $post, $image = null){
        $jshopConfig = JSFactory::getConfig();
        $dispatcher = Factory::getApplication();        
		$value_id = (int)$post["value_id"];
		$attr_id = (int)$post["attr_id"];
        
        $attributValue = JSFactory::getTable('attributvalue');
        
        $dispatcher->triggerEvent('onBeforeSaveAttributValue', array(&$post));
        
        if ($image){
            $upload = new UploadFile($image);
            $upload->setAllowFile($jshopConfig->allow_image_upload);
            $upload->setCheckImage($jshopConfig->image_upload_check_content);
            $upload->setDir($jshopConfig->image_attributes_path);
            $upload->setFileNameMd5(0);
            $upload->setFilterName(1);
            if ($upload->upload()){
                if ($post['old_image']){
                    @unlink($jshopConfig->image_attributes_path."/".Helper::getSafeFileName($post['old_image']));
                }
                $post['image'] = $upload->getName();
                @chmod($jshopConfig->image_attributes_path."/".$post['image'], 0777);
            }else{
                if ($upload->getError() != 4){
                    JSError::raiseWarning("", Text::_('JSHOP_ERROR_UPLOADING_IMAGE'));
                    Helper::saveToLog("error.log", "SaveAttributeValue - Error upload image. code: ".$upload->getError());
                }
            }
        }
        
        if (!$value_id){
            $post['value_ordering'] = $attributValue->getNextOrder('attr_id='.(int)$attr_id);
        }
        
        $attributValue->bind($post);
                
        if (!$attributValue->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE').' '.$attributValue->getError());
            return 0;
        }
                
        $dispatcher->triggerEvent('onAfterSaveAttributValue', array(&$attributValue));
        
        return $attributValue;
    }
	
	public function delete($id){
		$this->deleteImage($id);
		$this->deleteProductAttributeValue($id);
		$this->deleteAttributeValue($id);		
	}
	
	public function deleteAttributeValue($id){
		$db = Factory::getDBO();
		$query = "DELETE FROM `#__jshopping_attr_values` WHERE `value_id` = '".$db->escape($id)."'";
		$db->setQuery($query);
		$db->execute();
	}
	
	public function deleteImage($id){
		$image = $this->getImage($id);
		if ($image){
			@unlink(JSFactory::getConfig()->image_attributes_path."/".Helper::getSafeFileName($image));
		}
	}
	
	public function getImage($id){
		$db = Factory::getDBO();
		$query = "SELECT image FROM `#__jshopping_attr_values` WHERE value_id ='".$db->escape($id)."'";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	public function deleteProductAttributeValue($id){
		$this->deleteProductAttributeValueDependent($id);
		$this->deleteProductAttributeValueNotDependent($id);
	}
	
	public function deleteProductAttributeValueDependent($id){
		$db = Factory::getDBO();
		$attributValue = JSFactory::getTable('attributValue');
		$attributValue->load($id);
		$attr_id = $attributValue->attr_id;
		if ($attr_id){
			$field = 'attr_'.(int)$attr_id;
			$query = "update `#__jshopping_products_attr` set `".$field."`='' where `".$field."`='".$db->escape($id)."'";
			$db->setQuery($query);
			$db->execute();
		}
	}
	
	public function deleteProductAttributeValueNotDependent($id){
		$db = Factory::getDBO();
		$query = "delete from `#__jshopping_products_attr2` where `attr_value_id` = '".$db->escape($id)."'";
		$db->setQuery($query);
		$db->execute();
	}
    
    public function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();
        $dispatcher = Factory::getApplication();		
        $dispatcher->triggerEvent('onBeforeRemoveAttributValue', array(&$cid));		
		foreach($cid as $value){
            $this->delete(intval($value));
            if ($msg){
                $app->enqueueMessage(Text::_('JSHOP_ATTRIBUT_VALUE_DELETED'), 'message');
            }
		}
        $dispatcher->triggerEvent('onAfterRemoveAttributValue', array(&$cid));
    }
    
    public function deleteFoto($id){
        $jshopConfig = JSFactory::getConfig();
        $attributValue = JSFactory::getTable('attributValue');
        $attributValue->load($id);
        @unlink($jshopConfig->image_attributes_path."/".Helper::getSafeFileName($attributValue->image));
        $attributValue->image = "";
        $attributValue->store();
    }

	public function getProductCount($attr_id, $value_id) {
		$attribut = JSFactory::getTable('attribut');
		$attribut->load($attr_id);
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if($attribut->independent){
			$query->select('COUNT(DISTINCT ' . $db->quoteName('product_id') . ')')
			      ->from($db->quoteName('#__jshopping_products_attr2'))
			      ->where($db->quoteName('attr_id') . ' = ' . $db->quote($attr_id))
			      ->where($db->quoteName('attr_value_id') . ' = ' . $db->quote($value_id));
		}else{
			$field = 'attr_' . $attr_id;
			$query->select('COUNT(DISTINCT ' . $db->quoteName('product_id') . ')')
			      ->from($db->quoteName('#__jshopping_products_attr'))
				->where($db->quoteName($field) . ' = ' . $db->quote($value_id));
		}
		$db->setQuery($query);
		return $db->loadResult();
	}

    public function getListRaw($attr_id, $db_filed_select = '*') {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db_filed_select)
            ->from($db->qn('#__jshopping_attr_values'))
            ->where($db->qn('attr_id') . '=' . $db->q($attr_id))
            ->order('value_id');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getIdByNames($attr_id, $names) {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn('value_id'))
            ->from($db->qn('#__jshopping_attr_values'))
            ->where($db->qn('attr_id')."=".$db->q($attr_id))
            ->order('value_id');
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

    public function clearDoubleValues($attr_id) {
        $list = $this->getListRaw($attr_id);
        $langs = JSFactory::getModel('Languages')->getAllTags();
        $updated = 0;
        foreach($list as $item) {
            $names = [];
            foreach($langs as $lang) {
                $field = 'name_'.$lang;
                $names[$field] = $item->$field;
            }
            $first_id = $this->getIdByNames($attr_id, $names);
            if ($first_id && $first_id != $item->value_id) {
                Helper::saveToLog('attribute_clear_double.log', 'attrid: '.$attr_id.' oldval: '.$item->value_id.' newid: '.$first_id);
                $this->updateAllProductsValue($attr_id, $item->value_id, $first_id);
                $this->deleteAttributeValue($item->value_id);
                $updated++;
            }
        }
        return $updated;
    }

    public function updateAllProductsValue($attr_id, $old_val_id, $new_val_id) {
        $db = Factory::getDBO();
        $field = 'attr_'.(int)$attr_id;
        $columns = $db->getTableColumns('#__jshopping_products_attr');
        if (isset($columns[$field])) {
            $query = $db->getQuery(true);
            $query->update($db->qn('#__jshopping_products_attr'))
                ->set($db->qn($field) . "=" . $db->q($new_val_id))
                ->where($db->qn($field) . '=' . $db->q($old_val_id));
            $db->setQuery($query);
            $db->execute();
        }

        $query = "DELETE a FROM `#__jshopping_products_attr2` AS a
            INNER JOIN `#__jshopping_products_attr2` AS b ON b.product_id=a.product_id AND b.attr_id=a.attr_id AND b.attr_value_id=".(int)$new_val_id."
            WHERE a.attr_id=".(int)$attr_id." AND a.attr_value_id=".(int)$old_val_id;
        $db->setQuery($query);
        $db->execute();

        $query = $db->getQuery(true);
        $query->update($db->qn('#__jshopping_products_attr2'))
            ->set($db->qn('attr_value_id') . "=" . $db->q($new_val_id))
            ->where($db->qn('attr_id') . '=' . $db->q($attr_id))
            ->where($db->qn('attr_value_id') . '=' . $db->q($old_val_id));
        $db->setQuery($query);
        $db->execute();
    }

}