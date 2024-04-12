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

class PaymentTrxTable extends ShopbaseTable{
    function __construct(&$_db){
        parent::__construct('#__jshopping_payment_trx', 'id', $_db);
    }
}