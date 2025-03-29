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

class FreeAttributesController extends BaseadminController{
    
    protected $nameModel = 'freeattribut';
    
    function init(){
        HelperAdmin::checkAccessController("freeattributes");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.freeattributes";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $filter = array_filter($app->getUserStateFromRequest($context.'filter', 'filter', [], 'array'));
        
    	$freeattributes = JSFactory::getModel("freeattribut");    	
        $rows = $freeattributes->getAll($filter_order, $filter_order_Dir, $filter);
        $view = $this->getView("freeattributes", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->filter = $filter;
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";        
        $app->triggerEvent('onBeforeDisplayFreeAttributes', array(&$view));
        $view->displayList();
    }
	
    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
	
        $attribut = JSFactory::getTable('freeattribut');
        $attribut->load($id);
    
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        OutputFilter::objectHTMLSafe($attribut, ENT_QUOTES);		

        $view = $this->getView("freeattributes", 'html');
        $view->setLayout("edit");
        $view->set('attribut', $attribut);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent( 'onBeforeEditFreeAtribut', array(&$view, &$attribut) );
        $view->displayEdit();
	}
	
}