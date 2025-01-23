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

class LabelModel extends ListModel{

    public $default_adv_query = " AND prod.label_id!=0 ";
    
    function getCountProductsPerPage(){       
        return JSFactory::getConfig()->count_products_to_page_label;
    }
    
    function getCountProductsToRow(){
        return JSFactory::getConfig()->count_products_to_row_label;
    }
    
    function getProductFieldSorting($order){
        if ($order==4){
            $order = 1;
        }
        return JSFactory::getConfig()->sorting_products_field_s_select[$order];
    }
    
    public function getContext(){
        return "jshoping.list.front.product.label";
    }
    
    public function getContextFilter(){
        $context = "jshoping.list.front.product.label";
        $obj = $this;
        Factory::getApplication()->triggerEvent('onGetContextFilter', array(&$context, &$obj));
        return $context;
    }
    
    public function getProductListName(){
        return 'label';
    }

}