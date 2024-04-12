<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
use Joomla\Component\Jshopping\Site\Helper\Selects;
defined('_JEXEC') or die();

class CheckoutController extends BaseController{

    public function init(){
        \JPluginHelper::importPlugin('jshoppingcheckout');
        \JPluginHelper::importPlugin('jshoppingorder');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshoppingControllerCheckout', array(&$obj));
    }

    function display($cachable = false, $urlparams = false){
        $this->step2();
    }

    function step2(){
        $checkout = \JSFactory::getModel('checkout', 'Site');
        $checkout->checkStep(2);
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onLoadCheckoutStep2', array());

        $jshopConfig = \JSFactory::getConfig();

        $checkLogin = $this->input->getInt('check_login');
        if ($checkLogin){
            \JSFactory::getModel('userlogin', 'Site')->setPayWithoutReg();
            \JSHelper::checkUserLogin();
        }

		Metadata::checkoutAddress();

        $adv_user = \JSFactory::getUser()->loadDataFromEdit();

        $config_fields = $jshopConfig->getListFieldsRegisterType('address');
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');

        $checkout_navigator = $checkout->showCheckoutNavigation(2);
        $small_cart = $checkout->loadSmallCart(2);

		$select_countries = Selects::getCountry($adv_user->country);
		$select_d_countries = Selects::getCountry($adv_user->d_country, null, 'd_country');
		$select_titles = Selects::getTitle($adv_user->title);
		$select_d_titles = Selects::getTitle($adv_user->d_title, null, 'd_title');
		$select_client_types = Selects::getClientType($adv_user->client_type);

        \JSHelper::filterHTMLSafe($adv_user, ENT_QUOTES);

        $view = $this->getView("checkout");
        $view->setLayout("adress");
        $view->set('select', $jshopConfig->user_field_title);
        $view->set('config', $jshopConfig);
        $view->set('select_countries', $select_countries);
        $view->set('select_d_countries', $select_d_countries);
        $view->set('select_titles', $select_titles);
        $view->set('select_d_titles', $select_d_titles);
        $view->set('select_client_types', $select_client_types);
        $view->set('live_path', \JURI::base());
        $view->set('config_fields', $config_fields);
        $view->set('count_filed_delivery', $count_filed_delivery);
        $view->set('user', $adv_user);
        $view->set('delivery_adress', $adv_user->delivery_adress);
        $view->set('checkout_navigator', $checkout_navigator);
        $view->set('small_cart', $small_cart);
        $view->_tmp_ext_html_address_start = "";
        $view->_tmpl_address_html_2 = "";
        $view->_tmpl_address_html_3 = "";
        $view->_tmpl_address_html_4 = "";
        $view->_tmpl_address_html_5 = "";
        $view->_tmpl_address_html_6 = "";
        $view->_tmpl_address_html_7 = "";
        $view->_tmp_ext_html_address_end = "";
        $view->_tmpl_address_html_8 = "";
        $view->_tmpl_address_html_9 = "";
        $view->set('action', \JSFactory::getModel('checkoutStep', 'Site')->getCheckoutUrl('step2save', 0, 0));
        $dispatcher->triggerEvent('onBeforeDisplayCheckoutStep2View', array(&$view));
        $view->display();
    }

    function step2save(){
		$jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();
		$model = \JSFactory::getModel('useredit', 'Site');
		$adv_user = \JSFactory::getUser();
		$user = \JFactory::getUser();
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');
		$checkout = \JSFactory::getModel('checkout', 'Site');
        $checkout->checkStep(2);

		$post = $this->input->post->getArray();
		$back_url = $checkoutStep->getCheckoutUrl('2');

        $dispatcher->triggerEvent('onLoadCheckoutStep2save', array(&$post));

        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();

		$model->setUser($adv_user);
		$model->setData($post);
		if (!$model->check("address")){
            \JSError::raiseWarning('', $model->getError());
            $this->setRedirect($back_url );
            return 0;
        }

        $dispatcher->triggerEvent('onBeforeSaveCheckoutStep2', array(&$adv_user, &$user, &$cart, &$model));

		if (!$model->save()){
            \JSError::raiseWarning('500', $model->getError());
            $this->setRedirect($back_url);
            return 0;
        }

        \JSHelper::setNextUpdatePrices();
		$checkout->setCart($cart);
		$checkout->setEmptyCheckoutPrices();

        $dispatcher->triggerEvent('onAfterSaveCheckoutStep2', array(&$adv_user, &$user, &$cart));

		$next_step = $checkoutStep->getNextStep(2);
		$checkout->setMaxStep($next_step);
		$this->setRedirect($checkoutStep->getCheckoutUrl($next_step));
    }

    function step3(){
		$jshopConfig = \JSFactory::getConfig();
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');
        $checkout = \JSFactory::getModel('checkoutPayment', 'Site');
    	$checkout->checkStep(3);

		$dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onLoadCheckoutStep3', array() );

		if ($jshopConfig->without_payment){
			$next_step = $checkoutStep->getNextStep(3);
			$checkout->setMaxStep($next_step);
			$this->setRedirect($checkoutStep->getCheckoutUrl($next_step));
            return 0;
        }

        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();
		$checkout->setCart($cart);

        $adv_user = \JSFactory::getUser();

        Metadata::checkoutPayment();

        $checkout_navigator = $checkout->showCheckoutNavigation(3);
        $small_cart = $checkout->loadSmallCart(3);

		$paym = $checkout->getCheckoutListPayments();
		$active_payment = $checkout->getCheckoutActivePayment($paym, $adv_user);
		$first_payment_class = $checkout->getCheckoutFirstPaymentClass($paym);

        if ($jshopConfig->hide_payment_step){
            if (!$first_payment_class){
                \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_PAYMENT'));
                return 0;
            }
            $this->setRedirect($checkoutStep->getCheckoutUrl('step3save&payment_method='.$first_payment_class));
            return 0;
        }

        $view = $this->getView("checkout");
        $view->setLayout("payments");
        $view->set('payment_methods', $paym);
        $view->set('active_payment', $active_payment);
        $view->set('checkout_navigator', $checkout_navigator);
        $view->set('small_cart', $small_cart);
        $view->_tmp_ext_html_payment_start = "";
        $view->_tmp_ext_html_payment_end = "";
        $view->set('action', $checkoutStep->getCheckoutUrl('step3save', 0, 0));
        $dispatcher->triggerEvent('onBeforeDisplayCheckoutStep3View', array(&$view));
        $view->display();
    }

    function step3save(){
        $checkout = \JSFactory::getModel('checkoutPayment', 'Site');
        $checkout->checkStep(3);

		$dispatcher = \JFactory::getApplication();
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');
        $post = $this->input->post->getArray();

        $dispatcher->triggerEvent('onBeforeSaveCheckoutStep3save', array(&$post) );

        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();
		$checkout->setCart($cart);

        $adv_user = \JSFactory::getUser();

        $payment_method = $this->input->getVar('payment_method'); //class payment method
        $params = $this->input->getVar('params');

		if (!$checkout->savePaymentData($payment_method, $params, $adv_user)){
			\JSError::raiseWarning('', $checkout->getError());
            $this->setRedirect($checkoutStep->getCheckoutUrl('3'));
            return 0;
		}
		$paym_method = $checkout->getActivePaymMethod();

        $dispatcher->triggerEvent('onAfterSaveCheckoutStep3save', array(&$adv_user, &$paym_method, &$cart));

		$next_step = $checkoutStep->getNextStep(3);
		$checkout->setMaxStep($next_step);
		$this->setRedirect($checkoutStep->getCheckoutUrl($next_step));
    }

    function step4(){
		$dispatcher = \JFactory::getApplication();
        $checkout = \JSFactory::getModel('checkoutShipping', 'Site');
        $checkout->checkStep(4);
        $jshopConfig = \JSFactory::getConfig();
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');

		$dispatcher->triggerEvent('onLoadCheckoutStep4', array());

		if ($jshopConfig->without_shipping){
			$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');
			$next_step = $checkoutStep->getNextStep(4);
			$checkout->setMaxStep($next_step);
			$this->setRedirect($checkoutStep->getCheckoutUrl($next_step));
            return 0;
        }

        Metadata::checkoutShipping();

        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();
		$checkout->setCart($cart);
        $adv_user = \JSFactory::getUser();

        $checkout_navigator = $checkout->showCheckoutNavigation(4);
        $small_cart = $checkout->loadSmallCart(4);

		$shippings = $checkout->getCheckoutListShippings($adv_user);
		if ($shippings===false){
			\JSError::raiseWarning("", $checkout->getError());
			return 0;
		}
		if (count($shippings)==0 && $jshopConfig->checkout_step4_show_error_shipping_config){
			\JSError::raiseWarning("", \JText::_('JSHOP_ERROR_SHIPPING'));
		}
		$active_shipping = $checkout->getCheckoutActiveShipping($shippings, $adv_user);

        if ($jshopConfig->hide_shipping_step){
            $first_shipping = $checkout->getCheckoutFirstShipping($shippings);
            if (!$first_shipping){
                \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_SHIPPING'));
                return 0;
            }
            $this->setRedirect($checkoutStep->getCheckoutUrl('step4save&sh_pr_method_id='.$first_shipping));
            return 0;
        }

        $view = $this->getView("checkout");
        $view->setLayout("shippings");
        $view->set('shipping_methods', $shippings);
        $view->set('active_shipping', $active_shipping);
        $view->set('config', $jshopConfig);
        $view->set('checkout_navigator', $checkout_navigator);
        $view->set('small_cart', $small_cart);
        $view->_tmp_ext_html_shipping_start = "";
        $view->_tmp_ext_html_shipping_end = "";
        $view->set('action', $checkoutStep->getCheckoutUrl('step4save', 0, 0));
        $dispatcher->triggerEvent('onBeforeDisplayCheckoutStep4View', array(&$view));
        $view->display();
    }

    function step4save(){
        $checkout = \JSFactory::getModel('checkoutShipping', 'Site');
    	$checkout->checkStep(4);
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');

		$dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveCheckoutStep4save', array());

		$sh_pr_method_id = $this->input->getInt('sh_pr_method_id');
		$allparams = $this->input->getVar('params');

        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();
		$checkout->setCart($cart);
        $adv_user = \JSFactory::getUser();

		if (!$checkout->saveShippingData($sh_pr_method_id, $allparams, $adv_user)){
			\JSError::raiseWarning('', $checkout->getError());
            $this->setRedirect($checkoutStep->getCheckoutUrl('4'));
            return 0;
		}
		$sh_method = $checkout->getActiveShippingMethod();
		$shipping_method_price = $checkout->getActiveShippingMethodPrice();

        $dispatcher->triggerEvent('onAfterSaveCheckoutStep4', array(&$adv_user, &$sh_method, &$shipping_method_price, &$cart));

		$next_step = $checkoutStep->getNextStep(4);
		if ($next_step==3){
			$checkout->setMaxStep(4);
		}else{
			$checkout->setMaxStep($next_step);
		}
		$this->setRedirect($checkoutStep->getCheckoutUrl($next_step));
    }

    function step5(){
        $checkout = \JSFactory::getModel('checkout', 'Site');
        $checkout->checkStep(5);
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onLoadCheckoutStep5', array());

        Metadata::checkoutPreview();

        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();
		$checkout->setCart($cart);

        $jshopConfig = \JSFactory::getConfig();
        $adv_user = \JSFactory::getUser();
        $sh_method = $checkout->getShippingMethod();
		$delivery_time = $checkout->getDeliveryTime();
		$delivery_date = $checkout->getDeliveryDateShow();
        $pm_method = $checkout->getPaymentMethod();
        $invoice_info = $checkout->getInvoiceInfo($adv_user);
        $delivery_info = $checkout->getDeliveryInfo($adv_user, $invoice_info);
        $no_return = $checkout->getNoReturn();
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');
        $sh_method->name = $sh_method->getName();

        $checkout_navigator = $checkout->showCheckoutNavigation(5);
        $small_cart = $checkout->loadSmallCart(5);

		$view = $this->getView("checkout");
        $view->setLayout("previewfinish");
        $dispatcher->triggerEvent('onBeforeDisplayCheckoutStep5', array(&$sh_method, &$pm_method, &$delivery_info, &$cart, &$view));
        $view->set('no_return', $no_return);
		$view->set('sh_method', $sh_method );
		$view->set('payment_name', $pm_method->getName());
        $view->set('delivery_info', $delivery_info);
		$view->set('invoice_info', $invoice_info);
        $view->set('action', \JSFactory::getModel('checkoutStep', 'Site')->getCheckoutUrl('step5save', 0, 0));
        $view->set('config', $jshopConfig);
        $view->set('delivery_time', $delivery_time);
        $view->set('delivery_date', $delivery_date);
        $view->set('checkout_navigator', $checkout_navigator);
        $view->set('small_cart', $small_cart);
		$view->set('count_filed_delivery', $count_filed_delivery);
        $view->_tmp_ext_html_previewfinish_start = "";
        $view->_tmp_ext_html_previewfinish_agb = "";
        $view->_tmp_ext_html_previewfinish_before_button = "";
        $view->_tmp_ext_html_previewfinish_end = "";
        $dispatcher->triggerEvent('onBeforeDisplayCheckoutStep5View', array(&$view));
    	$view->display();
    }

    function step5save(){
		$session = \JFactory::getSession();
        $jshopConfig = \JSFactory::getConfig();
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');
        $checkout = \JSFactory::getModel('checkoutOrder', 'Site');
        $checkout->checkStep(5);

		$checkagb = $this->input->getVar('agb');
		$post = $this->input->post->getArray();
		$back_url = $checkoutStep->getCheckoutUrl('5');
		$cart_url = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1, 1);

        $dispatcher = \JFactory::getApplication();
		$dispatcher->triggerEvent('onLoadStep5save', array(&$checkagb));

        $adv_user = \JSFactory::getUser();
        $cart = \JSFactory::getModel('cart', 'Site')->init();
        $cart->setDisplayItem(1, 1);

		$checkout->setCart($cart);

		if (!$checkout->checkAgb($checkagb)){
			\JSError::raiseWarning("", $checkout->getError());
            $this->setRedirect($back_url);
            return 0;
		}
        if (!$cart->checkListProductsQtyInStore()){
            $this->setRedirect($cart_url);
            return 0;
        }
		if ($jshopConfig->step5_check_coupon && !$checkout->checkCoupon()){
			\JSError::raiseWarning("", $checkout->getError());
            $this->setRedirect($cart_url);
            return 0;
		}

		$order = $checkout->orderDataSave($adv_user, $post);

        $dispatcher->triggerEvent('onEndCheckoutStep5', array(&$order, &$cart));

		$checkout->setSendEndForm(0);

        if ($jshopConfig->without_payment || $order->order_total==0){
            $checkout->setMaxStep(10);
            $this->setRedirect($checkoutStep->getCheckoutUrl('finish'));
            return 0;
        }

        $pmconfigs = $checkout->getPaymentMethod()->getConfigs();

        $task = "step6";
        if (isset($pmconfigs['windowtype']) && $pmconfigs['windowtype']==2){
            $task = "step6iframe";
            $session->set("jsps_iframe_width", $pmconfigs['iframe_width']);
            $session->set("jsps_iframe_height", $pmconfigs['iframe_height']);
        }
        $checkout->setMaxStep(6);
        $this->setRedirect($checkoutStep->getCheckoutUrl($task));
    }

    function step6iframe(){
        $checkout = \JSFactory::getModel('checkout', 'Site');
        $checkout->checkStep(6);
        $session = \JFactory::getSession();
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');

        $width = $session->get("jsps_iframe_width");
        $height = $session->get("jsps_iframe_height");
        if (!$width) $width = 600;
        if (!$height) $height = 600;
		$url = $checkoutStep->getCheckoutUrl('step6&wmiframe=1');

        \JFactory::getApplication()->triggerEvent('onBeforeStep6Iframe', array(&$width, &$height, &$url));

		$view = $this->getView("checkout");
        $view->setLayout("step6iframe");
		$view->set('width', $width);
		$view->set('height', $height);
		$view->set('url', $url);
    	$view->display();
    }

    function step6(){
        $checkout = \JSFactory::getModel('checkoutOrder', 'Site');
        $checkout->checkStep(6);
        $jshopConfig = \JSFactory::getConfig();
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');

        header("Cache-Control: no-cache, must-revalidate");
        $order_id = $checkout->getEndOrderId();
        $wmiframe = $this->input->getInt("wmiframe");

        if (!$order_id){
            \JSError::raiseWarning("", \JText::_('JSHOP_SESSION_FINISH'));
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('5'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('5'));
            }
        }

		// user click back in payment system
        if ($checkout->getSendEndForm() == 1){
            $this->cancelPayOrder($order_id);
            return 0;
        }

		if (!$checkout->showEndFormPaymentSystem($order_id)){
			$checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('finish'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('finish'));
            }
            return 0;
		}
    }

    function step7(){
        $checkout = \JSFactory::getModel('checkoutBuy', 'Site');
        $wmiframe = $this->input->getInt("wmiframe");
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');

        \JFactory::getApplication()->triggerEvent('onLoadStep7', array());

		$act = $this->input->getVar("act");
        $payment_method = $this->input->getVar("js_paymentclass");
		$no_lang = $this->input->getInt('no_lang');

        $checkout->saveToLogPaymentData();
		$checkout->setSendEndForm(0);

		$checkout->setAct($act);
		$checkout->setPaymentMethodClass($payment_method);
		$checkout->setNoLang($no_lang);
		if (!$checkout->loadUrlParams()){
			\JSError::raiseWarning('', $checkout->getError());
            return 0;
		}

        if ($act == "cancel"){
            $this->cancelPayOrder($checkout->getOrderId());
            return 0;
        }

        if ($act == "return" && !$checkout->getCheckReturnParams()){
            $checkout->noCheckReturnExecute();
            $checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('finish'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('finish'));
            }
            return 1;
        }

		$codebuy = $checkout->buy();

		if ($codebuy==0){
			\JSError::raiseWarning('', $checkout->getError());
            return 0;
		}
		if ($codebuy==2){
			die();
		}

        if ($checkout->checkTransactionNoBuyCode()){
            \JSError::raiseWarning(500, $checkout->getCheckTransactionResText());
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('5'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('5'));
            }
            return 0;
        }else{
            $checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('finish'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('finish'));
            }
            return 1;
        }
    }

    function finish(){
        $checkout = \JSFactory::getModel('checkoutFinish', 'Site');
        $checkout->checkStep(10);
        $jshopConfig = \JSFactory::getConfig();
        $order_id = $checkout->getEndOrderId();
		$text = $checkout->getFinishStaticText();

        Metadata::checkoutFinish();

        \JFactory::getApplication()->triggerEvent('onBeforeDisplayCheckoutFinish', array(&$text, &$order_id));

        $view = $this->getView("checkout");
        $view->setLayout("finish");
        $view->set('text', $text);
        $view->display();

        if ($order_id){
			$checkout->paymentComplete($order_id, $text);
        }

        $checkout->clearAllDataCheckout();
    }

    function cancelPayOrder($order_id=""){
        $jshopConfig = \JSFactory::getConfig();
        $checkout = \JSFactory::getModel('checkout', 'Site');
		$checkoutStep = \JSFactory::getModel('checkoutStep', 'Site');
        $wmiframe = $this->input->getInt("wmiframe");

        if (!$order_id){
			$order_id = $checkout->getEndOrderId();
		}
        if (!$order_id){
            \JSError::raiseWarning("", \JText::_('JSHOP_SESSION_FINISH'));
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('5'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('5'));
            }
            return 0;
        }

        $checkout->cancelPayOrder($order_id);

        \JSError::raiseWarning("", \JText::_('JSHOP_PAYMENT_CANCELED'));
        if (!$wmiframe){
            $this->setRedirect($checkoutStep->getCheckoutUrl('5'));
        }else{
            $this->iframeRedirect($checkoutStep->getCheckoutUrl('5'));
        }
        return 0;
    }

    function iframeRedirect($url){
        echo "<script>parent.location.href='$url';</script>\n";
        $mainframe = \JFactory::getApplication();
        $mainframe->close();
    }

}