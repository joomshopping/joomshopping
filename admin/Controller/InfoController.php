<?php
/**
* @version      5.2.1 15.09.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die();

class InfoController extends BaseController{

    function display($cachable = false, $urlparams = false){
        HelperAdmin::checkAccessController("info");
        HelperAdmin::addSubmenu("info");
        
        $jshopConfig = JSFactory::getConfig();
        $data = Installer::parseXMLInstallFile($jshopConfig->admin_path."jshopping.xml");		
        $view = $this->getView("panel", 'html');
        $view->setLayout("info");
		$view->set("data",$data);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = 1;
        Factory::getApplication()->triggerEvent('onBeforeDisplayInfo', array(&$view));
        $view->displayInfo();
    }

}