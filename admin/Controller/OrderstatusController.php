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

class OrderStatusController extends BaseadminController{
    
    protected $urlEditParamId = 'status_id';
    
    function init(){
        \JSHelperAdmin::checkAccessController("orderstatus");
        \JSHelperAdmin::addSubmenu("other");
    }

	function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.orderstatus";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "status_id", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
		$_order = \JSFactory::getModel("orders");
		$rows = $_order->getAllOrderStatus($filter_order, $filter_order_Dir);

		$view = $this->getView("orderstatus", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";          
		$view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayOrderStatus', array(&$view));
		$view->displayList();
	}
	
	function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
		$status_id = $this->input->getInt("status_id");
		$order_status = \JSFactory::getTable('orderstatus');
		$order_status->load($status_id);
		$edit = ($status_id)?($edit = 1):($edit = 0);
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        \JFilterOutput::objectHTMLSafe($order_status, ENT_QUOTES);

		$view = $this->getView("orderstatus", 'html');
        $view->setLayout("edit");		
        $view->set('order_status', $order_status);        
        $view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('etemplatevar', '');
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditOrderStatus', array(&$view));
		$view->displayEdit();
	}

}