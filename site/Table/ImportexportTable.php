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

class ImportExportTable extends ShopbaseTable{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_import_export', 'id', $_db);
    }
	
	public function getParams(){
		return (array)Json_decode($this->params, 1);
	}
	
	public function setParams(array $params){
		$this->params = Json_encode($params);
	}

}