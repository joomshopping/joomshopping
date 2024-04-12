<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
defined('_JEXEC') or die();
include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/iecontroller.php");

class ImportExportController extends BaseadminController{
    
    function init(){
        \JSHelperAdmin::checkAccessController("importexport");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        if ($this->getTask()!="" && $this->getTask()!="backtolistie" && $this->input->getInt("ie_id")){
            $this->view();
            return 1;
        }
    	$importexport = \JSFactory::getModel("importexport");    	
        
		$rows = $importexport->getList();		
        $view = $this->getView("import_export_list", 'html');
		$view->set('rows', $rows);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayImportExport', array(&$view));
        $view->display();
    }
    
    function remove() {        
        $cid = $this->input->getInt("cid");        
        $_importexport = \JSFactory::getTable('Importexport');
        $_importexport->load($cid);        
        $_importexport->delete();        
        $this->setRedirect('index.php?option=com_jshopping&controller=importexport', \JText::_('JSHOP_ITEM_DELETED'));
    }
    
    function setautomaticexecution(){
        $cid = $this->input->getInt("cid");        
        $_importexport = \JSFactory::getTable('Importexport');
        $_importexport->load($cid);
        if ($_importexport->steptime > 0){
            $_importexport->steptime = 0;
        }else{
            $_importexport->steptime = 1;
        }
        $_importexport->store();
        $this->setRedirect('index.php?option=com_jshopping&controller=importexport');
    }
    
    function view(){
        $ie_id = $this->input->getInt("ie_id");
        $_importexport = \JSFactory::getTable('Importexport');
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php")){
            \JSError::raiseWarning("", sprintf(\JText::_('JSHOP_ERROR_FILE_NOT_EXIST'), "/importexport/".$alias."/".$alias.".php"));
            return 0;
        }
        
        include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php");
        
        $classname = 'Ie'.$alias;
        $controller = new $classname(array(
            'ie_id' => $ie_id,
            'alias' => $alias,
            'params' => $_importexport->get('params')
        ));
        $controller->execute( $this->input->getVar('task') );
    }

    public function save(){
        $this->display();
    }
		      
}