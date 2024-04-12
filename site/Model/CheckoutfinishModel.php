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

class CheckoutFinishModel  extends CheckoutModel{

	public function getFinishStaticText(){
		$statictext = \JSFactory::getTable("statictext");
        $rowstatictext = $statictext->loadData("order_finish_descr");
        $text = $rowstatictext->text;
		if (trim(strip_tags($text))==""){
            $text = '';
        }
		return $text;
	}
	
	public function paymentComplete($order_id, $text = ''){
		$order = \JSFactory::getTable('order');
		$order->load($order_id);
		$pm_method = $order->getPayment();
		$paymentsysdata = $pm_method->getPaymentSystemData();
		$payment_system = $paymentsysdata->paymentSystem;
		if ($payment_system){
			$pmconfigs = $pm_method->getConfigs();
			$payment_system->complete($pmconfigs, $order, $pm_method);
		}
		\JFactory::getApplication()->triggerEvent('onAfterDisplayCheckoutFinish', array(&$text, &$order, &$pm_method));
	}
	
	public function clearAllDataCheckout(){
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "before"));
		$cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();
        $cart->getSum();
        $cart->clear();
        $this->deleteSession();
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
	}
	
}