<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model\Productlist;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
defined('_JEXEC') or die();

class TophitsModel extends ListModel{

    function getBuildQueryOrderListProduct($order, $orderby, &$adv_from){
		if (!$order){
			return 'ORDER BY prod.hits DESC';
		}else{
			return parent::getBuildQueryOrderListProduct($order, $orderby, $adv_from);
		}
    }
    
    function getCountProductsPerPage(){
        return JSFactory::getConfig()->count_products_to_page_tophits;
    }
    
    function getCountProductsToRow(){
        return JSFactory::getConfig()->count_products_to_row_tophits;
    }
    
    function getProductFieldSorting($order){
        return '';
    }
    
    public function getContext(){
        return "jshoping.list.front.product.tophits";
    }
    
    public function getContextFilter(){
        $context = "jshoping.list.front.product.tophits";
        $obj = $this;
        Factory::getApplication()->triggerEvent('onGetContextFilter', array(&$context, &$obj));
        return $context;
    }
    
    public function getProductListName(){
        return 'tophits';
    }
	
}