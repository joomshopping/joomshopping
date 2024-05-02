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
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Filter\OutputFilter;
defined('_JEXEC') or die();

class OrderStatusController extends BaseadminController{
    
    protected $urlEditParamId = 'status_id';
    
    function init(){
        HelperAdmin::checkAccessController("orderstatus");
        HelperAdmin::addSubmenu("other");
    }

	function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.orderstatus";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "status_id", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
		$_order = JSFactory::getModel("orders");
		$rows = $_order->getAllOrderStatus($filter_order, $filter_order_Dir);

		$view = $this->getView("orderstatus", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";          
		$view->sidebar = Sidebar::render();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayOrderStatus', array(&$view));
		$view->displayList();
	}
	
	function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
		$status_id = $this->input->getInt("status_id");
		$order_status = JSFactory::getTable('orderstatus');
		$order_status->load($status_id);
		$edit = ($status_id)?($edit = 1):($edit = 0);
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        OutputFilter::objectHTMLSafe($order_status, ENT_QUOTES);

		$view = $this->getView("orderstatus", 'html');
        $view->setLayout("edit");		
        $view->set('order_status', $order_status);        
        $view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('etemplatevar', '');
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditOrderStatus', array(&$view));
		$view->displayEdit();
	}

}