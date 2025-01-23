<?php
/**
* @version      5.5.4 23.01.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

class Legacywebassetmanager{

    public function registerAndUseStyle($name, $url, $options = []) {
        $doc = Factory::getDocument();
        if (!preg_match('/\?/', $url)){
            $url .= '?'.$doc->getMediaVersion();
        }
        $doc->addStyleSheet($url);
    }

    public function registerAndUseScript($name, $url, $options = []) {
        $doc = Factory::getDocument();
        if (!preg_match('/\?/', $url)){
            $url .= '?'.$doc->getMediaVersion();
        }
        $doc->addScript($url);
    }

    public function addInlineScript($code) {
        $doc = Factory::getDocument();
        $doc->addScriptDeclaration($code);
    }
}