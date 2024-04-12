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

class AttributModel extends BaseadminModel{
    
    protected $tableFieldOrdering = 'attr_ordering';
    
    public function getNameAttribut($attr_id) {
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_attr` WHERE attr_id = '".$db->escape($attr_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    public function getAllAttributes($result = 0, $categorys = null, $order = null, $orderDir = null){
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        $ordering = "A.attr_ordering asc";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT A.attr_id, A.`".$lang->get("name")."` as name, A.attr_type, A.attr_ordering, A.independent, A.allcats, A.cats, G.`".$lang->get("name")."` as groupname
                  FROM `#__jshopping_attr` as A left join `#__jshopping_attr_groups` as G on A.`group`=G.id
                  ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $list = $db->loadObjectList();
                
        if (is_array($categorys) && count($categorys)){
            foreach($list as $k=>$v){
                if (!$v->allcats){
                    if ($v->cats!=""){
                        $cats = unserialize($v->cats);
                    }else{
                        $cats = array();
                    }
                    $enable = 0;
                    foreach($categorys as $cid){
                        if (in_array($cid, $cats)) $enable = 1;
                    }
                    if (!$enable){
                        unset($list[$k]);
                    }
                }
            } 
        }
        
        if ($result==0){
            return $list;
        }
        if ($result==1){
            $attributes_format1 = array();
            foreach($list as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($result==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($list as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    }
    
    function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        foreach($languages as $lang){
            $post['description_'.$lang->language] = $input->get('description_'.$lang->language, '', 'RAW');
        }
        return $post;
    }
    
    public function save(array $post){
		$attr_id = $post['attr_id'];
        $dispatcher = \JFactory::getApplication();
        $attribut = \JSFactory::getTable('attribut');
        $dispatcher->triggerEvent('onBeforeSaveAttribut', array(&$post));
        if (!$attr_id){
            $post['attr_ordering'] = $attribut->getNextOrder();
        }
        $attribut->bind($post);
        if (isset($post['category_id'])){ 
            $categorys = $post['category_id'];
        }else{
            $categorys = array();
        }
        if (!is_array($categorys)){
            $categorys = array();
        }
        $attribut->setCategorys($categorys);
        if (!$attribut->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0;
        }        
        if (!$attr_id){
            $attribut->addNewFieldProductsAttr();
        }        
        $dispatcher->triggerEvent('onAfterSaveAttribut', array(&$attribut));        
        return $attribut;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();	
        $dispatcher->triggerEvent('onBeforeRemoveAttribut', array(&$cid));
		foreach($cid as $value){
			$this->delete(intval($value));
		}
        $dispatcher->triggerEvent('onAfterRemoveAttribut', array(&$cid));
        if ($msg){
            $app->enqueueMessage(\JText::_('JSHOP_ATTRIBUT_DELETED'), 'message');
        }
    }
	
	public function delete($id){
		$this->deleteAttribute($id);
		$this->deleteAttributeValues($id);
		$this->deleteProductAttribute($id);
	}
	
	public function deleteAttribute($id){
		$db = \JFactory::getDBO();		
		$query = "DELETE FROM `#__jshopping_attr` WHERE `attr_id`='".$db->escape($id)."'";
		$db->setQuery($query);
		$db->execute();
	}
	
	public function deleteAttributeValues($id){
		$db = \JFactory::getDBO();
		
		$attr_values = $this->getListAttributeValues($id);
		foreach($attr_values as $attr_val){
			if ($attr_val->image){
				@unlink(\JSFactory::getConfig()->image_attributes_path."/".$attr_val->image);
			}
		}
		
		$query = "delete from `#__jshopping_attr_values` where `attr_id` = '".$db->escape($id)."' ";
		$db->setQuery($query);
		$db->execute();
	}
	
	public function getListAttributeValues($id){
		$db = \JFactory::getDBO();
		$query = "select * from `#__jshopping_attr_values` where `attr_id` = '".$db->escape($id)."' ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function deleteProductAttribute($id){
		$this->deleteProductAttributeDependent($id);
		$this->deleteProductAttributeNotDependent($id);
	}
	
	public function deleteProductAttributeDependent($id){
		$db = \JFactory::getDBO();
		$query="ALTER TABLE `#__jshopping_products_attr` DROP `attr_".(int)$id."`";
		$db->setQuery($query);
		$db->execute();
	}
	
	public function deleteProductAttributeNotDependent($id){
		$db = \JFactory::getDBO();
		$query = "delete from `#__jshopping_products_attr2` where `attr_id` = '".$db->escape($id)."' ";
		$db->setQuery($query);
		$db->execute();
	}
    
}