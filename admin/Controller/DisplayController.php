<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\CMS\MVC\Controller\BaseController;

defined( '_JEXEC' ) or die();

class DisplayController extends BaseController{

    function display($cachable = false, $urlparams = false){
        \JSHelperAdmin::checkAccessController("panel");
        \JSHelperAdmin::addSubmenu("");
        
		$view = $this->getView("panel", 'html');
        $view->setLayout("home");
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        \JFactory::getApplication()->triggerEvent('onBeforeDisplayHomePanel', array(&$view));
		$view->displayHome(); 
    }
}
