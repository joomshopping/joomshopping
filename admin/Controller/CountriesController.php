<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die();

class CountriesController extends BaseadminController{
    
    protected $urlEditParamId = 'country_id';
    
    function init(){
        \JSHelperAdmin::checkAccessController("countries");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){  	        		
        $app = \JFactory::getApplication();
		$context = "jshoping.list.admin.countries";
        $limit = $app->getUserStateFromRequest( $context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $publish = $app->getUserStateFromRequest( $context.'publish', 'publish', 0, 'int' );
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');        
		
		$countries = \JSFactory::getModel("countries");
		$total = $countries->getCountAllCountries();
        if ($publish == 0){
            $total = $countries->getCountAllCountries();
        } else {
            $total = $countries->getCountPublishCountries($publish % 2);
        }
		
		jimport('joomla.html.pagination');
        $pageNav = new \JPagination($total, $limitstart, $limit);		
        $rows = $countries->getAllCountries($publish, $pageNav->limitstart,$pageNav->limit, 0, $filter_order, $filter_order_Dir);
        $filter = \JHTML::_('select.genericlist', SelectOptions::getPublish(4), 'publish', 'class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $publish);
                
		$view = $this->getView("countries", 'html');
        $view->setLayout("list");		
        $view->set('rows', $rows); 
        $view->set('pageNav', $pageNav);       
        $view->set('filter', $filter);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayCountries', array(&$view));
		$view->displayList(); 
    }
    
   	function edit() {
        \JFactory::getApplication()->input->set('hidemainmenu', true);
		$country_id = $this->input->getInt("country_id");
		$country = \JSFactory::getTable('country');
		$country->load($country_id);
		$lists['order_countries'] = \JHTML::_('select.genericlist', SelectOptions::getCountryOrdering(), 'ordering','class="inputbox form-control"','ordering','name', $country->ordering);
        
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;        
        
		$edit = ($country_id)?($edit = 1):($edit = 0);                
        
        \JFilterOutput::objectHTMLSafe( $country, ENT_QUOTES);

		$view=$this->getView("countries", 'html');
        $view->setLayout("edit");		
        $view->set('country', $country); 
        $view->set('lists', $lists);       
        $view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('etemplatevar', '');
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditCountries', array(&$view));
		$view->displayEdit();
	}

}