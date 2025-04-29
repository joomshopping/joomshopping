<?php
/**
* @version      5.6.1 15.09.2018
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

class DeliveryTimesModel extends BaseadminModel{

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getDeliveryTimes($orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters);
	}

	public function getCountItems(array $filters = [], array $params = []) {
		return $this->getCountDeliveryTimes();
	}

    function getDeliveryTimes($order = null, $orderDir = null, $filter = []){
        $db = Factory::getDBO();    
        $lang = JSFactory::getLang();    
        
        $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $where = '';        
        if (isset($filter['text_search'])) {
            $where .= " AND (`".$lang->get("name")."` LIKE ".$db->q('%'.$filter['text_search'].'%').")";
        }
        $query = "SELECT id, `".$lang->get('name')."` as name 
        FROM `#__jshopping_delivery_times` 
        WHERE 1 ".$where."
        ORDER BY ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getCountDeliveryTimes() {
        $db = Factory::getDBO();         
        $query = "SELECT count(id) FROM `#__jshopping_delivery_times`";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    public function save(array $post){
        $deliveryTimes = JSFactory::getTable('deliveryTimes');
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveDeliveryTime', array(&$post));
        $post['days'] = isset($post['days']) ? (float)$post['days'] : null;
		$deliveryTimes->bind($post);
		if (!$deliveryTimes->store()){
			$this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE')." ".$deliveryTimes->getError());
			return 0;
		}
        $dispatcher->triggerEvent('onAfterSaveDeliveryTime', array(&$deliveryTimes));
        return $deliveryTimes;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = Factory::getDBO();
        $app = Factory::getApplication();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveDeliveryTime', array(&$cid));
        $res = array();
		foreach($cid as $id){
			$query = "DELETE FROM `#__jshopping_delivery_times` WHERE `id` = ".(int)$id;
			$db->setQuery($query);
			if ($db->execute()){
                $res[$id] = true;
                if ($msg){
                    $app->enqueueMessage(Text::_('JSHOP_DELIVERY_TIME_DELETED'), 'message');
                }
            }else{
                $res[$id] = false;
                if ($msg){
                    $app->enqueueMessage(Text::_('JSHOP_DELIVERY_TIME_DELETED_ERROR_DELETED'), 'error');
                }
            }
		}
        $dispatcher->triggerEvent('onAfterRemoveDeliveryTime', array(&$cid));
        return $res;
    }
    
}