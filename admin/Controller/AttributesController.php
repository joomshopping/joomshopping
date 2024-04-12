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

class AttributesController extends BaseadminController{
    
    protected $nameModel = 'attribut';
    protected $urlEditParamId = 'attr_id';
    
    function init(){
        \JSHelperAdmin::checkAccessController("attributes");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.attributes";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "A.attr_ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
    	$attributes = \JSFactory::getModel("attribut");
    	$attributesvalue = \JSFactory::getModel("attributvalue");
        $rows = $attributes->getAllAttributes(0, null, $filter_order, $filter_order_Dir);
        foreach($rows as $key => $value){
            $rows[$key]->values = \JSHelper::splitValuesArrayObject($attributesvalue->getAllValues($rows[$key]->attr_id), 'name');
            $rows[$key]->count_values = count($attributesvalue->getAllValues($rows[$key]->attr_id));
        }        
        $view = $this->getView("attributes", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayAttributes', array(&$view));
        $view->displayList();
    }

    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $attr_id = $this->input->getInt("attr_id");
        $attribut = \JSFactory::getTable('attribut');
        $attribut->load($attr_id);
        if (!$attribut->independent){
            $attribut->independent = 0;
        }
        if (!isset($attribut->allcats)){
            $attribut->allcats = 1;
        }
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $type_attribut = \JHTML::_('select.genericlist', SelectOptions::getAttributeType(), 'attr_type','class = "inputbox form-control"','attr_type_id','attr_type',$attribut->attr_type);
        $dependent_attribut = \JHTML::_('select.radiolist', SelectOptions::getAttributeDependent(), 'independent','class = "inputbox form-control"','id','name', $attribut->independent);
        $lists['allcats'] = \JHTML::_('select.radiolist', SelectOptions::getAttributeShowCategory(), 'allcats','onclick="jshopAdmin.PFShowHideSelectCats()"','id','value', $attribut->allcats);
        $categories_selected = $attribut->getCategorys();
        $lists['categories'] = \JHTML::_('select.genericlist', SelectOptions::getCategories(0), 'category_id[]','class="inputbox form-control" size="10" multiple = "multiple"','category_id','name', $categories_selected);
        $lists['group'] = \JHTML::_('select.genericlist', SelectOptions::getAttributeGroups(),'group','class="inputbox form-control"','id','name', $attribut->group);
        
        \JFilterOutput::objectHTMLSafe($attribut, ENT_QUOTES);
	    
        $view = $this->getView("attributes", 'html');
        $view->setLayout("edit");
        $view->set('attribut', $attribut);
        $view->set('type_attribut', $type_attribut);
        $view->set('dependent_attribut', $dependent_attribut);
        $view->set('etemplatevar', '');    
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('lists', $lists);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditAtribut', array(&$view, &$attribut));
        $view->displayEdit();		
    }
    
    function addgroup(){
        $this->setRedirect("index.php?option=com_jshopping&controller=attributesgroups");
    }

}