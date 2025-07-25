<?php
/**
* @version      5.8.0 18.06.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
use Joomla\Component\Jshopping\Site\Helper\Cart;
use Joomla\Component\Jshopping\Site\Helper\Request;
defined('_JEXEC') or die();

class CartController extends BaseController{

    public function init(){
        PluginHelper::importPlugin('jshoppingcheckout');
        $obj = $this;
        Factory::getApplication()->triggerEvent('onConstructJshoppingControllerCart', array(&$obj));
    }

    function display($cachable = false, $urlparams = false){
        $this->view();
    }

    function add(){
        header("Cache-Control: no-cache, must-revalidate");
        if (!Cart::checkAdd()){
			return 0;
		}

		$jshopConfig = JSFactory::getConfig();
		$dispatcher = Factory::getApplication();
        $ajax = $this->input->getInt('ajax');
        $product_id = $this->input->getInt('product_id');
        $category_id = $this->input->getInt('category_id');
		$quantity = Request::getQuantity();
		$attribut = Request::getAttribute();
        $to = Request::getCartTo();
        $freeattribut = Request::getFreeAttribute();

        JSFactory::getModel('checkout', 'Site')->setMaxStep(2);

        $cart = JSFactory::getModel('cart', 'Site');
        $cart->load($to);

        if (!$cart->add($product_id, $quantity, $attribut, $freeattribut)){
            if ($ajax){
                print Helper::getMessageJson();
                die();
            }
            JSFactory::getModel('productShop', 'Site')
				->setBackValue(array('pid'=>$product_id, 'attr'=>$attribut, 'freeattr'=>$freeattribut,'qty'=>$quantity));
			$dispatcher->triggerEvent('onAfterCartAddError', array(&$cart, &$product_id, &$quantity, &$attribut, &$freeattribut));
            $this->setRedirect(Helper::SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$category_id.'&product_id='.$product_id,1,1));
            return 0;
        }

		$dispatcher->triggerEvent('onAfterCartAddOk', array(&$cart, &$product_id, &$quantity, &$attribut, &$freeattribut));

        if ($ajax){
            print Helper::getOkMessageJson($cart);
            die();
        }

        if(
            ( $jshopConfig->not_redirect_in_cart_after_buy && $to == "cart" ) ||
            ( $jshopConfig->not_redirect_in_wishlist_after_buy && $to == "wishlist" )
        )
            {
            $this->setRedirect($_SERVER['HTTP_REFERER'], $cart->getMessageAddToCart());
            return 1;
        }

		$this->setRedirect(Helper::SEFLink($cart->getUrlList(), 1, 1));
    }

    function view(){
	    $jshopConfig = JSFactory::getConfig();
        if (!Cart::checkView()){
			return 0;
		}
		$dispatcher = Factory::getApplication();
        $ajax = $this->input->getInt('ajax');

		Metadata::cart();

		$cart = JSFactory::getModel('cart', 'Site')->init('cart', 1);

		$cartpreview = JSFactory::getModel('cartPreview', 'Site');
        $cartpreview->setCart($cart);
		$cartpreview->setCheckoutStep(0);

        if ($ajax == 2){
            print Helper::getOkMessageJson($cart);
            die();
        }

        $shopurl = $cartpreview->getBackUrlShop();
        $cartdescr = $cartpreview->getCartStaticText();
        $href_checkout = $cartpreview->getUrlCheckout();
		$show_percent_tax = $cartpreview->getShowPercentTax();
        $hide_subtotal = $cartpreview->getHideSubtotal();
        $checkout_navigator = JSFactory::getModel('checkout', 'Site')->showCheckoutNavigation('0');
		$deliverytimes = JSFactory::getAllDeliveryTime();
        $products = $cartpreview->getProductsPrepare($cartpreview->getProducts());

        $view = $this->getView('cart');
        $view->setLayout("cart");
        $view->cart = $cart;
        $view->config = $jshopConfig;
        $view->products = $products;
        $view->summ = $cartpreview->getSubTotal();
        $view->image_product_path = $jshopConfig->image_product_live_path;
        $view->image_path = $jshopConfig->live_path;
        $view->no_image = $jshopConfig->noimage;
        $view->href_shop = $shopurl;
        $view->href_checkout = $href_checkout;
        $view->discount = $cartpreview->getDiscount();
        $view->free_discount = $cartpreview->getFreeDiscount(1);
        $view->use_rabatt = $jshopConfig->use_rabatt_code;
        $view->tax_list = $cartpreview->getTaxExt();
        $view->fullsumm = $cartpreview->getFullSum();
        $view->show_percent_tax = $show_percent_tax;
        $view->hide_subtotal = $hide_subtotal;
        $view->weight = $cartpreview->getWeight();
        $view->shippinginfo = Helper::SEFLink($jshopConfig->shippinginfourl, 1);
        $view->cartdescr = $cartdescr;
        $view->checkout_navigator = $checkout_navigator;
        $view->deliverytimes = $deliverytimes;
        $view->_tmp_ext_tax = array();
        foreach ($cartpreview->getTaxExt() as $k => $v) {
            $view->_tmp_ext_tax[$k] = "";
        }
        $view->_tmp_ext_html_cart_start = "";
        $view->_tmp_html_after_subtotal = "";
        $view->_tmp_html_after_total = "";
        $view->_tmp_ext_subtotal = "";
        $view->_tmp_html_before_buttons = "";
        $view->_tmp_html_after_buttons = "";
        $view->_tmp_ext_html_before_discount = "";
		$view->_tmp_ext_html_after_discount = '';
        $view->_tmp_ext_total = "";
        $view->_tmp_ext_discount_text = '';
        $view->_tmp_ext_discount = '';
        $dispatcher->triggerEvent('onBeforeDisplayCartView', array(&$view));
		$view->display();
        if ($ajax) die();
    }

    function delete(){
        header("Cache-Control: no-cache, must-revalidate");
        $ajax = $this->input->getInt('ajax');
        $number_id = $this->input->getInt('number_id');
        $checkout = JSFactory::getModel('checkout', 'Site');
        $checkout->setMaxStep(2);
        $cart = JSFactory::getModel('cart', 'Site');
        $cart->load();
        $cart->delete($number_id);
        if ($ajax){
            print Helper::getOkMessageJson($cart);
            die();
        }
        $this->setRedirect( Helper::SEFLink($cart->getUrlList(),1,1) );
    }

    function refresh(){
        $ajax = $this->input->getInt('ajax');
        $quantitys = $this->input->getVar('quantity');
        $checkout = JSFactory::getModel('checkout', 'Site');
        $checkout->setMaxStep(2);
        $cart = JSFactory::getModel('cart', 'Site');
        $cart->load();
        $cart->refresh($quantitys);
        if ($ajax){
            print Helper::getOkMessageJson($cart);
            die();
        }
        $this->setRedirect( Helper::SEFLink($cart->getUrlList(),1,1) );
    }

    function discountsave(){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onLoadDiscountSave', array());

        $ajax = $this->input->getInt('ajax');
        $code = $this->input->getVar('rabatt');
		$back_step = $this->input->getInt('back_step');

        $coupon = JSFactory::getTable('coupon');
		$cart = JSFactory::getModel('cart', 'Site');

        if ($coupon->getEnableCode($code)){
            $cart->load();
            $dispatcher->triggerEvent('onBeforeDiscountSave', array(&$coupon, &$cart));
            $cart->setRabatt($coupon->coupon_id, $coupon->coupon_type, $coupon->coupon_value);
            $dispatcher->triggerEvent('onAfterDiscountSave', array(&$coupon, &$cart));
            if ($ajax){
                print Helper::getOkMessageJson($cart);
                die();
            }
            JSError::raiseMessage('', Text::_('JSHOP_RABATT_APPLIED'));
        }else{
            JSError::raiseWarning('', $coupon->error);
            if ($ajax){
                print Helper::getMessageJson();
                die();
            }
        }
		
		if ($back_step) {
			$url = JSFactory::getModel('checkoutStep', 'Site')->getCheckoutUrl((string)$back_step, 1);
		} else {
			$url = Helper::SEFLink($cart->getUrlList(), 1, 1);
		}
        $this->setRedirect($url);
    }

    function clear(){
        $ajax = $this->input->getInt('ajax');
        $cart = JSFactory::getModel('cart', 'Site');
        $cart->load();
        $cart->deleteAll();
        if ($ajax){
            print Helper::getOkMessageJson($cart);
            die();
        }
        $this->setRedirect(Helper::SEFLink($cart->getUrlList(), 1, 1));
    }
}