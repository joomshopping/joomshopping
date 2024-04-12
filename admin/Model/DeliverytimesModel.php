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

class DeliveryTimesModel extends BaseadminModel{

    function getDeliveryTimes($order = null, $orderDir = null){
        $db = \JFactory::getDBO();    
        $lang = \JSFactory::getLang();    
        
        $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT id, `".$lang->get('name')."` as name FROM `#__jshopping_delivery_times` ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getCountDeliveryTimes() {
        $db = \JFactory::getDBO();         
        $query = "SELECT count(id) FROM `#__jshopping_delivery_times`";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    public function save(array $post){
        $deliveryTimes = \JSFactory::getTable('deliveryTimes');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveDeliveryTime', array(&$post));        
		$deliveryTimes->bind($post);
		if (!$deliveryTimes->store()){
			$this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
			return 0;
		}
        $dispatcher->triggerEvent('onAfterSaveDeliveryTime', array(&$deliveryTimes));
        return $deliveryTimes;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = \JFactory::getDBO();
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveDeliveryTime', array(&$cid));
        $res = array();
		foreach($cid as $id){
			$query = "DELETE FROM `#__jshopping_delivery_times` WHERE `id` = ".(int)$id;
			$db->setQuery($query);
			if ($db->execute()){
                $res[$id] = true;
                if ($msg){
                    $app->enqueueMessage(\JText::_('JSHOP_DELIVERY_TIME_DELETED'), 'message');
                }
            }else{
                $res[$id] = false;
                if ($msg){
                    $app->enqueueMessage(\JText::_('JSHOP_DELIVERY_TIME_DELETED_ERROR_DELETED'), 'error');
                }
            }
		}
        $dispatcher->triggerEvent('onAfterRemoveDeliveryTime', array(&$cid));
        return $res;
    }
    
}