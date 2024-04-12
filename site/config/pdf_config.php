<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

define('K_TCPDF_EXTERNAL_CONFIG', true);
// Installation path
define("K_PATH_MAIN", JPATH_SITE."/components/com_jshopping/Lib/tcpdf");
// URL path
define("K_PATH_URL", JPATH_SITE);
// Fonts path
define("K_PATH_FONTS", JPATH_SITE."/components/com_jshopping/Lib/tcpdf/fonts/");
// Cache directory path
define("K_PATH_CACHE", K_PATH_MAIN."/cache");
// Cache URL path
define("K_PATH_URL_CACHE", K_PATH_URL."/cache");
// Images path
define("K_PATH_IMAGES", K_PATH_MAIN."/images");
// Blank image path
define("K_BLANK_IMAGE", K_PATH_IMAGES."/_blank.png");

// Cell height ratio
define("K_CELL_HEIGHT_RATIO", 1.5);
// Magnification scale for titles
define("K_TITLE_MAGNIFICATION", 1);
// Reduction scale for small font
define("K_SMALL_RATIO", 2/3);
// Magnication scale for head
define("HEAD_MAGNIFICATION", 1);