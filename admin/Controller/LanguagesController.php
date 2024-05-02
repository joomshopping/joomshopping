<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
defined( '_JEXEC' ) or die();

class LanguagesController extends BaseadminController{
    
    function init(){
        HelperAdmin::checkAccessController("languages");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $languages = JSFactory::getModel("languages");
        $rows = $languages->getAllLanguages(0);
        $jshopConfig = JSFactory::getConfig();        
                
		$view = $this->getView("languages_list", 'html');
        $view->set('rows', $rows);
        $view->set('default_front', $jshopConfig->getFrontLang());
        $view->set('defaultLanguage', $jshopConfig->defaultLanguage);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayLanguage', array(&$view));
		$view->display();
    }
        
}