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

class UserOrdersModel{

	private $user_id = 0 ;
	private $list = array();
	
	public function setUserId($id){
		$this->user_id = $id;
	}
	
	public function getUserId(){
		return $this->user_id;
	}
	
	public function getListOrders(){
		$order = \JSFactory::getTable('order');
		$this->list = $order->getOrdersForUser($this->user_id);
		$this->loadOrderLink();
		foreach ($this->list as $k => $v) {
			$this->list[$k]->_tmp_ext_order_number = "";
			$this->list[$k]->_tmp_ext_status_name = "";
			$this->list[$k]->_tmp_ext_user_info = "";
			$this->list[$k]->_tmp_ext_prod_info = "";
			$this->list[$k]->_tmp_ext_but_info = "";
			$this->list[$k]->_tmp_ext_row_end = "";
			$this->list[$k]->_ext_price_html = "";
		}
		return $this->list;
	}
	
	public function getTotal(){
		$total = 0;
        foreach($this->list as $key=>$value){
            $total += $value->order_total / $value->currency_exchange;
        }
		return $total;
	}
	
	private function loadOrderLink(){
		$jshopConfig = \JSFactory::getConfig();
		foreach($this->list as $key=>$value){
            $this->list[$key]->order_href = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=user&task=order&order_id='.$value->order_id,1,0,$jshopConfig->use_ssl);
        }
	}
	
	
}