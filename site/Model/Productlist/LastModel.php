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

class LastModel extends ListModel{
    
    function getBuildQueryOrderListProduct($order, $orderby, &$adv_from){
		if (!$order){
			return 'ORDER BY '.\JSFactory::getConfig()->get_last_products_order_query.' DESC';
		}else{
			return parent::getBuildQueryOrderListProduct($order, $orderby, $adv_from);
		}
    }
        
    function getCountProductsPerPage(){       
        return \JSFactory::getConfig()->count_products_to_page_last;
    }
    
    function getCountProductsToRow(){
        return \JSFactory::getConfig()->count_products_to_row_last;
    }
    
    function getProductFieldSorting($order){
        return '';
    }
    
    public function getContext(){
        return "jshoping.list.front.product.last";
    }
    
    public function getContextFilter(){
        return "jshoping.list.front.product.last";
    }
    
    public function getProductListName(){
        return 'last';
    }
        
}