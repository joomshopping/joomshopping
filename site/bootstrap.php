<?php
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.5.6 18.11.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

error_reporting(error_reporting() & ~E_NOTICE);
if (!defined('JPATH_JOOMSHOPPING')) define('JPATH_JOOMSHOPPING', JPATH_ROOT.'/components/com_jshopping');
if (!defined('JPATH_JOOMSHOPPING_ADMIN')) define('JPATH_JOOMSHOPPING_ADMIN', JPATH_ADMINISTRATOR.'/components/com_jshopping');

include_once(JPATH_JOOMSHOPPING."/classmap.php");
include_once(JPATH_JOOMSHOPPING."/payments/payment.php");
include_once(JPATH_JOOMSHOPPING."/shippingform/shippingform.php");
include_once(JPATH_JOOMSHOPPING."/addons/addon_core.php");

Helper::disableStrictMysql();