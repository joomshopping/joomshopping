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

class OtherController extends BaseController{

    function display($cachable = false, $urlparams = false){
        \JSHelperAdmin::checkAccessController("other");
        \JSHelperAdmin::addSubmenu("other");
        $view=$this->getView("panel", 'html');
        $view->setLayout("options");
        $view->sidebar = \JHTMLSidebar::render();
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayOptionsPanel', array(&$view));
        $view->displayOptions();
    }
    
}
