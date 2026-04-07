<?php
/**
* @version      5.9.1 07.04.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

class ProductrelationTable extends ShopbaseTable{

    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_relations', 'id', $_db);
    }
}