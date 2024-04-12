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

class CategoryModel extends ListModel{

    function getCountProductsPerPage(){
        return $this->table->products_page;
    }

    function getCountProductsToRow(){
        return $this->table->products_row;
    }

    function getProductFieldSorting($order){
        return \JSFactory::getConfig()->sorting_products_field_select[$order];
    }

    public function getContext(){
        return "jshoping.list.front.product";
    }

    public function getContextFilter(){
        return "jshoping.list.front.product.cat.".$this->table->category_id;
    }

    public function getStandartFilterListProduct(){
        return array('categorys');
    }

    public function getProductListName(){
        return 'category';
    }

    public function getProductsOrderingTypeList(){
        return 1;
    }

}