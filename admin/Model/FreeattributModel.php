<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class FreeAttributModel extends BaseadminModel{
    
    function getNameAttrib($id) {
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_free_attr` WHERE id = '".$db->escape($id)."'";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAll($order = null, $orderDir = null) {
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO(); 
        $ordering = 'ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT id, `".$lang->get("name")."` as name, ordering, required FROM `#__jshopping_free_attr` ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);        
        return $db->loadObjectList();
    }
    
    public function save(array $post){
        $attribut = \JSFactory::getTable('freeattribut');    
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveFreeAtribut', array(&$post));
        if (!$post['id']){
            $attribut->ordering = null;
            $attribut->ordering = $attribut->getNextOrder();            
        }
        $attribut->bind($post);
        if (!$attribut->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE').' '.$attribut->getError());
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveFreeAtribut', array(&$attribut));
        return $attribut;
    }
    
    public function deleteList(array $cid = array(), $msg = 1){
        $app = \JFactory::getApplication();
        $db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveFreeAtribut', array(&$cid));
		foreach($cid as $id){            
			$query = "DELETE FROM `#__jshopping_free_attr` WHERE `id` = ".(int)$id;
			$db->setQuery($query);
			$db->execute();
            
            $query = "delete from `#__jshopping_products_free_attr` where `attr_id` = ".(int)$id;
            $db->setQuery($query);
            $db->execute();
		}
        if ($msg){
            $app->enqueueMessage(\JText::_('JSHOP_ATTRIBUT_DELETED'), 'message');
        }
        $dispatcher->triggerEvent('onAfterRemoveFreeAtribut', array(&$cid));
    }
    
}
