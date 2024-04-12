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

class ProductModel extends ListModel{

    function getCountProductsPerPage(){
        return \JSFactory::getConfig()->count_products_to_page;
    }

    function getCountProductsToRow(){
        return \JSFactory::getConfig()->count_products_to_row;
    }

    function getProductFieldSorting($order){
        if ($order==4){
            $order = 1;
        }
        return \JSFactory::getConfig()->sorting_products_field_s_select[$order];
    }

    public function getContext(){
        return "jshoping.alllist.front.product";
    }

    public function getContextFilter(){
        return "jshoping.list.front.product.fulllist";
    }

    public function getProductListName(){
        return 'products';
    }


}