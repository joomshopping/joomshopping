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

class CurrenciesController extends BaseadminController{
    
    protected $urlEditParamId = 'currency_id';
    
    function init(){
        \JSHelperAdmin::checkAccessController("currencies");
        \JSHelperAdmin::addSubmenu("other");
    }
        
    function display($cachable = false, $urlparams = false) {        
        $jshopConfig = \JSFactory::getConfig();
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.currencies";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "currency_ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');

        $current_currency = \JSFactory::getTable('currency');
        $current_currency->load($jshopConfig->mainCurrency);
        if ($current_currency->currency_value!=1){
            \JSError::raiseWarning("",\JText::_('JSHOP_ERROR_MAIN_CURRENCY_VALUE'));
        }
        
        $currencies = \JSFactory::getModel("currencies");
        $rows = $currencies->getAllCurrencies(0, $filter_order, $filter_order_Dir);
        
        $view = $this->getView("currencies", 'html');
        $view->setLayout("list");        
        $view->set('rows', $rows);        
        $view->set('config', $jshopConfig);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayCourencies', array(&$view));
        $view->displayList();
    }
    
    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $currency = \JSFactory::getTable('currency');
        $currencies = \JSFactory::getModel("currencies");
        $currency_id = $this->input->getInt('currency_id');
        $currency->load($currency_id);
        if ($currency->currency_value==0){
            $currency->currency_value = 1;
        }
        $first[] = \JHTML::_('select.option', '0',\JText::_('JSHOP_ORDERING_FIRST'),'currency_ordering','currency_name');
        $rows = array_merge($first, $currencies->getAllCurrencies(0));
        $lists['order_currencies'] = \JHTML::_('select.genericlist', $rows,'currency_ordering','class="inputbox form-control" ','currency_ordering','currency_name',$currency->currency_ordering);
        $edit = ($currency_id)?($edit = 1):($edit = 0);
        \JFilterOutput::objectHTMLSafe($currency, ENT_QUOTES);
        
        $view = $this->getView("currencies", 'html');
        $view->setLayout("edit");
        $view->set('currency', $currency);        
        $view->set('lists', $lists);        
        $view->set('edit', $edit);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditCurrencies', array(&$view));        
        $view->displayEdit();
    }
    
    function setdefault(){
        $jshopConfig = \JSFactory::getConfig();
        $cid = $this->input->getVar("cid");
        if ($cid[0]){
            $config = \JSFactory::getTable('Config');
            $config->id = $jshopConfig->load_id;             
            $config->mainCurrency = $cid[0];
            $config->store();
        }
        $this->setRedirect("index.php?option=com_jshopping&controller=currencies");
    }
    
}