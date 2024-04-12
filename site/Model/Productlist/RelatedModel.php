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

class RelatedModel extends ListModel{

    function getLoadProducts($filters = [], $order = null, $orderby = null, $limitstart = 0, $limit = 0, $listProductUpdateData = 1){
        $jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();
        $adv_query = $this->default_adv_query;
        $adv_from = $this->default_adv_from;
        $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct($this->getProductListName(), "list", $filters, $adv_query, $adv_from, $adv_result);
        if (!$order){
            $order_query = "ORDER BY ".$jshopConfig->product_related_order_by;
        }else{
            $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);
        }

        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeQueryGetProductList', array($this->getProductListName(), &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
        $query = "SELECT $adv_result FROM `#__jshopping_products_relations` AS relation
                INNER JOIN `#__jshopping_products` AS prod ON relation.product_related_id = prod.product_id
                LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = relation.product_related_id
                LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                $adv_from
                WHERE relation.product_id=".(int)$this->table->product_id." AND cat.category_publish=1 AND prod.product_publish=1 ".$adv_query."
				group by prod.product_id ".$order_query;
        $dispatcher->triggerEvent('onBeforeExeQueryGetProductList', array($this->getProductListName(), &$query, &$filters) );
        if ($limit){
            $db->setQuery($query, $limitstart, $limit);
        }else{
            $db->setQuery($query);
        }
        $products = $db->loadObjectList();
        if ($listProductUpdateData){
            $products = \JSHelper::listProductUpdateData($products, 1);
        }
        return $products;
    }    

    public function getProductListName(){
        return 'related';
    }

}