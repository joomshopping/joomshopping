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

class VendorModel extends ListModel{

    function getCountProductsPerPage(){
        $count = isset($this->table->products_page) ? $this->table->products_page : 0;
        if (!$count){
		    $count = \JSFactory::getConfig()->count_products_to_page;
        }
        return $count;
    }

    function getCountProductsToRow(){
		$count = isset($this->table->products_row) ? $this->table->products_row : 0;
        if (!$count){
		    $count = \JSFactory::getConfig()->count_products_to_row;
        }
        return $count;
    }

    function getProductFieldSorting($order){
        if ($order==4) {
            $order = 1;
        }
        return \JSFactory::getConfig()->sorting_products_field_s_select[$order];
    }

    public function getContext(){
        return "Jshoping.vendor.front.product";
    }

    public function getContextFilter(){
        return "Jshoping.list.front.product.vendor.".$this->table->id;
    }

    public function getStandartFilterListProduct(){
        return array('vendors');
    }

    public function getProductListName(){
        return 'vendor';
    }

}