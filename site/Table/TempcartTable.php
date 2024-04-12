<?php
/**
* @version      5.3.0 23.12.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die();

class TempcartTable extends ShopbaseTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_cart_temp', 'id', $_db);
    }
}