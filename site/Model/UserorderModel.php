<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class UserOrderModel  extends BaseModel{

	private $user_id = 0;
	private $order_id = 0;
	
	public function setUserId($id){
		$this->user_id = $id;
	}
	
	public function getUserId(){
		return $this->user_id;
	}
	
	public function setOrderId($id){
		$this->order_id = $id;
	}
	
	public function getOrderId(){
		return $this->order_id;
	}
	
	public function userOrderCancel(){
		$jshopConfig = \JSFactory::getConfig();
		if (!$jshopConfig->client_allow_cancel_order){
			$this->setError('Cancel order disabled');
			return 0;
		}
		$order = \JSFactory::getTable('order');
        $order->load($this->order_id);
        
        if ($this->user_id!=$order->user_id){
            $this->setError("Error order number");
			return 0;
        }
        $status = $jshopConfig->payment_status_for_cancel_client;
		
		if ($order->order_status==$status || in_array($order->order_status, $jshopConfig->payment_status_disable_cancel_client)){
            return 0;
        }
		
		$checkout = \JSFactory::getModel('checkout', 'Site');
        $checkout->changeStatusOrder($this->order_id, $status, 1);
		$obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterUserCancelOrder', array(&$this->order_id, &$status, &$obj));
		return 1;
	}

}