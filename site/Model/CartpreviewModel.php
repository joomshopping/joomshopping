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

class CartPreviewModel extends BaseModel{
    
    protected $cart = null;
	protected $checkout_step = 0;
	protected $tax_ext_list = array();
	protected $fullsum = 0;
	protected $display_item_shipping = 0;
    protected $display_item_payment = 0;
    protected $display_item_discount = 1;
    protected $weight = 0;

    public function setCart($cart){
        $this->cart = $cart;
    }
    
    function getCart(){
		if (is_null($this->cart)){
			throw new Exception('Error load jshopCart');
		}
		return $this->cart;
	}
	
	public function setCheckoutStep($step){
        $this->checkout_step = $step;
		$this->loadDisplayItem();
		$this->prepareView();
    }
    
    public function getCheckoutStep(){
        return $this->checkout_step;
    }
	
	public function setDisplayItemDiscount($val){
        $this->display_item_discount = $val;
    }
    
    public function getDisplayItemDiscount(){
        return $this->display_item_discount;
    }
	
    public function getBackUrlShop(){
        $jshopConfig = \JSFactory::getConfig();
        $modelproduct = \JSFactory::getModel('productShop', 'Site');
        $shopurl = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=category', 1);
        if ($jshopConfig->cart_back_to_shop=="product"){
            $endpagebuyproduct = \JSHelper::xhtmlUrl($modelproduct->getEndPageBuy());
        }elseif ($jshopConfig->cart_back_to_shop=="list"){
            $endpagebuyproduct =  \JSHelper::xhtmlUrl($modelproduct->getEndPageList());
        }
        if (isset($endpagebuyproduct) && $endpagebuyproduct){
            $shopurl = $endpagebuyproduct;
        }
        return $shopurl;
    }
    
    public function getCartStaticText(){
        $statictext = \JSFactory::getTable("statictext");
        $tmp = $statictext->loadData("cart");
        return $tmp->text;
    }
    
    public function getUrlCheckout(){
        $jshopConfig = \JSFactory::getConfig();
        if ($jshopConfig->shop_user_guest==1){
            $href_checkout = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2&check_login=1',1, 0, $jshopConfig->use_ssl);
        }else{
            $href_checkout = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2',1, 0, $jshopConfig->use_ssl);
        }
        return $href_checkout;
    }
    
    protected function prepareView(){
		$dispatcher = \JFactory::getApplication();
		$cart = $this->getCart();
		if ($this->checkout_step>0){
			$cart->updateDiscountData();
			$trigger_type = 'SmallCart';
		}else{			
			$trigger_type = ucfirst($cart->type_cart);
		}
		$obj = $this;
		$dispatcher->triggerEvent('onBeforeDisplay'.$trigger_type, array(&$cart, &$obj));
        $this->loadConfigShowWeightOrder();
		$this->loadTaxExt();
		$this->loadFullSum();
    }
	
	public function setDisplayItem($shipping = 0, $payment = 0){
		$this->getCart()->setDisplayItem($shipping, $payment);
        $this->display_item_shipping = $shipping;
        $this->display_item_payment = $payment;		
    }	
	
	public function loadWeight(){
		$this->weight = $this->getCart()->getWeightProducts();
	}
	
	public function loadTaxExt(){
		$this->tax_ext_list = $this->getCart()->getTaxExt($this->display_item_shipping, $this->display_item_discount, $this->display_item_payment);
	}
	
	public function loadFullSum(){
		$this->fullsum = $this->getCart()->getSum($this->display_item_shipping, $this->display_item_discount, $this->display_item_payment);
	}
	
	public function getTaxExt(){
		return $this->tax_ext_list;
	}
	
	public function getProducts(){
		return $this->getCart()->products;
	}

	public function getProductsPrepare($products) {
		foreach($products as &$v) {	
			$v['_tmp_tr_before'] = "";
			$v['_tmp_tr_after'] = "";
			$v['_tmp_img_before'] = "";
			$v['_tmp_img_after'] = "";
			$v['_ext_product_name'] = "";
			$v['_ext_attribute_html'] = "";
			$v['_ext_price_html'] = "";
			$v['not_qty_update'] = "";
			$v['_qty_unit'] = "";
			$v['_ext_price_total_html'] = "";
			$v['not_delete'] = "";
			$v['basicprice'] = "";
		}
		return $products;
	}
	
	
	public function getFullSum(){
		return $this->fullsum;
	}
	
	public function getSubTotal(){
		return $this->getCart()->getPriceProducts();
	}
	
	public function getWeight(){
		return $this->weight;
	}
	
	public function getDiscount(){
		return $this->getCart()->getDiscountShow();
	}
	
	public function getFreeDiscount($include_type = 0){
		return $this->getCart()->getFreeDiscount($include_type);
	}
	
	public function getShowPercentTax(){
		$jshopConfig = \JSFactory::getConfig();
		$show = 0;
        if (count($this->tax_ext_list)>1 || $jshopConfig->show_tax_in_product){
			$show = 1;
		}
		if ($jshopConfig->hide_tax){
			$show = 0;
		}
		return $show;
	}
	
	public function getHideSubtotal(){
		$jshopConfig = \JSFactory::getConfig();
		$step = $this->getCheckoutStep();
		$cart = $this->getCart();
		$tax_list = $this->getTaxExt();
		$hide_subtotal = 0;
        if ($step == 5){
            if (($jshopConfig->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ && $jshopConfig->without_shipping && $cart->getPaymentPrice()==0){
				$hide_subtotal = 1;
			}
        }elseif ($step == 4 && !$jshopConfig->step_4_3) {
            if (($jshopConfig->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ && $cart->getPaymentPrice()==0){
				$hide_subtotal = 1;
			}
        }elseif ($step == 3 && $jshopConfig->step_4_3){
            if (($jshopConfig->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ && $jshopConfig->without_shipping){
				$hide_subtotal = 1;
			}
        }else{
            if (($jshopConfig->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ){
				$hide_subtotal = 1;
			}
        }
		return $hide_subtotal;
	}
	
	public function getCartPayment(){
		$payment_id = $this->getCart()->getPaymentId();
		if ($payment_id){
			$pm_method = \JSFactory::getTable('paymentMethod');            
            $pm_method->load($payment_id);
		}else{
			$pm_method = null;
		}
		return $pm_method;
	}
	
	public function getCartPaymentName(){
		$payment = $this->getCartPayment();
		if (!is_null($payment)){
			return $payment->getName();
		}else{
			return '';
		}
	}
	
	public function getCartShipping(){
		$id = $this->getCart()->getShippingId();
		if ($id){
			$sh_method = \JSFactory::getTable('shippingMethod');            
            $sh_method->load($id);
		}else{
			$sh_method = null;
		}
		return $sh_method;
	}
	
	public function getCartShippingName(){
		$shipping = $this->getCartShipping();
		if (!is_null($shipping)){
			return $shipping->getName();
		}else{
			return '';
		}
	}
	
	public function getPriceItemsShow(){
		$jshopConfig = \JSFactory::getConfig();		
		$cart = $this->getCart();
		$items = array(
			'shipping_price'=>0,
			'shipping_package_price'=>0,
			'payment_price'=>0
		);
		if ($this->display_item_shipping){
			$items['shipping_price'] = 1;
			if ($cart->getPackagePrice()>0 || $jshopConfig->display_null_package_price){
				$items['shipping_package_price'] = 1;
			}
		}
		if ($this->display_item_payment){
			$items['payment_price'] = 1;
		}
		return $items;
	}
	
	public function getTextTotalPrice(){
		$jshopConfig = \JSFactory::getConfig();
		$step = $this->getCheckoutStep();
		$cart = $this->getCart();
		$tax_list = $this->getTaxExt();
		
		$text_total = \JText::_('JSHOP_PRICE_TOTAL');
        if ($step == 5){
            $text_total = \JText::_('JSHOP_ENDTOTAL');
            if (($jshopConfig->show_tax_in_product || $jshopConfig->show_tax_product_in_cart) && (count($tax_list)>0)){
                $text_total = \JText::_('JSHOP_ENDTOTAL_INKL_TAX');
            }
        }
		return $text_total;
	}
	
	protected function loadConfigShowWeightOrder(){
		$jshopConfig = \JSFactory::getConfig();
		$this->loadWeight();
        if ($this->weight==0 && $jshopConfig->hide_weight_in_cart_weight0){
            $jshopConfig->show_weight_order = 0;
        }
	}
	
	protected function loadDisplayItem(){
		$jshopConfig = \JSFactory::getConfig();
		$step = $this->getCheckoutStep();		
		if ($step == 5){		
			$shipping = 1;
			$payment = 1;
        }elseif ($step == 4 && !$jshopConfig->step_4_3) {
            $shipping = 0;
			$payment = 1;
        }elseif ($step == 3 && $jshopConfig->step_4_3){
            $shipping = 1;
			$payment = 0;
        }else{
            $shipping = 0;
			$payment = 0;
        }
		if ($jshopConfig->without_shipping){
			$shipping = 0;
		}
		if ($jshopConfig->without_payment){
			$payment = 0;
		}
		$this->setDisplayItem($shipping, $payment);
	}
        
}