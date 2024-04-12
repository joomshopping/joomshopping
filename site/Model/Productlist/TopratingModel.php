<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model\Productlist;
defined('_JEXEC') or die();

class TopratingModel extends ListModel{
    
    function getBuildQueryOrderListProduct($order, $orderby, &$adv_from){
		if (!$order){
			return 'ORDER BY prod.average_rating DESC';
		}else{
			return parent::getBuildQueryOrderListProduct($order, $orderby, $adv_from);
		}
    }
    
    function getProductFieldSorting($order){
        return '';
    }
    
    public function getContext(){
        return "jshoping.list.front.product.toprating";
    }
    
    public function getContextFilter(){
        return "jshoping.list.front.product.toprating";
    }
    
    public function getProductListName(){
        return 'toprating';
    }
    	
}