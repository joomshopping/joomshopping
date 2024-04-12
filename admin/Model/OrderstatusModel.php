<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class OrderstatusModel extends BaseadminModel{
	
	public function save(array $post){
        $order_status = \JSFactory::getTable('orderStatus');        
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveOrderStatus', array(&$post));
		$order_status->bind($post);
		if (!$order_status->store()){
			$this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE').' '.$order_status->getError());
			return 0;
		}
        $dispatcher->triggerEvent('onAfterSaveOrderStatus', array(&$order_status));
        return $order_status;
	}
    
    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveOrderStatus', array(&$cid));
        $res = array();
		foreach($cid as $id){
			$this->delete($id);
            $res[$id] = true;
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
            }
		}
        $dispatcher->triggerEvent('onAfterRemoveOrderStatus', array(&$cid));
        return $res;
    }
    
    public function delete($id){
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_order_status` WHERE `status_id`=".(int)$id;
        $db->setQuery($query);
        return $db->execute();
    }

}