<?php
/**
* @version      5.3.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
defined('_JEXEC') or die();

class ProductPriceTable extends ShopbaseTable{
	
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_products_prices', 'price_id', $_db );
    }
    
    function getAddPrices($product_id){        
        $db = Factory::getDBO();
        $query = "SELECT * FROM `#__jshopping_products_prices` WHERE product_id=".$db->q($product_id)." ORDER BY product_quantity_start";
        $db->setQuery($query);
        $rows = $db->loadObJectList();
		extract(Helper::Js_add_trigger(get_defined_vars(), "after"));
		return $rows;
    }

}