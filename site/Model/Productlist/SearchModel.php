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

class SearchModel extends ListModel{

    function getCountProductsPerPage(){
        return JSFactory::getConfig()->count_products_to_page_search;
    }

    function getCountProductsToRow(){
        return JSFactory::getConfig()->count_products_to_row_search;
    }

    function getProductFieldSorting($order){
        if ($order==4){
            $order = 1;
        }
        return JSFactory::getConfig()->sorting_products_field_s_select[$order];
    }

    public function getContext(){
        return "jshoping.searchlist.front.product";
    }

    public function getContextFilter(){
        $context = "jshoping.searchlist.front.product";
        $obj = $this;
        Factory::getApplication()->triggerEvent('onSearchGetContextFilter', array(&$context, &$obj));
        return $context;
    }

    public function getProductListName(){
        return 'search';
    }

	public function getFilterListProduct(){
		return JSFactory::getModel('filtersearch', 'Site\\Productlist')->getFilter($this->getContextFilter(), $this->getNoFilterListProduct());
	}

}