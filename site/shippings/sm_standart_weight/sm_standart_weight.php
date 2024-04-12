<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;

class sm_standart_weight extends shippingextRoot{
    
    var $version = 2;
    
    function showShippingPriceForm($params, &$shipping_ext_row, &$template){        
        include(dirname(__FILE__)."/shippingpriceform.php");
    }
    
    function showConfigForm($config, &$shipping_ext, &$template){
        include(dirname(__FILE__)."/configform.php");
    }
    
    function getPrices($cart, $params, $prices, &$shipping_ext_row, &$shipping_method_price){
        $weight_sum = $cart->getWeightProducts();
        $sh_price = $shipping_method_price->getPrices("desc");
        foreach($sh_price as $sh_pr){
            if ($weight_sum >= $sh_pr->shipping_weight_from && ($weight_sum <= $sh_pr->shipping_weight_to || $sh_pr->shipping_weight_to==0)) {
                $prices['shipping'] = $sh_pr->shipping_price;
                $prices['package'] = $sh_pr->shipping_package_price;
                break;
            }
        }
    return $prices;
    }
}

?>