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

class CheckoutModel extends BaseModel{
    
	protected $cart = null;
	
    function __construct(){
        \JPluginHelper::importPlugin('jshoppingorder');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshopCheckout', array(&$obj));
    }
	
	function setCart($cart){
		$this->cart = $cart;
	}
	
	function getCart(){
		return $this->cart;
	}
    
    function sendOrderEmail($order_id, $manuallysend = 0){
		$model = \JSFactory::getModel('orderMail', 'Site');
		$model->setData($order_id, $manuallysend);
		return $model->send();
    }
    
    function changeStatusOrder($order_id, $status, $sendmessage = 1){
		$model = \JSFactory::getModel('orderChangeStatus', 'Site');
		$model->setData($order_id, $status, $sendmessage);
		return $model->store();
    }
    
    function cancelPayOrder($order_id){
        $order = \JSFactory::getTable('order');
        $order->load($order_id);
        $pm_method = \JSFactory::getTable('paymentMethod');
        $pm_method->load($order->payment_method_id);
        $pmconfigs = $pm_method->getConfigs();
        $status = $pmconfigs['transaction_cancel_status'];
        if (!$status){
			$status = $pmconfigs['transaction_failed_status'];
		}
        if ($order->order_created) 
			$sendmessage = 1; 
		else 
			$sendmessage = 0;
        $this->changeStatusOrder($order_id, $status, $sendmessage);
        \JFactory::getApplication()->triggerEvent('onAfterCancelPayOrderJshopCheckout', array(&$order_id, $status, $sendmessage));
    }
    
    function setMaxStep($step){
        $session = \JFactory::getSession();
        $jhop_max_step = $session->get('jhop_max_step');
        if (!isset($jhop_max_step)) $session->set('jhop_max_step', 2);
        $jhop_max_step = $session->get('jhop_max_step');
        $session->set('jhop_max_step', $step);
        \JFactory::getApplication()->triggerEvent('onAfterSetMaxStepJshopCheckout', array(&$step));
    }
    
    function checkStep($step){
        $mainframe = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $session = \JFactory::getSession();
        
        if ($step<10){
            if (!$jshopConfig->shop_user_guest){
                \JSHelper::checkUserLogin();
            }
            
            $cart = \JSFactory::getModel('cart', 'Site');
            $cart->load();

            if ($cart->getCountProduct() == 0){
                $mainframe->redirect(\JSHelper::SEFLink('index.php?option=com_jshopping&controller=cart&task=view',1,1));
                exit();
            }

            if ($jshopConfig->min_price_order && ($cart->getPriceProducts() < ($jshopConfig->min_price_order * $jshopConfig->currency_value) )){
                \JSError::raiseNotice("", sprintf(\JText::_('JSHOP_ERROR_MIN_SUM_ORDER'), \JSHelper::formatprice($jshopConfig->min_price_order * $jshopConfig->currency_value)));
                $mainframe->redirect(\JSHelper::SEFLink('index.php?option=com_jshopping&controller=cart&task=view',1,1));
                exit();
            }
            
            if ($jshopConfig->max_price_order && ($cart->getPriceProducts() > ($jshopConfig->max_price_order * $jshopConfig->currency_value) )){
                \JSError::raiseNotice("", sprintf(\JText::_('JSHOP_ERROR_MAX_SUM_ORDER'), \JSHelper::formatprice($jshopConfig->max_price_order * $jshopConfig->currency_value)));
                $mainframe->redirect(\JSHelper::SEFLink('index.php?option=com_jshopping&controller=cart&task=view',1,1));
                exit();
            }
        }

        if ($step>2){
            $jhop_max_step = $session->get("jhop_max_step");
            if (!$jhop_max_step){
                $session->set('jhop_max_step', 2);
                $jhop_max_step = 2;
            }
            if ($step > $jhop_max_step){
                if ($step==10){
                    $mainframe->redirect(\JSHelper::SEFLink('index.php?option=com_jshopping&controller=cart&task=view',1,1));
                }else{
                    \JSError::raiseWarning("", \JText::_('JHOP_ERROR_STEP'));
                    $mainframe->redirect(\JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2',1,1, $jshopConfig->use_ssl));
                }
                exit();
            }
        }
    }
    
    function showCheckoutNavigation($step){
        $jshopConfig = \JSFactory::getConfig();
        if (!$jshopConfig->ext_menu_checkout_step && in_array($step, array('0', '1'))){
            return '';
        }
        if ($jshopConfig->step_4_3){
            $array_navigation_steps = array('0'=>\JText::_('JSHOP_CART'), '1'=>\JText::_('JSHOP_LOGIN'), '2'=>\JText::_('JSHOP_STEP_ORDER_2'), '4'=>\JText::_('JSHOP_STEP_ORDER_4'), '3'=>\JText::_('JSHOP_STEP_ORDER_3'), '5'=>\JText::_('JSHOP_STEP_ORDER_5'));
        }else{
            $array_navigation_steps = array('0'=>\JText::_('JSHOP_CART'), '1'=>\JText::_('JSHOP_LOGIN'), '2' => \JText::_('JSHOP_STEP_ORDER_2'), '3' => \JText::_('JSHOP_STEP_ORDER_3'), '4' => \JText::_('JSHOP_STEP_ORDER_4'), '5' => \JText::_('JSHOP_STEP_ORDER_5'));
        }
        $output = array();
        $cssclass = array();
        if (!$jshopConfig->ext_menu_checkout_step){
            unset($array_navigation_steps['0']);
            unset($array_navigation_steps['1']);
        }
        if ($jshopConfig->shop_user_guest==2){
            unset($array_navigation_steps['1']);    
        }
        if ($jshopConfig->without_shipping || $jshopConfig->hide_shipping_step){
            unset($array_navigation_steps['4']);
        }
        if ($jshopConfig->without_payment || $jshopConfig->hide_payment_step){
            unset($array_navigation_steps['3']);
        }

        foreach($array_navigation_steps as $key=>$value){
            if ($key=='0'){
                $url = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=cart', 1, 0);
            }elseif($key=='1'){
                $url = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=user&task=login', 1, 0, $jshopConfig->use_ssl);
            }else{
                $url = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step'.$key,0,0,$jshopConfig->use_ssl);
            }
            if ($key < $step && !($jshopConfig->step_4_3 && $key==3 && $step==4) || ($jshopConfig->step_4_3 && $key==4 && $step==3)){
                $output[$key] = '<span class="not_active_step"><a href="'.$url.'">'.$value.'</a></span>';
                $cssclass[$key] = "prev";
            }else{
                if ($key == $step){
                    $output[$key] = '<span id="active_step"  class="active_step">'.$value.'</span>';
                    $cssclass[$key] = "active";
                }else{
                    $output[$key] = '<span class="not_active_step">'.$value.'</span>';
                    $cssclass[$key] = "next";
                }
            }
        }

        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayCheckoutNavigator', array(&$output, &$array_navigation_steps, &$step));
        
        $view = $this->getView('checkout');
        $view->setLayout("menu");
        $view->set('steps', $output);
        $view->set('step', $step);
        $view->set('cssclass', $cssclass);
        $view->set('array_navigation_steps', $array_navigation_steps);
        $dispatcher->triggerEvent('onAfterDisplayCheckoutNavigator', array(&$view));
    return $view->loadTemplate();
    }
    
	function loadSmallCart($step = 0){
		$jshopConfig = \JSFactory::getConfig();
		if ($jshopConfig->show_cart_all_step_checkout || $step==5){
            $small_cart = $this->showSmallCart($step);
        }else{
            $small_cart = '';
        }
		return $small_cart;
	}
	
    function showSmallCart($step = 0){
        $jshopConfig = \JSFactory::getConfig();
        $dispatcher = \JFactory::getApplication();
		
        $cart = \JSFactory::getModel('cart', 'Site')->init('cart', 0);
		
		$cartpreview = \JSFactory::getModel('cartPreview', 'Site');
		$cartpreview->setCart($cart);
		$cartpreview->setCheckoutStep($step);
		$price_items_show = $cartpreview->getPriceItemsShow();
		$deliverytimes = \JSFactory::getAllDeliveryTime();

		$payment_name = $cartpreview->getCartPaymentName();
		$tax_list = $cartpreview->getTaxExt();
		$fullsumm = $cartpreview->getFullSum();
		$show_percent_tax = $cartpreview->getShowPercentTax();
        $hide_subtotal = $cartpreview->getHideSubtotal();		
		$text_total = $cartpreview->getTextTotalPrice();

        foreach($cart->products as $k=>$v) {
			$cart->products[$k]['_tmp_tr_before'] = "";
			$cart->products[$k]['_tmp_tr_after'] = "";
			$cart->products[$k]['_ext_product_name'] = "";
			$cart->products[$k]['_ext_attribute_html'] = "";
			$cart->products[$k]['_ext_price_html'] = "";
			$cart->products[$k]['_qty_unit'] = "";
			$cart->products[$k]['_ext_price_total_html'] = "";
		}
                
        $view = $this->getView('cart');
        $view->setLayout("checkout");
        $view->set('step', $step);
        $view->set('config', $jshopConfig);
        $view->set('products', $cart->products);
        $view->set('summ', $cartpreview->getSubTotal());
        $view->set('image_product_path', $jshopConfig->image_product_live_path);
        $view->set('no_image', $jshopConfig->noimage);
        $view->set('discount', $cartpreview->getDiscount());
        $view->set('free_discount', $cartpreview->getFreeDiscount(1));
        $view->set('deliverytimes', $deliverytimes);
        $view->set('payment_name', $payment_name);
		if ($price_items_show['payment_price']){
			$view->set('summ_payment', $cart->getPaymentPrice());
		} else {
            $view->summ_payment = 0;
        }
		if ($price_items_show['shipping_price']){
			$view->set('summ_delivery', $cart->getShippingPrice());
		}
		if ($price_items_show['shipping_package_price']){
			$view->set('summ_package', $cart->getPackagePrice());
		}
        $view->set('tax_list', $tax_list);
        $view->set('fullsumm', $fullsumm);
        $view->set('show_percent_tax', $show_percent_tax);
        $view->set('hide_subtotal', $hide_subtotal);
        $view->set('text_total', $text_total);
        $view->set('weight', $cartpreview->getWeight());
        $view->_tmp_ext_subtotal = "";
        $view->_tmp_html_after_subtotal = "";
        $view->_tmp_ext_discount_text = "";
        $view->_tmp_ext_discount = "";
        $view->_tmp_ext_shipping = "";
        $view->_tmp_ext_shipping_package = "";
        $view->_tmp_ext_payment = "";        
        $view->_tmp_ext_tax = array();
        $view->_tmp_ext_total = "";
        $view->_tmp_html_after_total = "";
        $view->_tmp_html_after_checkout_cart = "";
        $view->checkoutcartdescr = "";
        foreach ($tax_list as $k => $v) {
            $view->_tmp_ext_tax[$k] = "";
        }
        $dispatcher->triggerEvent('onBeforeDisplayCheckoutCartView', array(&$view));
    return $view->loadTemplate();
    }
    
	function removeWishlistItemToCart($number_id){
		$dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeLoadWishlistRemoveToCart', array(&$number_id));
        
        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load("wishlist");
        $prod = $cart->products[$number_id];
        $attr = unserialize($prod['attributes']);
        $freeattribut = unserialize($prod['freeattributes']);
        $cart->delete($number_id);
                        
        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load("cart");        
        $cart->add($prod['product_id'], $prod['quantity'], $attr, $freeattribut);
        $dispatcher->triggerEvent('onAfterWishlistRemoveToCart', array(&$cart));
		return $cart;
	}
	
    function deleteSession(){
        $session = \JFactory::getSession();
        $session->set('check_params', null);
        $session->set('cart', null);
        $session->set('jhop_max_step', null);        
        $session->set('jshop_price_shipping_tax_percent', null);
        $session->set('jshop_price_shipping', null);
        $session->set('jshop_price_shipping_tax', null);
        $session->set('pm_params', null);
        $session->set('payment_method_id', null);
        $session->set('jshop_payment_price', null);
        $session->set('shipping_method_id', null);
        $session->set('sh_pr_method_id', null);
        $session->set('jshop_price_shipping_tax_percent', null);                
        $session->set('jshop_end_order_id', null);
        $session->set('jshop_send_end_form', null);
        $session->set('show_pay_without_reg', 0);
        $session->set('checkcoupon', 0);
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterDeleteDataOrder', array(&$obj));
    }
    
	function setEmptyCheckoutPrices(){
		$cart = $this->getCart();
		$cart->setShippingId(0);
		$cart->setShippingPrId(0);
		$cart->setShippingPrice(0);
		$cart->setPaymentId(0);
		$cart->setPaymentParams("");
		$cart->setPaymentPrice(0);
	}
	
    function getNoReturn(){
		$jshopConfig = \JSFactory::getConfig();
		$cart = $this->getCart();
        $no_return = 0;
        if ($jshopConfig->return_policy_for_product){
            $cart_products = array();
            foreach($cart->products as $products){
                $cart_products[] = $products['product_id'];
            }
            $cart_products = array_unique($cart_products);
            $_product_option = \JSFactory::getTable('productOption');
            $list_no_return = $_product_option->getProductOptionList($cart_products, 'no_return');
            $no_return = intval(in_array('1', $list_no_return));
        }
        if ($jshopConfig->no_return_all){
            $no_return = 1;
        }
        return $no_return;
    }
	
	function getInvoiceInfo($adv_user){
		$lang = \JSFactory::getLang();
		$field_name = $lang->get("name");
		$info = array();
        $country = \JSFactory::getTable('country');
        $country->load($adv_user->country);
        $info['f_name'] = $adv_user->f_name;
        $info['l_name'] = $adv_user->l_name;
        $info['firma_name'] = $adv_user->firma_name;
        $info['street'] = $adv_user->street;
        $info['street_nr'] = $adv_user->street_nr;
        $info['zip'] = $adv_user->zip;
        $info['state'] = $adv_user->state;
        $info['city'] = $adv_user->city;
        $info['country'] = $country->$field_name;
        $info['home'] = $adv_user->home;
        $info['apartment'] = $adv_user->apartment;
		$info['email'] = $adv_user->email;
        $info['phone'] = $adv_user->phone;
	return $info;
	}
	
	function getDeliveryInfo($adv_user, $invoice_info){
		$lang = \JSFactory::getLang();
		$field_name = $lang->get("name");
		if ($adv_user->delivery_adress){
			$info = array();
            $country = \JSFactory::getTable('country');
            $country->load($adv_user->d_country);
			$info['f_name'] = $adv_user->d_f_name;
            $info['l_name'] = $adv_user->d_l_name;
			$info['firma_name'] = $adv_user->d_firma_name;
			$info['street'] = $adv_user->d_street;
            $info['street_nr'] = $adv_user->d_street_nr;
			$info['zip'] = $adv_user->d_zip;
			$info['state'] = $adv_user->d_state;
            $info['city'] = $adv_user->d_city;
			$info['country'] = $country->$field_name;
            $info['home'] = $adv_user->d_home;
            $info['apartment'] = $adv_user->d_apartment;
		} else {
            $info = $invoice_info;
		}
	return $info;
	}
	
	function getDeliveryDateShow(){
		$cart = $this->getCart();
		$jshopConfig = \JSFactory::getConfig();
		if ($jshopConfig->show_delivery_date){
            $date = $cart->getDeliveryDate();
            if ($date){
                $date = \JSHelper::formatdate($date);
            }
        }else{
            $date = '';
        }
	return $date;
	}
	
	function getDeliveryTime(){
		$cart = $this->getCart();
		$jshopConfig = \JSFactory::getConfig();
		$sh_mt_pr = $this->getShippingMethodPrice();
        if ($jshopConfig->show_delivery_time_checkout){
            $deliverytimes = \JSFactory::getAllDeliveryTime();
            $deliverytimes[0] = '';
            $delivery_time = $deliverytimes[$sh_mt_pr->delivery_times_id];
            if (!$delivery_time && $jshopConfig->delivery_order_depends_delivery_product){
                $delivery_time = $cart->getDelivery();
            }
        }else{
            $delivery_time = '';
        }
	return $delivery_time;
	}
	
	function getShippingMethod(){
		$cart = $this->getCart();
		$sh_method = \JSFactory::getTable('shippingMethod');
        $id = $cart->getShippingId();
        $sh_method->load($id);
	return $sh_method;
	}
	
	function getShippingMethodPrice(){
		$cart = $this->getCart();
		$sh_mt_pr = \JSFactory::getTable('shippingMethodPrice');
        $sh_mt_pr->load($cart->getShippingPrId());
	return $sh_mt_pr;
	}
	
	function getPaymentMethod(){
		$cart = $this->getCart();
		$pm_method = \JSFactory::getTable('paymentMethod');
        $id = $cart->getPaymentId();
		$pm_method->load($id);
	return $pm_method;
	}
	
	function setEndOrderId($id){
		\JFactory::getSession()->set("jshop_end_order_id", $id);
	}
	
	function getEndOrderId(){
		return \JFactory::getSession()->get("jshop_end_order_id");
	}
	
	function setSendEndForm($val){
		\JFactory::getSession()->set("jshop_send_end_form", $val);
	}
	
	function getSendEndForm(){
		return \JFactory::getSession()->get("jshop_send_end_form");
	}
	
}