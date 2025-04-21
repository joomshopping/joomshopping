<?php
/**
* @version      5.6.1 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Filter\OutputFilter;
defined('_JEXEC') or die();

class DeliveryTimesController extends BaseadminController{
    
    function init(){
        HelperAdmin::checkAccessController("deliverytimes");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.deliverytimes";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "name", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $filter = array_filter($app->getUserStateFromRequest($context.'filter', 'filter', [], 'array'));
        
        $_deliveryTimes = JSFactory::getModel("deliverytimes");
        $rows = $_deliveryTimes->getDeliveryTimes($filter_order, $filter_order_Dir, $filter);
        $view=$this->getView("deliverytimes", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->filter = $filter;
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayDeliveryTimes', array(&$view));
        $view->displayList();
    }
	
	function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
		$id = $this->input->getInt("id");
		$deliveryTimes = JSFactory::getTable('deliverytimes');
		$deliveryTimes->load($id);
		$edit = ($id)?(1):(0);
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        OutputFilter::objectHTMLSafe($deliveryTimes, ENT_QUOTES);

		$view = $this->getView("deliverytimes", 'html');
        $view->setLayout("edit");
        $view->set('deliveryTimes', $deliveryTimes);        
        $view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditDeliverytimes', array(&$view));
		$view->displayEdit();
	}
    
}
