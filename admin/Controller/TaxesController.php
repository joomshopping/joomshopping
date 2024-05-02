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
use Joomla\CMS\Filter\OutputFilter;
defined('_JEXEC') or die();

class TaxesController extends BaseadminController{

    protected $urlEditParamId = 'tax_id';
    
    function init(){
        HelperAdmin::checkAccessController("taxes");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.taxes";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "tax_name", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');

        $taxes = JSFactory::getModel("taxes");
        $rows = $taxes->getAllTaxes($filter_order, $filter_order_Dir);

        $view = $this->getView("taxes", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayTaxes', array(&$view));
        $view->displayList();
    }

    function edit() {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $tax_id = $this->input->getInt("tax_id");
        $tax = JSFactory::getTable('tax');
        $tax->load($tax_id);
        $edit = ($tax_id)?($edit = 1):($edit = 0);

        $view=$this->getView("taxes", 'html');
        $view->setLayout("edit");
        OutputFilter::objectHTMLSafe( $tax, ENT_QUOTES);
        $view->set('tax', $tax);
        $view->set('edit', $edit);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditTaxes', array(&$view));
        $view->displayEdit();
    }

}