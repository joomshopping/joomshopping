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

class ProductLabelsController extends BaseadminController{
    
    protected $modelSaveItemFileName = 'image';
    
    function init(){
        \JSHelperAdmin::checkAccessController("productlabels");
        \JSHelperAdmin::addSubmenu("other");
    }

	function display($cachable = false, $urlparams = false){
        $jshopConfig = \JSFactory::getConfig();
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.productlabels";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "name", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
		$_productLabels = \JSFactory::getModel("productlabels");
		$rows = $_productLabels->getList($filter_order, $filter_order_Dir);
        
		$view = $this->getView("product_labels", 'html');
        $view->setLayout("list");		
        $view->set('rows', $rows);
        $view->set('config', $jshopConfig);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductLabels', array(&$view));		
		$view->displayList();
	}
	
	function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $jshopConfig = \JSFactory::getConfig();
		$id = $this->input->getInt("id");
		$productLabel = \JSFactory::getTable('productlabel');
		$productLabel->load($id);
		$edit = ($id)?(1):(0);
		$_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        \JFilterOutput::objectHTMLSafe($productLabel, ENT_QUOTES);

		$view = $this->getView("product_labels", 'html');
        $view->setLayout("edit");
        $view->set('productLabel', $productLabel);
        $view->set('config', $jshopConfig);
        $view->set('edit', $edit);
		$view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditProductLabels', array(&$view));
		$view->displayEdit();
	}
    
    function delete_foto(){
        $jshopConfig = \JSFactory::getConfig();
        $id = $this->input->getInt("id");
        $productLabel = \JSFactory::getTable('productlabel');
        $productLabel->load($id);
        @unlink($jshopConfig->image_labels_path."/".$productLabel->image);
        $productLabel->image = "";
        $productLabel->store();
        die();               
    }

}