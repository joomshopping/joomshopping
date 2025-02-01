<?php
/**
* @version      5.3.5 26.03.2024
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

class LogsController extends BaseadminController{
    
    function init(){
        HelperAdmin::checkAccessController("logs");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $model = JSFactory::getModel("logs");
        $rows = $model->getList();
        
		$view = $this->getView("logs", 'html');
        $view->setLayout("list");	
        $view->rows = $rows;
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayLogs', array(&$view));
		$view->displayList();
    }
    
    function edit(){
        $id = $this->input->getVar('id');
        $filename = str_replace(array('..', '/', ':'), '', $id);
        $model = JSFactory::getModel("logs");
        $data = $model->read($filename);        
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                
        $view = $this->getView("logs", 'html');
        $view->setLayout("edit");        
        $view->set('filename', $filename);                
        $view->set('data', $data);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditLogs', array(&$view));
        $view->displayEdit();
    }

    function download(){
        $id = $this->input->getVar('id');
        $filename = str_replace(array('..', '/', ':'), '', $id);
        $model = JSFactory::getModel("logs");
        $model->download($filename);
        die();
    }
}