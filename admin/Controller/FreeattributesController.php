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

class FreeAttributesController extends BaseadminController{
    
    protected $nameModel = 'freeattribut';
    
    function init(){
        \JSHelperAdmin::checkAccessController("freeattributes");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.freeattributes";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
    	$freeattributes = \JSFactory::getModel("freeattribut");    	
        $rows = $freeattributes->getAll($filter_order, $filter_order_Dir);
        $view = $this->getView("freeattributes", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayFreeAttributes', array(&$view));
        $view->displayList();
    }
	
    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
	
        $attribut = \JSFactory::getTable('freeattribut');
        $attribut->load($id);
    
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        \JFilterOutput::objectHTMLSafe($attribut, ENT_QUOTES);		

        $view = $this->getView("freeattributes", 'html');
        $view->setLayout("edit");
        $view->set('attribut', $attribut);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent( 'onBeforeEditFreeAtribut', array(&$view, &$attribut) );
        $view->displayEdit();
	}
	
}