<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
defined('_JEXEC') or die;

class UnitsController extends BaseadminController{

    function init(){
        \JSHelperAdmin::checkAccessController("units");
        \JSHelperAdmin::addSubmenu("other");
    }

	function display($cachable = false, $urlparams = false){		
		$rows = \JSFactory::getModel("units")->getUnits();

		$view = $this->getView("units", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();        
        \JFactory::getApplication()->triggerEvent('onBeforeDisplayUnits', array(&$view));
		$view->displayList();
	}

    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
        $units = \JSFactory::getTable('unit');
        $units->load($id);
        $edit = ($id)?(1):(0);
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        if (!$units->qty){
            $units->qty = 1;
        }

        \JFilterOutput::objectHTMLSafe( $units, ENT_QUOTES);

		$view = $this->getView("units", 'html');
        $view->setLayout("edit");
        $view->set('units', $units);
        $view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditUnitss', array(&$view));
		$view->displayEdit();
	}

}