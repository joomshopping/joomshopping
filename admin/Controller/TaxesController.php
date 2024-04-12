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

class TaxesController extends BaseadminController{

    protected $urlEditParamId = 'tax_id';
    
    function init(){
        \JSHelperAdmin::checkAccessController("taxes");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.taxes";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "tax_name", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');

        $taxes = \JSFactory::getModel("taxes");
        $rows = $taxes->getAllTaxes($filter_order, $filter_order_Dir);

        $view = $this->getView("taxes", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayTaxes', array(&$view));
        $view->displayList();
    }

    function edit() {
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $tax_id = $this->input->getInt("tax_id");
        $tax = \JSFactory::getTable('tax');
        $tax->load($tax_id);
        $edit = ($tax_id)?($edit = 1):($edit = 0);

        $view=$this->getView("taxes", 'html');
        $view->setLayout("edit");
        \JFilterOutput::objectHTMLSafe( $tax, ENT_QUOTES);
        $view->set('tax', $tax);
        $view->set('edit', $edit);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditTaxes', array(&$view));
        $view->displayEdit();
    }

}