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

class ProductFieldsController extends BaseadminController{
    
    function init(){
        \JSHelperAdmin::checkAccessController("productfields");
        \JSHelperAdmin::addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.productfields";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "F.ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $group = $app->getUserStateFromRequest($context.'group', 'group', 0, 'int');
        $category_id = $app->getUserStateFromRequest($context.'category_id', 'category_id', 0, 'int');
        $text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
        
        $filter = array("group"=>$group, "text_search"=>$text_search, 'category_id'=>$category_id);
        
        $_productfields = \JSFactory::getModel("productfields");
		$rows = $_productfields->getList(0, $filter_order, $filter_order_Dir, $filter, 1);
        
        $_productfieldvalues = \JSFactory::getModel("productfieldvalues");
        $vals = $_productfieldvalues->getAllList(2);
    
        foreach($rows as $k=>$v){
            if (isset($vals[$v->id])){
                if (is_array($vals[$v->id])){
                    $rows[$k]->count_option = count($vals[$v->id]);
                }else{
                    $rows[$k]->count_option = 0;
                }
            }else{
                $rows[$k]->count_option = 0;
            }    
        }
		$lists = array();
        $lists['group'] = \JHTML::_('select.genericlist', SelectOptions::getProductFieldGroups(), 'group', 'class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $group);
        $lists['treecategories'] = \JHTML::_('select.genericlist', SelectOptions::getCategories(), 'category_id', 'class="form-select" onchange="document.adminForm.submit();"', 'category_id', 'name', $category_id);
        $types = array(\JText::_('JSHOP_LIST'), \JText::_('JSHOP_TEXT'));

        $view = $this->getView("product_fields", 'html');
        $view->setLayout("list");
		$view->set('lists', $lists);
        $view->set('rows', $rows);
        $view->set('vals', $vals);
        $view->set('types', $types);
		$view->set('text_search', $text_search);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductField', array(&$view));
        $view->displayList();
    }
    
    function edit(){  
        \JFactory::getApplication()->input->set('hidemainmenu', true);      
        $id = $this->input->getInt("id");
        $productfield = \JSFactory::getTable('productfield');
        $productfield->load($id);
        
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        $categories_selected = $productfield->getCategorys();
        $categories = SelectOptions::getCategories(0);
        if (!isset($productfield->type)) $productfield->type = 0;
        if ($productfield->multilist) $productfield->type = -1;
        if (!isset($productfield->allcats)) $productfield->allcats = 1;
        
        $lists['allcats'] = \JHTML::_('select.radiolist', SelectOptions::getProductFieldShowCategory(), 'allcats', 'class="form-control" onclick="jshopAdmin.PFShowHideSelectCats()"', 'id', 'value', $productfield->allcats);
        $lists['categories'] = \JHTML::_('select.genericlist', $categories, 'category_id[]', 'class="inputbox form-control" size="10" multiple = "multiple"', 'category_id', 'name', $categories_selected);
        $lists['type'] = \JHTML::_('select.radiolist', SelectOptions::getProductFieldTypes(), 'type', 'class="form-control"', 'id', 'value', $productfield->type);
        $lists['group'] = \JHTML::_('select.genericlist', SelectOptions::getProductFieldGroups('- - -'), 'group', 'class="inputbox form-control"', 'id', 'name', $productfield->group);
        
        \JFilterOutput::objectHTMLSafe($productfield, ENT_QUOTES);
        
        $view = $this->getView("product_fields", 'html');
        $view->setLayout("edit");
        $view->set('row', $productfield);
        $view->set('lists', $lists);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditProductFields', array(&$view));
        $view->displayEdit();
    }
    
    function addgroup(){
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups");
    }
    
    function cancel(){
        $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
    }
    
}