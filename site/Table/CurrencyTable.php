<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;

defined('_JEXEC') or die();


class CurrencyTable extends ShopbaseTable{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_currencies', 'currency_id', $_db);
    }

	function getAllCurrencies($publish = 1) {
		$db = \JFactory::getDBO();
		$query_where = ($publish)?("WHERE currency_publish = '1'"):("");
		$query = "SELECT currency_id, currency_name, currency_code, currency_code_iso, currency_value FROM `#__jshopping_currencies` $query_where ORDER BY currency_ordering";
		$db->setQuery($query);
		return $db->loadObJectList();
	}
    
}