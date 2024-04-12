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

defined('_JEXEC') or die();

class InfoController extends BaseController{

    function display($cachable = false, $urlparams = false){
        \JSHelperAdmin::checkAccessController("info");
        \JSHelperAdmin::addSubmenu("info");
        
        $jshopConfig = \JSFactory::getConfig();
        $data = \JInstaller::parseXMLInstallFile($jshopConfig->admin_path."jshopping.xml");
		if ($jshopConfig->display_updates_version){
		    $update_model = \JSFactory::getModel("info");
		    $update = $update_model->getUpdateObj($data['version'], $jshopConfig);
        }else{
            $update = new \stdClass();
        }
        $view = $this->getView("panel", 'html');
        $view->setLayout("info");
		$view->set("data",$data);
        $view->set("update",$update);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        \JFactory::getApplication()->triggerEvent('onBeforeDisplayInfo', array(&$view));
        $view->displayInfo();
    }

}