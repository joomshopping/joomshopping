<?php
/**
* @version      5.10.0 27.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;
use Joomla\CMS\Uri\Uri;
defined('_JEXEC') or die();

class JSUri extends Uri{

	public static function isInternal($url){
		// browsers treat "/\host" as "//host"
		$url = str_replace('\\', '/', (string)$url);
		if (preg_match('/[\x00-\x1F\x7F]/', $url)){
			return false;
		}
		$uri = static::getInstance($url);
		$base = $uri->toString(array('scheme', 'host', 'port', 'path'));
		$host = $uri->toString(array('scheme', 'host', 'port'));

		if (stripos($base, static::base()) !== 0 && !empty($host)){
			return false;
		}
		return true;
	}

}