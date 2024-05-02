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
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die;

class VendorsController extends BaseadminController{

    function init(){
        HelperAdmin::checkAccessController("vendors");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshopping.list.admin.vendors";
        $limit = $app->getUserStateFromRequest( $context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $text_search = $app->getUserStateFromRequest( $context.'text_search', 'text_search', '' );

        $vendors = JSFactory::getModel("vendors");
        $total = $vendors->getCountAllVendors($text_search);

        jimport('joomla.html.pagination');
        $pageNav = new Pagination($total, $limitstart, $limit);
        $rows = $vendors->getAllVendors($pageNav->limitstart, $pageNav->limit, $text_search);

        $view = $this->getView("vendors", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('pageNav', $pageNav);
        $view->set('text_search', $text_search);
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayVendors', array(&$view));
        $view->displayList();
    }

    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
        $vendor = JSFactory::getTable('vendor');
        $vendor->load($id);
        if (!$id){
            $vendor->publish = 1;
        }
        $lists['country'] = HTMLHelper::_('select.genericlist', SelectOptions::getCountrys(0), 'country','class = "inputbox form-select"','country_id','name', $vendor->country);

        $nofilter = array();
        OutputFilter::objectHTMLSafe( $vendor, ENT_QUOTES, $nofilter);

        $view = $this->getView("vendors", 'html');
        $view->setLayout("edit");
        $view->set('vendor', $vendor);
        $view->set('lists', $lists);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditVendors', array(&$view));
        $view->displayEdit();
    }

}