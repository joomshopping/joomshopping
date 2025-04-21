<?php
/**
* @version      5.6.1 29.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
defined('_JEXEC') or die();

class ProductLabelsController extends BaseadminController{
    
    protected $modelSaveItemFileName = 'image';
    
    function init(){
        HelperAdmin::checkAccessController("productlabels");
        HelperAdmin::addSubmenu("other");
    }

	function display($cachable = false, $urlparams = false){
        $jshopConfig = JSFactory::getConfig();
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.productlabels";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "name", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $filter = array_filter($app->getUserStateFromRequest($context.'filter', 'filter', [], 'array'));
        
		$_productLabels = JSFactory::getModel("productlabels");
		$rows = $_productLabels->getList($filter_order, $filter_order_Dir, $filter);
        
		$view = $this->getView("product_labels", 'html');
        $view->setLayout("list");		
        $view->set('rows', $rows);
        $view->set('config', $jshopConfig);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->filter = $filter;
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductLabels', array(&$view));		
		$view->displayList();
	}
	
	function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $jshopConfig = JSFactory::getConfig();
		$id = $this->input->getInt("id");
		$productLabel = JSFactory::getTable('productlabel');
		$productLabel->load($id);
		$edit = ($id)?(1):(0);
		$_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        OutputFilter::objectHTMLSafe($productLabel, ENT_QUOTES);

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
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditProductLabels', array(&$view));
		$view->displayEdit();
	}
    
    function delete_foto(){
        $jshopConfig = JSFactory::getConfig();
        $id = $this->input->getInt("id");
        $productLabel = JSFactory::getTable('productlabel');
        $productLabel->load($id);
        @unlink($jshopConfig->image_labels_path."/".$productLabel->image);
        $productLabel->image = "";
        $productLabel->store();
        die();               
    }

}