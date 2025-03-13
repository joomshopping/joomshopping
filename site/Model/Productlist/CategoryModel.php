<?php
/**
* @version      5.6.0 13.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model\Productlist;

use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

class CategoryModel extends ListModel{

    function getCountProductsPerPage(){
        return $this->table->products_page ? $this->table->products_page : JSFactory::getConfig()->count_products_to_page;
    }

    function getCountProductsToRow(){
        return $this->table->products_row ? $this->table->products_row : JSFactory::getConfig()->count_products_to_row;
    }

    function getProductFieldSorting($order){
        return JSFactory::getConfig()->sorting_products_field_select[$order];
    }

    public function getContext(){
        return "jshoping.list.front.product";
    }

    public function getContextFilter(){
        $context = "jshoping.list.front.product.cat.".$this->table->category_id;
        $obj = $this;
        Factory::getApplication()->triggerEvent('onGetContextFilter', array(&$context, &$obj));
        return $context;
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

    function getDefaultProductSorting(){
        $sort = $this->table->product_sorting;
        if (!$sort) {
            $sort = JSFactory::getConfig()->product_sorting;
        }
        return $sort;
    }

    function getDefaultProductSortingDirection(){
        $dir = $this->table->product_sorting_direction;
        if ($dir == -1) {
            $dir = JSFactory::getConfig()->product_sorting_direction;
        }
        return $dir;
    }

}