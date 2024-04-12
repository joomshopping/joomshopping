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

class CountryTable extends MultilangTable{
    
    public $ordering = null;

    function __construct(&$_db){
        parent::__construct('#__jshopping_countries', 'country_id', $_db);
    }

    function getAllCountries($publish = 1){
        $db = \JFactory::getDBO(); 
        $lang = \JSFactory::getLang();
        $JshopConfig = \JSFactory::getConfig();
        $where = ($publish)?(" WHERE country_publish = '1' "):(" ");
        $ordering = "ordering";
        if ($JshopConfig->sorting_country_in_alphabet) $ordering = "name";
        $query = "SELECT country_id, `".$lang->get("name")."` as name FROM `#__jshopping_countries` ".$where." ORDER BY ".$ordering;
        $db->setQuery($query);
        return $db->loadObJectList();
    }
	
	public function getCountryIdFromCode2($code2){
		$db = \JFactory::getDbo();			
		$query = "select country_id from `#__jshopping_countries` where country_code_2='".$db->escape($code2)."'";
		$db->setQuery($query);
        return $db->loadResult();
	}
	
	public function getCountryIdFromCode($code){
		$db = \JFactory::getDbo();			
		$query = "select country_id from `#__jshopping_countries` where country_code='".$db->escape($code)."'";
		$db->setQuery($query);
        return $db->loadResult();
	}

}