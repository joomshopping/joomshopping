<?php
/**
* @version      5.6.2 13.03.2025
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
defined('_JEXEC') or die();

class CountriesController extends BaseadminController{
    
    protected $urlEditParamId = 'country_id';
    
    function init(){
        HelperAdmin::checkAccessController("countries");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
		$context = "jshoping.list.admin.countries";
        $limit = $app->getUserStateFromRequest( $context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $publish = $app->getUserStateFromRequest( $context.'publish', 'publish', 0, 'int' );
        $text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');        
		
        $filters = [];
        if ($publish) {
            $filters['publish'] = $publish % 2;
        }
        if ($text_search) {
            $filters['text_search'] = $text_search;
        }
		$countries = JSFactory::getModel("countries");
        $total = $countries->getCountItems($filters);
        $pageNav = new Pagination($total, $limitstart, $limit);		
        $rows = $countries->getListItems(
            $filters, 
            ['order' => $filter_order, 'dir' => $filter_order_Dir], 
            ['limitstart' =>$pageNav->limitstart, 'limit' => $pageNav->limit], 
            ['orderConfig' => 0]
        );
        $filter = HTMLHelper::_('select.genericlist', SelectOptions::getPublish(4), 'publish', 'class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $publish);
                
		$view = $this->getView("countries", 'html');
        $view->setLayout("list");		
        $view->set('rows', $rows); 
        $view->set('pageNav', $pageNav);       
        $view->set('filter', $filter);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->text_search = $text_search;
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayCountries', array(&$view));
		$view->displayList(); 
    }
    
   	function edit() {
        Factory::getApplication()->input->set('hidemainmenu', true);
		$country_id = $this->input->getInt("country_id");
		$country = JSFactory::getTable('country');
		$country->load($country_id);
		$lists['order_countries'] = HTMLHelper::_('select.genericlist', SelectOptions::getCountryOrdering(), 'ordering','class="inputbox form-select"','ordering','name', $country->ordering);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;        
        
		$edit = ($country_id)?($edit = 1):($edit = 0);                
        
        OutputFilter::objectHTMLSafe($country, ENT_QUOTES);

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
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditCountries', array(&$view));
		$view->displayEdit();
	}

}