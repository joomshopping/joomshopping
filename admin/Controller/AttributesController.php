<?php
/**
* @version      5.7.1 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die();

class AttributesController extends BaseadminController{
    
    protected $nameModel = 'attribut';
    protected $urlEditParamId = 'attr_id';
    
    function init(){
        HelperAdmin::checkAccessController("attributes");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.attributes";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "A.attr_ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $filter = array_filter($app->getUserStateFromRequest($context.'filter', 'filter', [], 'array'));
        
    	$attributes = JSFactory::getModel("attribut");
    	$attributesvalue = JSFactory::getModel("attributvalue");
        $rows = $attributes->getAllAttributes(0, null, $filter_order, $filter_order_Dir, [], $filter);
        foreach($rows as $key => $value){
            $rows[$key]->values = Helper::splitValuesArrayObject($attributesvalue->getAllValues($rows[$key]->attr_id), 'name');
            $rows[$key]->count_values = count($attributesvalue->getAllValues($rows[$key]->attr_id));
            $rows[$key]->count_products = $attributes->getProductCount($rows[$key]->attr_id);
        }
        $view = $this->getView("attributes", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->filter = $filter;
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $app->triggerEvent('onBeforeDisplayAttributes', array(&$view));
        $view->displayList();
    }

    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $attr_id = $this->input->getInt("attr_id");
        $attribut = JSFactory::getTable('attribut');
        $attribut->load($attr_id);
        if (!$attr_id) {
            $attribut->required = 1;
        }
        if (!$attribut->independent){
            $attribut->independent = 0;
        }
        if (!isset($attribut->allcats)){
            $attribut->allcats = 1;
        }
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $type_attribut = HTMLHelper::_('select.genericlist', SelectOptions::getAttributeType(), 'attr_type','class = "inputbox form-select"','attr_type_id','attr_type',$attribut->attr_type);
        $dependent_attribut = HTMLHelper::_('select.radiolist', SelectOptions::getAttributeDependent(), 'independent','class = "inputbox form-control"','id','name', $attribut->independent);
        $lists['allcats'] = HTMLHelper::_('select.radiolist', SelectOptions::getAttributeShowCategory(), 'allcats','onclick="jshopAdmin.PFShowHideSelectCats()"','id','value', $attribut->allcats);
        $categories_selected = $attribut->getCategorys();
        $lists['categories'] = HTMLHelper::_('select.genericlist', SelectOptions::getCategories(0), 'category_id[]','class="inputbox form-select" size="10" multiple = "multiple"','category_id','name', $categories_selected);
        $lists['group'] = HTMLHelper::_('select.genericlist', SelectOptions::getAttributeGroups(),'group','class="inputbox form-select"','id','name', $attribut->group);
        
        OutputFilter::objectHTMLSafe($attribut, ENT_QUOTES);
	    
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
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditAtribut', array(&$view, &$attribut));
        $view->displayEdit();		
    }
    
    function addgroup(){
        $this->setRedirect("index.php?option=com_jshopping&controller=attributesgroups");
    }

}