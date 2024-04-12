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

class SeoTable extends ShopbaseTable{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_config_seo', 'id', $_db);
    }
    
    function loadData($alias){
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        $query = "SELECT id, alias, `".$lang->get('title')."` as title, `".$lang->get('keyword')."` as keyword, `".$lang->get('description')."` as description FROM `#__jshopping_config_seo` where alias='".$db->escape($alias)."'";
        $db->setQuery($query);
		$data = $db->loadObJect();
		if (!isset($data)){
            $data = new \stdClass();
            $data->title = '';
            $data->keyword = '';
            $data->description = '';
        }
	return $data;
    }
    
}