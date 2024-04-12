<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\Component\Jshopping\Site\Lib\UploadFile;
defined('_JEXEC') or die();

class AttributValueModel extends BaseadminModel{
    
    protected $tableFieldOrdering = 'value_ordering';
    
    function getNameValue($value_id) {
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_attr_values` WHERE value_id = '".$db->escape($value_id)."'";
        $db->setQuery($query);        
        return $db->loadResult();
    }

    function getAllValues($attr_id, $order = null, $orderDir = null) {
        $db = \JFactory::getDBO(); 
        $lang = \JSFactory::getLang();
        $ordering = 'value_ordering, value_id';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT value_id, image, `".$lang->get("name")."` as name, attr_id, value_ordering FROM `#__jshopping_attr_values` where attr_id='".$attr_id."' ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    /**
    * get All Atribute value
    * @param $resulttype (0 - ObjectList, 1 - array {id->name}, 2 - array(id->object) )
    * 
    * @param mixed $resulttype
    */
    function getAllAttributeValues($resulttype=0){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT value_id, image, `".$lang->get("name")."` as name, attr_id, value_ordering FROM `#__jshopping_attr_values` ORDER BY value_ordering, value_id";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
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
        $jshopConfig = \JSFactory::getConfig();
        $dispatcher = \JFactory::getApplication();        
		$value_id = (int)$post["value_id"];
		$attr_id = (int)$post["attr_id"];
        
        $attributValue = \JSFactory::getTable('attributvalue');
        
        $dispatcher->triggerEvent('onBeforeSaveAttributValue', array(&$post));
        
        if ($image){
            $upload = new UploadFile($image);
            $upload->setAllowFile($jshopConfig->allow_image_upload);
            $upload->setDir($jshopConfig->image_attributes_path);
            $upload->setFileNameMd5(0);
            $upload->setFilterName(1);
            if ($upload->upload()){
                if ($post['old_image']){
                    @unlink($jshopConfig->image_attributes_path."/".$post['old_image']);
                }
                $post['image'] = $upload->getName();
                @chmod($jshopConfig->image_attributes_path."/".$post['image'], 0777);
            }else{
                if ($upload->getError() != 4){
                    \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING_IMAGE'));
                    \JSHelper::saveToLog("error.log", "SaveAttributeValue - Error upload image. code: ".$upload->getError());
                }
            }
        }
        
        if (!$value_id){
            $post['value_ordering'] = $attributValue->getNextOrder('attr_id='.(int)$attr_id);
        }
        
        $attributValue->bind($post);
                
        if (!$attributValue->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE').' '.$attributValue->getError());
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
		$db = \JFactory::getDBO();
		$query = "DELETE FROM `#__jshopping_attr_values` WHERE `value_id` = '".$db->escape($id)."'";
		$db->setQuery($query);
		$db->execute();
	}
	
	public function deleteImage($id){
		$image = $this->getImage($id);
		if ($image){
			@unlink(\JSFactory::getConfig()->image_attributes_path."/".$image);
		}
	}
	
	public function getImage($id){
		$db = \JFactory::getDBO();
		$query = "SELECT image FROM `#__jshopping_attr_values` WHERE value_id ='".$db->escape($id)."'";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	public function deleteProductAttributeValue($id){
		$this->deleteProductAttributeValueDependent($id);
		$this->deleteProductAttributeValueNotDependent($id);
	}
	
	public function deleteProductAttributeValueDependent($id){
		$db = \JFactory::getDBO();
		$attributValue = \JSFactory::getTable('attributValue');
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
		$db = \JFactory::getDBO();
		$query = "delete from `#__jshopping_products_attr2` where `attr_value_id` = '".$db->escape($id)."'";
		$db->setQuery($query);
		$db->execute();
	}
    
    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();		
        $dispatcher->triggerEvent('onBeforeRemoveAttributValue', array(&$cid));		
		foreach($cid as $value){
            $this->delete(intval($value));
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ATTRIBUT_VALUE_DELETED'), 'message');
            }
		}
        $dispatcher->triggerEvent('onAfterRemoveAttributValue', array(&$cid));
    }
    
    public function deleteFoto($id){
        $jshopConfig = \JSFactory::getConfig();
        $attributValue = \JSFactory::getTable('attributValue');
        $attributValue->load($id);
        @unlink($jshopConfig->image_attributes_path."/".$attributValue->image);
        $attributValue->image = "";
        $attributValue->store();
    }
}