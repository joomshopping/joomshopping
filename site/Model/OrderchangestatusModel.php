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

class OrderChangeStatusModel  extends BaseModel{
	
	protected $order_id;	
	protected $status;	
	protected $sendmessage;	
	protected $appadmin = 0;
	protected $status_id;
	protected $notify;
	protected $include_comment;
	protected $view_order;	
	protected $comments;
	private $order;
	private $vendorinfo;
	private $tbl_order_status;
	private $order_details_url;	
	private $listVendors;
	private $vendors_send_message;
	private $vendor_send_order;
	private $admin_send_order;
	
	public function __construct(){
		$this->order = \JSFactory::getTable('order');
		$this->tbl_order_status = \JSFactory::getTable('orderStatus');
	}
	
	public function setData($order_id, $status, $sendmessage = 1, $status_id = 0, $notify = 1, $comments = '', $include_comment = 0, $view_order = 0){
		$this->order_id = $order_id;
		$this->status = $status;
		$this->sendmessage = $sendmessage;
		$this->status_id = $status_id;
		$this->notify = $notify;
		$this->comments = $comments;
		$this->include_comment = $include_comment;
		$this->view_order = $view_order;
	}
	
	public function setAppAdmin($val){
		$this->appadmin = $val;
	}
	
	public function getAppAdmin(){
		return $this->appadmin;
	}
	
	
	public function store(){
        $jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();

		if ($this->getAppAdmin()){
			$dispatcher->triggerEvent(
				'onBeforeChangeOrderStatusAdmin', 
				array(
					&$this->order_id, 
					&$this->status, 
					&$this->status_id, 
					&$this->notify, 
					&$this->comments, 
					&$this->include_comment, 
					&$this->view_order
				)
			);
		}else{
			$dispatcher->triggerEvent(
				'onBeforeChangeOrderStatus', 
				array(
					&$this->order_id, 
					&$this->status, 
					&$this->sendmessage, 
					&$this->comments
				)
			);
		}

		$prev_order_status = $this->orderStatusStore();
		
		if ($this->getAppAdmin()){
			\JSFactory::loadLanguageFile($this->order->getLang());
			\JSFactory::getLang($this->order->getLang());
		}
        
        $this->vendorinfo = $this->order->getVendorInfo();

        $this->tbl_order_status->load($this->status);
        
		$this->order->updateProductsInStock();
        
		$this->order->saveOrderHistory($this->notify, $this->comments);
		
		$this->order_details_url = $this->getOrderDetailsUrl();
		
		if (!$this->include_comment){
			$this->comments = '';
		}
        
        $message = $this->getMessage(
			$this->order, 
			$this->tbl_order_status->getName(), 
			$this->vendorinfo, 
			$this->order_details_url, 
			$this->comments
		);

		$this->listVendors = $this->getListVendors($this->order);
		$this->vendors_send_message = $this->getVendorSendMessage($this->order);
		$this->vendor_send_order = $this->getVendorSendOrder($this->order);
		$this->admin_send_order = $this->getAdminSendOrder($this->order, $this->listVendors);

		$send_msg_client = $this->getSendMsg('client');
		$send_msg_admin = $this->getSendMsg('admin');
		$send_msg_vendor = $this->getSendMsg('vendor');
		
        if ($send_msg_client){			
            $this->sendMail('client', $this->order->email, $message, $this->order);
		}

		if ($send_msg_admin){
			$this->sendMail('admin', $jshopConfig->getAdminContactEmails(), $message, $this->order);
		}

		if ($send_msg_vendor){
			foreach($this->listVendors as $k=>$datavendor){
				$this->sendMail('vendor', $datavendor->email, $message, $this->order, $datavendor);
			}
		}

		if ($this->getAppAdmin()){
			$dispatcher->triggerEvent(
				'onAfterChangeOrderStatusAdmin', 
				array(
					&$this->order_id, 
					&$this->status, 
					&$this->status_id, 
					&$this->notify, 
					&$this->comments, 
					&$this->include_comment, 
					&$this->view_order, 
					&$prev_order_status
				)
			);
		}else{
			$dispatcher->triggerEvent(
				'onAfterChangeOrderStatus', 
				array(
					&$this->order_id, 
					&$this->status, 
					&$this->sendmessage, 
					&$prev_order_status
				)
			);
		}
		return 1;
	}
	
	public function getSubjectMail($type, $order){
		if ($type=='admin'){
			$subject = \JText::_('JSHOP_ORDER_STATUS_CHANGE_TITLE');
		}else{
			$subject = sprintf(\JText::_('JSHOP_ORDER_STATUS_CHANGE_SUBJECT'), $order->order_number);
		}
		return $subject;
	}
	
	public function getMessage($order, $newstatus, $vendorinfo, $order_details_url, $comments = ''){        
        $view = $this->getView('order');
        $view->setLayout("statusorder");
        $view->set('order', $order);
        $view->set('order_status', $newstatus);
        $view->set('vendorinfo', $vendorinfo);
        $view->set('order_detail', $order_details_url);
        $view->set('comment', $comments);
        \JFactory::getApplication()->triggerEvent('onBeforeCreateMailOrderStatusView', array(&$view));
    return $view->loadTemplate();
    }
	
	public function sendMail($type, $recipient, $message, $order, $datavendor = null){
		$app = \JFactory::getApplication();
		$jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();
		
		$mailfrom = $app->getCfg('mailfrom');
        $fromname = $app->getCfg('fromname');
		
		$ishtml = $jshopConfig->orderchangestatus_email_html;
		$subject = $this->getSubjectMail($type, $order);
		
		$dispatcher->triggerEvent(
			$this->getSendMailTriggerTypeBefore($type), 
			array(
				&$message, 
				&$order, 
				&$this->comments, 
				&$this->tbl_order_status, 
				&$this->vendorinfo, 
				&$this->order_details_url, 
				&$ishtml, 
				&$mailfrom, 
				&$fromname, 
				&$subject, 
				&$datavendor
			)
		);
		
		$mailer = \JFactory::getMailer();
		$mailer->setSender(array($mailfrom, $fromname));
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->setBody($message);
		$mailer->isHTML($ishtml);
		$dispatcher->triggerEvent(
			$this->getSendMailTriggerTypeAfter($type), 
			array(
				&$mailer, 
				&$this->order_id, 
				&$this->status, 
				&$this->sendmessage, 
				&$order)
		);
		return $mailer->Send();
	}
	
	private function getOrderDetailsUrl(){
		if ($this->order->user_id==-1){
			$url = '';
		}else{
			$url = \JSHelper::getFullUrlSefLink('index.php?option=com_jshopping&controller=user&task=order&order_id='.$this->order_id, 1);
		}
		return $url;
	}
	
	private function orderStatusStore(){
		$this->order->load($this->order_id);		
		$prev_status = $this->order->order_status;
        $this->order->order_status = $this->status;
        $this->order->order_m_date = \JSHelper::getJsDate();
        $this->order->store();
		return $prev_status;
	}
	
	private function getSendMsg($type){
		$jshopConfig = \JSFactory::getConfig();
		if (!$this->getAppAdmin()){
			if ($type=='client'){
				$send = $this->sendmessage;
			}
			if ($type=='admin'){
				$send = $this->sendmessage && $this->admin_send_order;
			}
			if ($type=='vendor'){
				$send = $this->sendmessage && ($this->vendors_send_message || $this->vendor_send_order);
			}
		}else{
			if ($type=='client'){
				$send = $this->notify;
			}
			if ($type=='admin'){
				$send = $jshopConfig->send_admin_mail_order_status_appadmin;
			}
			if ($type=='vendor'){
				$send = ($this->vendors_send_message || $this->vendor_send_order);
			}
		}
		return $send;
	}
	
	protected function getSendMailTriggerTypeBefore($type){
		if ($type=="client"){
			$trigger = 'onBeforeSendClientMailOrderStatus';
		}
		if ($type=="admin"){
			$trigger = 'onBeforeSendAdminMailOrderStatus';
		}
		if ($type=="vendor"){
			$trigger = 'onBeforeSendVendorMailOrderStatus';
		}
		return $trigger;
	}
	
	protected function getSendMailTriggerTypeAfter($type){
		if ($type=="client"){
			$trigger = 'onBeforeSendMailChangeOrderStatusClient';
		}
		if ($type=="admin"){
			$trigger = 'onBeforeSendMailChangeOrderStatusAdmin';
		}
		if ($type=="vendor"){
			$trigger = 'onBeforeSendMailChangeOrderStatusVendor';
		}
		return $trigger;
	}
	
	protected function getListVendors($order){
		if (\JSFactory::getConfig()->admin_show_vendors){
            $listVendors = $order->getVendors();
        }else{
            $listVendors = array();
        }
		return $listVendors;
	}
	
	protected function getVendorSendMessage($order){
		$jshopConfig = \JSFactory::getConfig();
		return ($jshopConfig->vendor_order_message_type==1 || ($order->vendor_type==1 && $jshopConfig->vendor_order_message_type==2));
	}
	
	protected function getVendorSendOrder($order){
		$jshopConfig = \JSFactory::getConfig();
		$vendor_send_order = ($jshopConfig->vendor_order_message_type==2 && $order->vendor_type == 0 && $order->vendor_id);
        if ($jshopConfig->vendor_order_message_type==3){
			$vendor_send_order = 1;
		}
		return $vendor_send_order;
	}
	
	protected function getAdminSendOrder($order, $listVendors){		
		$admin_send_order = 1;		
        if (\JSFactory::getConfig()->admin_not_send_email_order_vendor_order && $this->getVendorSendOrder($order) && count($listVendors)){
			$admin_send_order = 0;
		}
		return $admin_send_order;
	}
	
}