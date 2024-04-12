<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
defined( '_JEXEC' ) or die();

class LogsController extends BaseadminController{
    
    function init(){
        \JSHelperAdmin::checkAccessController("logs");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $model = \JSFactory::getModel("logs");
        $rows = $model->getList();
        
		$view = $this->getView("logs", 'html');
        $view->setLayout("list");	
        $view->set('rows', $rows);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayLogs', array(&$view));
		$view->displayList();
    }
    
    function edit(){
        $id = $this->input->getVar('id');
        $filename = str_replace(array('..', '/', ':'), '', $id);
        $model = \JSFactory::getModel("logs");
        $data = $model->read($filename);        
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                
        $view = $this->getView("logs", 'html');
        $view->setLayout("edit");        
        $view->set('filename', $filename);                
        $view->set('data', $data);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditLogs', array(&$view));
        $view->displayEdit();
    }
}