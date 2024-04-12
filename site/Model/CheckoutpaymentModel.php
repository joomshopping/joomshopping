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

class CheckoutPaymentModel  extends CheckoutModel{
	
	private $active_paym_method;
    
	public function getCheckoutListPayments(){
		$jshopConfig = \JSFactory::getConfig();
		$cart = $this->getCart();
		$paymentmethod = \JSFactory::getTable('paymentmethod');
		$shipping_id = $cart->getShippingId();
        $all_payment_methods = $paymentmethod->getAllPaymentMethods(1, $shipping_id);
		$i = 0;
        $paym = array();
		foreach($all_payment_methods as $pm){
            $paym[$i] = new \stdClass();
            if ($pm->scriptname!=''){
                $scriptname = $pm->scriptname;    
            }else{
                $scriptname = $pm->payment_class;   
            }
            $paymentmethod->load($pm->payment_id); 
            $paymentsysdata = $paymentmethod->getPaymentSystemData($scriptname);
            if ($paymentsysdata->paymentSystem){
                $paym[$i]->existentcheckform = 1;
				$paym[$i]->payment_system = $paymentsysdata->paymentSystem;
            }else{
                $paym[$i]->existentcheckform = 0;
            }
            
            $paym[$i]->name = $pm->name;
            $paym[$i]->payment_id = $pm->payment_id;
            $paym[$i]->payment_class = $pm->payment_class;
            $paym[$i]->scriptname = $pm->scriptname;
            $paym[$i]->payment_description = $pm->description;
            $paym[$i]->price_type = $pm->price_type;
            $paym[$i]->image = $pm->image;
            $paym[$i]->price_add_text = '';
            if ($pm->price_type==2){
                $paym[$i]->calculeprice = $pm->price;
                if ($paym[$i]->calculeprice!=0){
                    if ($paym[$i]->calculeprice>0){
                        $paym[$i]->price_add_text = '+'.$paym[$i]->calculeprice.'%';
                    }else{
                        $paym[$i]->price_add_text = $paym[$i]->calculeprice.'%';
                    }
                }
            }else{
                $paym[$i]->calculeprice = \JSHelper::getPriceCalcParamsTax($pm->price * $jshopConfig->currency_value, $pm->tax_id, $cart->products);
                if ($paym[$i]->calculeprice!=0){
                    if ($paym[$i]->calculeprice>0){
                        $paym[$i]->price_add_text = '+'.\JSHelper::formatprice($paym[$i]->calculeprice);
                    }else{
                        $paym[$i]->price_add_text = \JSHelper::formatprice($paym[$i]->calculeprice);
                    }
                }
            }
            
            $s_payment_method_id = $cart->getPaymentId();
            if ($s_payment_method_id == $pm->payment_id){
                $params = $cart->getPaymentParams();
            }else{
                $params = array();
            }

            if ($paym[$i]->existentcheckform){
                $paym[$i]->form = $paymentmethod->loadPaymentForm($paym[$i]->payment_system, $params, $pm->pmconfig);
            }else{
                $paym[$i]->form = "";
            }
            
            $i++;
        }
		return $paym;
	}
	
	public function getCheckoutActivePayment(&$paym, &$adv_user){
		$cart = $this->getCart();
		
		$pm_id = $cart->getPaymentId();
        $active_payment = intval($pm_id);

        if (!$active_payment){
            $list_payment_id = array();
            foreach($paym as $v){
                $list_payment_id[] = $v->payment_id;
            }
            if (in_array($adv_user->payment_id, $list_payment_id)){
				$active_payment = $adv_user->payment_id;
			}
        }
        
        if (!$active_payment){
            if (isset($paym[0])){
                $active_payment = $paym[0]->payment_id;
            }
        }
		return $active_payment;
	}
	
	public function getCheckoutFirstPaymentClass(&$paym){
		return $paym[0]->payment_class;
	}
	
	public function savePaymentData($payment_method, &$params, &$adv_user){
		if (isset($params[$payment_method])){
            $params_pm = $params[$payment_method];
        }else{
            $params_pm = '';
        }
		$cart = $this->getCart();
		$paym_method = \JSFactory::getTable('paymentmethod');
        $paym_method->class = $payment_method;
        $payment_method_id = $paym_method->getId();
        $paym_method->load($payment_method_id);
        $pmconfigs = $paym_method->getConfigs();
        $paymentsysdata = $paym_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        if ($paym_method->payment_publish==0){
            $cart->setPaymentParams('');
			$this->setError(\JText::_('JSHOP_ERROR_PAYMENT'));
            return 0;
        }
        if ($payment_system){
            if (!$payment_system->checkPaymentInfo($params_pm, $pmconfigs)){
                $cart->setPaymentParams('');
				$this->setError($payment_system->getErrorMessage());                
                return 0;
            }            
        }
		
		$paym_method->setCart($cart);
        $cart->setPaymentId($payment_method_id);
        if (\JSFactory::getConfig()->step_4_3) {
            $cart->setDisplayItem(1, 1);
        }
        $price = $paym_method->getPrice();
        $cart->setPaymentDatas($price, $paym_method);
        
        if (isset($params[$payment_method])) {
            $cart->setPaymentParams($params_pm);
        } else {
            $cart->setPaymentParams('');
        }
        
        $adv_user->saveTypePayment($payment_method_id);
		
		$this->setActivePaymMethod($paym_method);
		
		return 1;
	}
	
	public function setActivePaymMethod($paym_method){
		$this->active_paym_method = $paym_method;
	}
	
	public function getActivePaymMethod(){
		return $this->active_paym_method;
	}
	
}