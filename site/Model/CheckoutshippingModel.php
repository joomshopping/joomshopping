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

class CheckoutShippingModel  extends CheckoutModel{
	
	private $active_shipping_method;
	private $active_shipping_method_price;
	
	public function getCheckoutListShippings($adv_user){
		$jshopConfig = \JSFactory::getConfig();
		$cart = $this->getCart();
		$shippingmethod = \JSFactory::getTable('shippingMethod');
        $shippingmethodprice = \JSFactory::getTable('shippingMethodPrice');

		$id_country = $this->getAnyIdCountry($adv_user);
        if (!$id_country){
			$this->setError(\JText::_('JSHOP_REGWARN_COUNTRY'));
            return false;
        }

        if ($jshopConfig->show_delivery_time_checkout){
            $deliverytimes = \JSFactory::getAllDeliveryTime();
            $deliverytimes[0] = '';
        }
        if ($jshopConfig->show_delivery_date){
            $deliverytimedays = \JSFactory::getAllDeliveryTimeDays();
        }
		
        $sh_pr_method_id = $cart->getShippingPrId();
        $active_shipping = intval($sh_pr_method_id);
        $payment_id = $cart->getPaymentId();
		
        $shippings = $shippingmethod->getAllShippingMethodsCountry($id_country, $payment_id);
        foreach($shippings as $key=>$value){
            $shippingmethodprice->load($value->sh_pr_method_id);
            if ($jshopConfig->show_list_price_shipping_weight){
                $shippings[$key]->shipping_price = $shippingmethodprice->getPricesWeight($value->sh_pr_method_id, $id_country, $cart);
            }
            $prices = $shippingmethodprice->calculateSum($cart);
            $shippings[$key]->calculeprice = $prices['shipping']+$prices['package'];
            $shippings[$key]->delivery = '';
            $shippings[$key]->delivery_date_f = '';
            if ($jshopConfig->show_delivery_time_checkout){
                $shippings[$key]->delivery = $deliverytimes[$value->delivery_times_id];
            }
            if ($jshopConfig->show_delivery_date){
                $day = isset($deliverytimedays[$value->delivery_times_id]) ? $deliverytimedays[$value->delivery_times_id] : 0;
                if ($day){
                    $shippings[$key]->delivery_date = \JSHelper::getCalculateDeliveryDay($day);
                    $shippings[$key]->delivery_date_f = \JSHelper::formatdate($shippings[$key]->delivery_date);
                }
            }
            
            if ($value->sh_pr_method_id==$active_shipping){
                $params = $cart->getShippingParams();
            }else{
                $params = array();
            }
            
            $shippings[$key]->form = $shippingmethod->loadShippingForm($value->shipping_id, $value, $params);
        }
		return $shippings;
	}
	
	public function getCheckoutActiveShipping(&$shippings, &$adv_user){
		$cart = $this->getCart();
		$sh_pr_method_id = $cart->getShippingPrId();
        $active_shipping = intval($sh_pr_method_id);
        if (!$active_shipping){
            foreach($shippings as $v){
                if ($v->shipping_id == $adv_user->shipping_id){
                    $active_shipping = $v->sh_pr_method_id;
                    break;
                }
            }
        }
        if (!$active_shipping){
            $active_shipping = $this->getCheckoutFirstShipping($shippings);
        }
		return $active_shipping;
	}
	
	public function getCheckoutFirstShipping(&$shippings){
		if (isset($shippings[0])){
			$first = (int)$shippings[0]->sh_pr_method_id;
		}else{
			$first = 0;
		}
		return $first;
	}
	
	public function saveShippingData($sh_pr_method_id, &$allparams, &$adv_user){
		$jshopConfig = \JSFactory::getConfig();
		$cart = $this->getCart();
                
        $shipping_method_price = \JSFactory::getTable('shippingMethodPrice');
        $shipping_method_price->load($sh_pr_method_id);
        
        $sh_method = \JSFactory::getTable('shippingMethod');
        $sh_method->load($shipping_method_price->shipping_method_id);
        		
        if (!$shipping_method_price->sh_pr_method_id){
            $this->setError(\JText::_('JSHOP_ERROR_SHIPPING'));
            return 0;
        }
        
		$id_country = $this->getAnyIdCountry($adv_user);
		
        if (!$shipping_method_price->isCorrectMethodForCountry($id_country)){
            $this->setError(\JText::_('JSHOP_ERROR_SHIPPING'));
            return 0;
        }
        
        if (!$sh_method->shipping_id){
            $this->setError(\JText::_('JSHOP_ERROR_SHIPPING'));
            return 0;
        }
          
        if (isset($allparams[$sh_method->shipping_id])) {
            $params = $allparams[$sh_method->shipping_id];
        }
        
        if (isset($params)){
            $cart->setShippingParams($params);
        }else{
            $cart->setShippingParams('');
        }
        
        $shippingForm = $sh_method->getShippingForm();
        		
        if ($shippingForm && !$shippingForm->check($params, $sh_method)){            
			$this->setError($shippingForm->getErrorMessage());
            return 0;
        }
		
        $prices = $shipping_method_price->calculateSum($cart);
        $cart->setShippingId($sh_method->shipping_id);
        $cart->setShippingPrId($sh_pr_method_id);
        $cart->setShippingsDatas($prices, $shipping_method_price);
        
        if ($jshopConfig->show_delivery_date){
            $delivery_date = '';
            $deliverytimedays = \JSFactory::getAllDeliveryTimeDays();
            $day = $deliverytimedays[$shipping_method_price->delivery_times_id];
            if ($day){
                $delivery_date = \JSHelper::getCalculateDeliveryDay($day);
            }else{
                if ($jshopConfig->delivery_order_depends_delivery_product){
                    $day = $cart->getDeliveryDaysProducts();
                    if ($day){
                        $delivery_date = \JSHelper::getCalculateDeliveryDay($day);                    
                    }
                }
            }
            $cart->setDeliveryDate($delivery_date);
        }

        //update payment price        
        if ($cart->getPaymentId()){
			$paym_method = $this->getPaymentMethod();			
            $cart->setDisplayItem(1, 1);
            $paym_method->setCart($cart);
            $price = $paym_method->getPrice();
            $cart->setPaymentDatas($price, $paym_method);
        }

        $adv_user->saveTypeShipping($sh_method->shipping_id);
		
		$this->setActiveShippingMethod($sh_method);
		$this->setActiveShippingMethodPrice($shipping_method_price);
		
		return 1;
	}
	
	public function setActiveShippingMethod($sh_method){
		$this->active_shipping_method = $sh_method;
	}
	
	public function setActiveShippingMethodPrice($shipping_method_price){
		$this->active_shipping_method_price = $shipping_method_price;
	}
	
	public function getActiveShippingMethod(){
		return $this->active_shipping_method;
	}
	
	public function getActiveShippingMethodPrice(){
		return $this->active_shipping_method_price;
	}
	
	public function getAnyIdCountry($adv_user){
		$jshopConfig = \JSFactory::getConfig();
		if ($adv_user->delivery_adress){
            $id_country = $adv_user->d_country;
        }else{
            $id_country = $adv_user->country;
        }
        if (!$id_country){
			$id_country = $jshopConfig->default_country;
		}
		return $id_country;
	}
	
}