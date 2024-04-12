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

class AttributesGroupsModel extends BaseadminModel{
    
    protected $nameTable = 'attributesgroup';

    public function getList(){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang(); 
        $query = "SELECT id, `".$lang->get("name")."` as name, ordering FROM `#__jshopping_attr_groups` order by ordering";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    public function save(array $post){
        $row = \JSFactory::getTable('attributesgroup');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveAttributesGroups', array(&$post));
        $row->bind($post);
        if (!$post['id']){
            $row->ordering = null;
            $row->ordering = $row->getNextOrder();
        }        
        $row->store();
        $dispatcher->triggerEvent('onAfterSaveAttributesGroups', array(&$row));
        return $row;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        foreach($cid as $id){
            $this->delete($id);
        }
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onAfterRemoveAttributesGroups', array(&$cid));
        if ($msg){
            $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
        }
    }
    
    public function delete($id){
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_attr_groups` WHERE `id`=".(int)$id;
        $db->setQuery($query);
        $db->execute();
    }
}