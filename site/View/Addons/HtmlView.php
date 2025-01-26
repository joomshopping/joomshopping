<?php
/**
* @version      5.5.5 26.01.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\View\Addons;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    public function setVars(array $vars = []) {
        foreach($vars as $k => $v) {
            $this->$k = $v;
        }
        return $this;
    }

}