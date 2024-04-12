<?php
/**
* @version      5.2.2 18.11.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

error_reporting(error_reporting() & ~E_NOTICE);
define('JPATH_JOOMSHOPPING', JPATH_ROOT.'/components/com_jshopping');
define('JPATH_JOOMSHOPPING_ADMIN', JPATH_ADMINISTRATOR.'/components/com_jshopping');

include_once(JPATH_JOOMSHOPPING."/classmap.php");
include_once(JPATH_JOOMSHOPPING."/payments/payment.php");
include_once(JPATH_JOOMSHOPPING."/shippingform/shippingform.php");
include_once(JPATH_JOOMSHOPPING."/addons/addon_core.php");

\JSHelper::disableStrictMysql();