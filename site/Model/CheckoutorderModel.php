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

class CheckoutOrderModel  extends CheckoutModel{

	public function orderDataSave(&$adv_user, &$post){
		$jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();
		$session = \JFactory::getSession();
		$cart = $this->getCart();

		$order = $this->createOrder($adv_user, $post);

        $dispatcher->triggerEvent('onAfterCreateOrder', array(&$order, &$cart));

        if ($order->order_created) {
            $this->couponFinished($order);
        }

        $order->saveOrderItem($cart->products);

		$dispatcher->triggerEvent('onAfterCreateOrderFull', array(&$order, &$cart));

		$this->setEndOrderId($order->order_id);

        $order->saveOrderHistory(1, '');

        $order->updateProductsInStock(1);

		if ($jshopConfig->send_order_email && $order->order_created){
			$send = $this->sendOrderEmail($order->order_id);
		}

		return $order;
	}

	public function createOrder(&$adv_user, &$post){
		$jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();
		$cart = $this->getCart();

		$orderNumber = $jshopConfig->getNextOrderNumber(1);

		$pm_method = $this->getPaymentMethod();
        $paymentSystemVerySimple = 0;

		if ($jshopConfig->without_payment){
            $pm_method->payment_type = 1;
            $paymentSystemVerySimple = 1;
        }else{
            $paymentsysdata = $pm_method->getPaymentSystemData();
            $payment_system = $paymentsysdata->paymentSystem;
            if ($paymentsysdata->paymentSystemVerySimple){
                $paymentSystemVerySimple = 1;
            }
        }

		$order = \JSFactory::getTable('orderTable');
		$arr_property = $order->getListFieldCopyUserToOrder();
        foreach($adv_user as $key => $value){
            if (in_array($key, $arr_property)){
                $order->$key = $value;
            }
        }

        $sh_mt_pr = $this->getShippingMethodPrice();

        $order->order_date = $order->order_m_date = \JSHelper::getJsDate();
        $order->order_tax = $cart->getTax(1, 1, 1);
        $order->setTaxExt($cart->getTaxExt(1, 1, 1));
        $order->order_subtotal = $cart->getPriceProducts();
        $order->order_shipping = $cart->getShippingPrice();
        $order->order_payment = $cart->getPaymentPrice();
        $order->order_discount = $cart->getDiscountShow();
        $order->shipping_tax = $cart->getShippingPriceTaxPercent();
        $order->setShippingTaxExt($cart->getShippingTaxList());
        $order->payment_tax = $cart->getPaymentTaxPercent();
        $order->setPaymentTaxExt($cart->getPaymentTaxList());
        $order->order_package = $cart->getPackagePrice();
        $order->setPackageTaxExt($cart->getPackageTaxList());
        $order->order_total = $cart->getSum(1, 1, 1);
        $order->currency_exchange = $jshopConfig->currency_value;
        $order->vendor_type = $cart->getVendorType();
        $order->vendor_id = $cart->getVendorId();
        $order->order_status = $jshopConfig->default_status_order;
        $order->shipping_method_id = $cart->getShippingId();
        $order->payment_method_id = $cart->getPaymentId();
        $order->delivery_times_id = $sh_mt_pr->delivery_times_id;
        if ($jshopConfig->delivery_order_depends_delivery_product){
            $order->delivery_time = $cart->getDelivery();
        }
        if ($jshopConfig->show_delivery_date){
            $order->delivery_date = $cart->getDeliveryDate();
        }
        $order->coupon_id = $cart->getCouponId();
        $order->coupon_free_discount = $cart->getFreeDiscount();

        $pm_params = $cart->getPaymentParams();

        if (is_array($pm_params) && !$paymentSystemVerySimple){
            $payment_system->setParams($pm_params);
            $payment_params_names = $payment_system->getDisplayNameParams();
			$pm_params_data = $payment_system->getPaymentParamsData($pm_params);
            $order->payment_params = \JSHelper::getTextNameArrayValue($payment_params_names, $pm_params_data);
            if ($payment_system->getSavePaymentParams()){
                $order->setPaymentParamsData($pm_params);
            }
        }

        $sh_params = $cart->getShippingParams();
        if (is_array($sh_params)){
            $sh_method = $this->getShippingMethod();
            $shippingForm = $sh_method->getShippingForm();
            if ($shippingForm){
				$shippingForm->setParams($sh_params);
                $shipping_params_names = $shippingForm->getDisplayNameParams();
                $order->shipping_params = \JSHelper::getTextNameArrayValue($shipping_params_names, $sh_params);
            }
            $order->setShippingParamsData($sh_params);
        }

        $order->ip_address = $_SERVER['REMOTE_ADDR'];
        $order->order_add_info = $post['order_add_info'];
        $order->currency_code = $jshopConfig->currency_code;
        $order->currency_code_iso = $jshopConfig->currency_code_iso;
        $order->order_number = $order->formatOrderNumber($orderNumber);
        $order->order_hash = md5(time().$order->order_total.$order->user_id);
        $order->file_hash = md5(time().$order->order_total.$order->user_id."hashfile");
        $order->display_price = $jshopConfig->display_price_front_current;
        $order->lang = $jshopConfig->getLang();

        if ($order->client_type){
            $order->client_type_name = $jshopConfig->user_field_client_type[$order->client_type];
        }else{
            $order->client_type_name = "";
        }

		if ($order->order_total==0){
            $pm_method->payment_type = 1;
            $order->order_status = $jshopConfig->payment_status_paid;
        }

        if ($pm_method->payment_type == 1){
            $order->order_created = 1;
        }else {
            $order->order_created = 0;
        }

        if (!$adv_user->delivery_adress) $order->copyDeliveryData();
        $obj = $this;
        $dispatcher->triggerEvent('onBeforeCreateOrder', array(&$order, &$cart, &$obj));

        $order->store();

	return $order;
	}

	public function checkCoupon(){
		$session = \JFactory::getSession();
		$cart = $this->getCart();
		if (!$session->get('checkcoupon')){
            if (!$cart->checkCoupon()){
                $cart->setRabatt(0,0,0);
				$this->setError(\JText::_('JSHOP_RABATT_NON_CORRECT'));
                return 0;
            }
            $session->set('checkcoupon', 1);
        }
		return 1;
	}

	public function couponFinished($order){
		$jshopConfig = \JSFactory::getConfig();
		if ($order->coupon_id){
            $coupon = \JSFactory::getTable('coupon');
            $coupon->load($order->coupon_id);
            if ($coupon->finished_after_used){
                $free_discount = $order->coupon_free_discount;
                if ($free_discount > 0){
                    $coupon->coupon_value = $free_discount / $jshopConfig->currency_value;
                }else{
                    $coupon->used = $order->user_id;
                }
				$cart = $this->getCart();
                $user_id = $order->user_id;
				\JFactory::getApplication()->triggerEvent('onBeforeCouponFinished', array(&$coupon, &$cart, &$user_id, &$order));
                return $coupon->store();
            }
        }
		return 0;
	}

	public function checkAgb($checkagb){
		$jshopConfig = \JSFactory::getConfig();
		if ($jshopConfig->check_php_agb && $checkagb!='on'){
            $this->setError(\JText::_('JSHOP_ERROR_AGB'));
            return 0;
        }
		return 1;
	}

	public function showEndFormPaymentSystem($order_id){
        $order = \JSFactory::getTable('order');
        $order->load($order_id);

        $pm_method = $order->getPayment();
        $payment_method = $pm_method->payment_class;

		$paymentsysdata = $pm_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        if ($paymentsysdata->paymentSystemVerySimple){
            $paymentSystemVerySimple = 1;
        } else {
            $paymentSystemVerySimple = 0;
        }
        
        if ($pm_method->payment_type == 1 || $paymentSystemVerySimple){
            return 0;
        }

		$cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();

        \JFactory::getApplication()->triggerEvent('onBeforeShowEndFormStep6', array(&$order, &$cart, $pm_method));

		$this->setSendEndForm(1);

        $pmconfigs = $pm_method->getConfigs();
        $payment_system->showEndForm($pmconfigs, $order);
		return 1;
	}

}