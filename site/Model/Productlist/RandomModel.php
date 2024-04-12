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

class RandomModel extends ListModel{

    function getLoadProducts($filters = [], $order = null, $orderby = null, $limitstart = 0, $limit = 0, $listProductUpdateData = 1){
        $db = \JFactory::getDBO();
        $adv_query = $this->default_adv_query;
        $adv_from = $this->default_adv_from;
        $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct($this->getProductListName(), "list", $filters, $adv_query, $adv_from, $adv_result);
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);

        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeQueryGetProductList', array($this->getProductListName(), &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );

        $query = "SELECT count(distinct prod.product_id) FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish=1 AND cat.category_publish=1 ".$adv_query;
        $db->setQuery($query);
        $count = $limit;
        $totalrow = $db->loadResult();
        $totalrow = $totalrow - $count;
        if ($totalrow < 0) $totalrow = 0;
        $limitstart = rand(0, $totalrow);

        $order = array();
        $order[] = "name asc";
        $order[] = "name desc";
        $order[] = "prod.product_price asc";
        $order[] = "prod.product_price desc";
        $orderby = $order[rand(0,3)];

        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish=1 AND cat.category_publish=1 ".$adv_query."
                  GROUP BY prod.product_id order by ".$orderby."
				  LIMIT ".$limitstart.", ".$count;
        $db->setQuery($query);
        $products = $db->loadObJectList();
        if ($listProductUpdateData){
            $products = \JSHelper::listProductUpdateData($products, 1);
        }
        return $products;
    }
        
    function getCountProductsPerPage(){       
        return \JSFactory::getConfig()->count_products_to_page_random;
    }
    
    function getCountProductsToRow(){
        return \JSFactory::getConfig()->count_products_to_row_random;
    }
    
    function getProductFieldSorting($order){
        if ($order==4){
            $order = 1;
        }
        return \JSFactory::getConfig()->sorting_products_field_s_select[$order];
    }
    
    public function getContext(){
        return "jshoping.list.front.product.random";
    }
    
    public function getContextFilter(){
        return "jshoping.list.front.product.random";
    }
    
    public function getProductListName(){
        return 'random';
    }
        
}