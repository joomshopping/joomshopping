<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die();

class ShippingMethodPriceTable extends ShopbaseTable{

    function __construct( &$_db ){
        parent::__construct( '#__jshopping_shipping_method_price', 'sh_pr_method_id', $_db );
    }
    
	function getPricesWeight($sh_pr_method_id, $id_country, &$cart){
        $db = \JFactory::getDBO();
        $JshopConfig = \JSFactory::getConfig();

        $query = "SELECT (sh_pr_weight.shipping_price + sh_pr_weight.shipping_package_price) AS shipping_price, sh_pr_weight.shipping_weight_from, sh_pr_weight.shipping_weight_to, sh_price.shipping_tax_id
                  FROM `#__jshopping_shipping_method_price` AS sh_price
                  INNER JOIN `#__jshopping_shipping_method_price_weight` AS sh_pr_weight ON sh_pr_weight.sh_pr_method_id = sh_price.sh_pr_method_id
                  INNER JOIN `#__jshopping_shipping_method_price_countries` AS sh_pr_countr ON sh_pr_weight.sh_pr_method_id = sh_pr_countr.sh_pr_method_id
                  WHERE sh_price.sh_pr_method_id = '" . $db->escape($sh_pr_method_id) . "'AND sh_pr_countr.country_id = '" . $db->escape($id_country) . "' 
                  ORDER BY sh_pr_weight.shipping_weight_from";
        $db->setQuery($query);
        $list = $db->loadObJectList();
        foreach($list as $k=>$v){
            $list[$k]->shipping_price = $list[$k]->shipping_price * $JshopConfig->currency_value;            
            $list[$k]->shipping_price = \JSHelper::getPriceCalcParamsTax($list[$k]->shipping_price, $list[$k]->shipping_tax_id, $cart->products);
        }
        return $list; 
    }

    function getPrices($orderdir = "asc") {
        $db = \JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_shipping_method_price_weight` AS sh_price
                  WHERE sh_price.sh_pr_method_id = '" . $db->escape($this->sh_pr_method_id) . "'
                  ORDER BY sh_price.shipping_weight_from ".$orderdir;
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    function getCountries() {
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT sh_country.country_id, countries.`".$lang->get('name')."` as name
                  FROM `#__jshopping_shipping_method_price_countries` AS sh_country
                  INNER JOIN `#__jshopping_countries` AS countries ON countries.country_id = sh_country.country_id
                  WHERE sh_country.sh_pr_method_id = '" . $db->escape($this->sh_pr_method_id) . "'";
        $db->setQuery($query);        
        return $db->loadObJectList();
    }

    function getTax(){        
        $taxes = \JSFactory::getAllTaxes();        
        return $taxes[$this->shipping_tax_id];
    }
    
    function getTaxPackage(){
        $taxes = \JSFactory::getAllTaxes();
        return $taxes[$this->package_tax_id];
    }
    
    function getGlobalConfigPriceNull($cart){
        $JshopConfig = \JSFactory::getConfig();
        if ($JshopConfig->free_shipping_calc_from_total_and_discount){
            $total = $cart->getSum(0, 1);
        }else{
            $total = $cart->getSum();
        }        
        return ($total >= ($JshopConfig->summ_null_shipping * $JshopConfig->currency_value) && $JshopConfig->summ_null_shipping > 0);
    }

    function calculateSum(&$cart){
        $JshopConfig = \JSFactory::getConfig();
        if ($this->getGlobalConfigPriceNull($cart)){
            return 0;
        }

        $price = $this->shipping_stand_price;
        $package = $this->package_stand_price;
        $prices = array('shipping'=>$price,'package'=>$package);

        $extensions = \JSFactory::getShippingExtList($this->shipping_method_id);
        foreach($extensions as $extension){
            if (isset($extension->exec->version) && $extension->exec->version==2){
                $prices = $extension->exec->getPrices($cart, $this->getParams(), $prices, $extension, $this);
                $price = $prices['shipping'];
            }else{
                $price = $extension->exec->getPrice($cart, $this->getParams(), $price, $extension, $this);
                $prices = array('shipping'=>$price,'package'=>$package);
            }
        }

        $prices['shipping'] = floatval($prices['shipping']) * $JshopConfig->currency_value;
        $prices['shipping'] = \JSHelper::getPriceCalcParamsTax($prices['shipping'], $this->shipping_tax_id, $cart->products);
        $prices['package'] = floatval($prices['package']) * $JshopConfig->currency_value;
        $prices['package'] = \JSHelper::getPriceCalcParamsTax($prices['package'], $this->package_tax_id, $cart->products);
    return $prices;
    }

    function calculateTax($sum){
        $JshopConfig = \JSFactory::getConfig();
        $pricetax = \JSHelper::getPriceTaxValue($sum, $this->getTax(), $JshopConfig->display_price_front_current);
        return $pricetax;
    }
    function calculateTaxPackage($sum){
        $JshopConfig = \JSFactory::getConfig();
        $pricetax = \JSHelper::getPriceTaxValue($sum, $this->getTaxPackage(), $JshopConfig->display_price_front_current);
        return $pricetax;
    }
    
    function getShipingPriceForTaxes($price, $cart){
        if ($this->shipping_tax_id==-1){
            $prodtaxes = \JSHelper::getPriceTaxRatioForProducts($cart->products);
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
    
    function getPackegePriceForTaxes($price, $cart){
        if ($this->package_tax_id==-1){
            $prodtaxes = \JSHelper::getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[$k] = $price*$v;
            }
        }else{
            $prices = array();
            $prices[$this->getTaxPackage()] = $price;
        }
    return $prices;
    }

    function calculateShippingTaxList($price, $cart){
        $JshopConfig = \JSFactory::getConfig();
        if ($this->shipping_tax_id==-1){
            $prodtaxes = \JSHelper::getPriceTaxRatioForProducts($cart->products);
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
    
    function calculatePackageTaxList($price, $cart){
        $JshopConfig = \JSFactory::getConfig();
        if ($this->package_tax_id==-1){
            $prodtaxes = \JSHelper::getPriceTaxRatioForProducts($cart->products);
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
            $taxes[$this->getTaxPackage()] = $this->calculateTaxPackage($price);
        }
    return $taxes;
    }
    
    function isCorrectMethodForCountry($id_country) {
        $db = \JFactory::getDBO();
        $query = "SELECT `sh_method_country_id` FROM `#__jshopping_shipping_method_price_countries` WHERE `country_id` = '".$db->escape($id_country)."' AND `sh_pr_method_id` = '".$db->escape($this->sh_pr_method_id)."'";
        $db->setQuery($query);
        $db->execute();
        return ($db->getNumRows())?(1):(0);
    }
    
    function setParams($params){
        $this->params = serialize((array)$params);
    }
    
    function getParams(){
        if ($this->params==""){
            return array();
        }else{
            return (array)unserialize($this->params);
        }
    }

}