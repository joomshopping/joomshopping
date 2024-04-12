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

class PaymentsModel extends BaseadminModel{
    
    protected $nameTable = 'paymentMethod';
    protected $tableFieldOrdering = 'payment_ordering';
    
    function getAllPaymentMethods($publish = 1, $order = null, $orderDir = null) {
        $database = \JFactory::getDBO(); 
        $query_where = ($publish)?("WHERE payment_publish = '1'"):("");
        $lang = \JSFactory::getLang();
        $ordering = 'payment_ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT payment_id, `".$lang->get("name")."` as name, `".$lang->get("description")."` as description , payment_code, payment_class, scriptname, payment_publish, payment_ordering, payment_params, payment_type FROM `#__jshopping_payment_method`
                  $query_where
                  ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $database->setQuery($query);
        return $database->loadObjectList();
    }
    
    function getTypes(){
    	return array('1' => \JText::_('JSHOP_TYPE_DEFAULT'),'2' => \JText::_('JSHOP_PAYPAL_RELATED'));
    }
    
    function getMaxOrdering(){
        $db = \JFactory::getDBO();
        $query = "select max(payment_ordering) from `#__jshopping_payment_method`";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
	function getListNamePaymens($publish = 1){
        $_list = $this->getAllPaymentMethods($publish);
        $list = array();
        foreach($_list as $v){
            $list[$v->payment_id] = $v->name;
        }
        return $list;
    }
    
    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        if (!isset($post['payment_publish'])){
            $post['payment_publish'] = 0;
        }
        if (!isset($post['show_descr_in_email'])){
            $post['show_descr_in_email'] = 0;
        }
        $post['price'] = \JSHelper::saveAsPrice($post['price']);
        $post['payment_class'] = $input->getCmd("payment_class");
        if (!$post['payment_id'] && !$post['payment_type']){
            $post['payment_type'] = 1;
        }        
        $languages = \JSFactory::getModel("languages")->getAllLanguages(1);
        foreach($languages as $lang){
            $post['description_'.$lang->language] = $input->get('description'.$lang->id, '', 'RAW');
        }
        return $post;
    }
    
    public function save(array $post){
        $dispatcher = \JFactory::getApplication();
        $payment = \JSFactory::getTable('paymentMethod');
        $dispatcher->triggerEvent('onBeforeSavePayment', array(&$post));
		$payment->bind($post);
        if (!$payment->payment_id){
            $payment->payment_ordering = $this->getMaxOrdering() + 1;
        }
        $payment->setConfigs($post['pm_params']);		
        if (!$payment->check()){
            print $payment->getError();
            $this->setError($payment->getError());            
            return 0;
        }
		if (!$payment->store()){
            print $payment->getError();die();
        }
        $dispatcher->triggerEvent('onAfterSavePayment', array(&$payment));        
        return $payment;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemovePayment', array(&$cid));
		foreach($cid as $id){
            $this->delete($id);
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
            }
		}
        $dispatcher->triggerEvent('onAfterRemovePayment', array(&$cid));
    }
    
    public function delete($id){
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_payment_method` WHERE `payment_id`=".(int)$id;
        $db->setQuery($query);
        return $db->execute();
    }
    
    public function publish(array $cid, $flag){
        $db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishPayment', array(&$cid, &$flag));
		foreach($cid as $value){
			$query = "UPDATE `#__jshopping_payment_method`
					   SET `payment_publish` = '" . $db->escape($flag) . "'
					   WHERE `payment_id` = '" . $db->escape($value) . "'";
			$db->setQuery($query);
			$db->execute();
		}        
        $dispatcher->triggerEvent('onAfterPublishPayment', array(&$cid, &$flag));
    }
    
}