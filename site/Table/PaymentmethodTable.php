<?php
/**
* @version      5.4.0 09.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

class PaymentMethodTable extends MultilangTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_payment_method', 'payment_id', $_db);
        PluginHelper::importPlugin('jshoppingcheckout');
    }

	function loadFromClass($class, $use_scriptname = 0){
        $db = Factory::getDBO();
		$field = 'payment_class';
		if ($use_scriptname) {
			$field = 'scriptname';
		}
        $query = "SELECT payment_id FROM `#__jshopping_payment_method` WHERE ".$field."='".$db->escape($class)."'";
        extract(Helper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $id = $db->loadResult();
        return $this->load($id);
	}

    function getAllPaymentMethods($publish = 1, $shipping_id = 0){
        $db = Factory::getDBO();
        $JshopConfig = JSFactory::getConfig();
        $user = Factory::getUser();        
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $query_where = 'WHERE `access` IN ('.$groups.')';
        if ($publish) {
            $query_where .= " AND payment_publish=1 ";
        }
        $lang = JSFactory::getLang();
        $query = "SELECT payment_id, `".$lang->get("name")."` as name, `".$lang->get("description")."` as description , payment_code, payment_class, scriptname, payment_publish, payment_ordering, payment_params, payment_type, price, price_type, tax_id, image FROM `#__jshopping_payment_method` $query_where ORDER BY payment_ordering";
        extract(Helper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $rows = $db->loadObJectList();
        if ($shipping_id && $JshopConfig->step_4_3){
            $sh = JSFactory::getTable('shippingMethod');            
            $sh->load($shipping_id);
            $payments = $sh->getPayments();
            if (count($payments)>0){
                foreach($rows as $k=>$v){
                    if (!in_array($v->payment_id, $payments)) unset($rows[$k]);
                }
                $rows = array_values($rows);
            }
        }
        foreach($rows as $k=>$v){
            $rows[$k]->pmconfig = (array)json_decode($v->payment_params, true);
        }
    return $rows;
    }

    /**
    * get id payment for payment_class
    */
    function getId(){
        $db = Factory::getDBO();
        $query = "SELECT payment_id FROM `#__jshopping_payment_method` WHERE payment_class = '".$db->escape($this->class)."'";
        extract(Helper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function setCart(&$cart){
        $this->_cart = $cart;
    }
    
    function getCart(){
        return $this->_cart;
    }
    
    function getPrice(){
        $JshopConfig = JSFactory::getConfig();
        if ($this->price_type==2){
            $cart = $this->getCart();
            $price = $cart->getSummForCalculePlusPayment() * $this->price / 100;
            if ($JshopConfig->display_price_front_current){
                $price = Helper::getPriceCalcParamsTax($price, $this->tax_id, $cart->products);
            }
        }else{
            $cart = $this->getCart();
            $price = $this->price * $JshopConfig->currency_value; 
            $price = Helper::getPriceCalcParamsTax($price, $this->tax_id, $cart->products);
        }
        $dispatcher = Factory::getApplication();
        $obj = $this;
        $dispatcher->triggerEvent('onAfterGetPricePaymant', array(&$obj, &$price));        
        return $price;
    }
    
    function getTax(){        
        $taxes = JSFactory::getAllTaxes();        
        return $taxes[$this->tax_id] ?? 0;
    }
    
    function calculateTax($price = 0){
        $JshopConfig = JSFactory::getConfig();
        if (!$price){
            $price = $this->getPrice();
        }
        $pricetax = Helper::getPriceTaxValue($price, $this->getTax(), $JshopConfig->display_price_front_current);
        return $pricetax;
    }
    
    function getPriceForTaxes($price){
        if ($this->tax_id==-1){
            $cart = $this->getCart();
            $prodtaxes = Helper::getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[$k] = $price*$v;
            }
        }else{
            $prices = array();
            $prices[$this->getTax()] = $price;
        }
    return $prices;
    }
    
    function calculateTaxList($price){
        $cart = $this->getCart();
        $JshopConfig = JSFactory::getConfig();
        if ($this->tax_id==-1){
            $prodtaxes = Helper::getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[] = array('tax'=>$k, 'price'=>$price*$v);
            }
            $taxes = array();
            if ($JshopConfig->display_price_front_current==0){
                foreach($prices as $v){
                    $taxes[$v['tax']] = $v['price']*$v['tax']/(100+$v['tax']);
                }
            }else{
                foreach($prices as $v){
                    $taxes[$v['tax']] = $v['price']*$v['tax']/100;
                }
            }    
        }else{
            $taxes = array();
            $taxes[$this->getTax()] = $this->calculateTax($price);
        }        
    return $taxes;
    }
    
    /**
    * static
    * get config payment for classname
    */
    function getConfigsForClassName($classname) {
        $db = Factory::getDBO(); 
        $query = "SELECT payment_params FROM `#__jshopping_payment_method` WHERE payment_class = '".$db->escape($classname)."'";
        extract(Helper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $params_str = $db->loadResult();
        return (array)json_decode($params_str, true);
    }
    
    /**
    * get config    
    */
    function getConfigs(){
        return (array)json_decode($this->payment_params ?? '', true);
    }
    
    function setConfigs($data){
        $this->payment_params = json_encode((array)$data);
    }
    
    function check(){
        if ($this->payment_class==""){
            $this->setError("Alias Empty");
            return 0;
        }
        return 1;
    }
	
    function getPaymentSystemData($script=''){
        $JshopConfig = JSFactory::getConfig();
        if ($script==''){
            if ($this->scriptname!=''){
                $script = $this->scriptname;
            }else{
                $script = $this->payment_class;
            }
        }else{
            $script = str_replace(array('.','/'),'', $script);
        }
        $data = new \stdClass();
        
        if (!file_exists($JshopConfig->path.'payments/'.$script."/".$script.'.php')){
            $data->paymentSystemVerySimple = 1;
            $data->paymentSystem = null;
        }else{
            include_once($JshopConfig->path.'payments/'.$script."/".$script.'.php');
            if (!class_exists($script)){
                throw new Exception('Error loading class '.$script);
            }else{
                $data->paymentSystemVerySimple = 0;
                $data->paymentSystem = new $script();
                $data->paymentSystem->setPmMethod($this);
            }
        }
    return $data;
    }
    
    function loadPaymentForm($payment_system, $params, $pmconfig){
        ob_start();
        $payment_system->showPaymentForm($params, $pmconfig);
        $html = ob_get_contents();
        ob_get_clean();
        return $html;
    }
    
    public function reorder($where = '',  $fieldordering = 'ordering'){
    }

}