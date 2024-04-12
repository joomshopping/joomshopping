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

class BestsellerModel extends ListModel{
    
    function getLoadProducts($filters = [], $order = null, $orderby = null, $limitstart = 0, $limit = 0, $listProductUpdateData = 1){
        $db = \JFactory::getDBO();
        $adv_query = $this->default_adv_query;
        $adv_from = $this->default_adv_from;
        $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct($this->getProductListName(), "list", $filters, $adv_query, $adv_from, $adv_result);
		if ($order=='max_num'){
			$orderby = 'DESC';
		}
		$order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);

        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeQueryGetProductList', array($this->getProductListName(), &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );

        $query = "SELECT SUM(OI.product_quantity) as max_num, $adv_result FROM #__jshopping_order_item AS OI
                  INNER JOIN `#__jshopping_products` AS prod   ON prod.product_id=OI.product_id
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish=1 AND cat.category_publish=1 ".$adv_query."
                  GROUP BY prod.product_id ".$order_query;
        $dispatcher->triggerEvent('onBeforeExeQueryGetProductList', array($this->getProductListName(), &$query, &$filters) );
        if ($limit){
            $db->setQuery($query, $limitstart, $limit);
        }else{
            $db->setQuery($query);
        }
        $products = $db->loadObJectList();
        if ($listProductUpdateData){
            $products = \JSHelper::listProductUpdateData($products, 1);
        }
        return $products;
    }
        
    function getCountProductsPerPage(){       
        return \JSFactory::getConfig()->count_products_to_page_bestseller;
    }
    
    function getCountProductsToRow(){
        return \JSFactory::getConfig()->count_products_to_row_bestseller;
    }
    
    function getProductFieldSorting($order){
        return 'max_num';
    }
    
    public function getContext(){
        return "jshoping.list.front.product.bestseller";
    }
    
    public function getContextFilter(){
        return "jshoping.list.front.product.bestseller";
    }
    
    public function getProductListName(){
        return 'bestseller';
    }	
    
}