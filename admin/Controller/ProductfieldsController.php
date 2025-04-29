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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die();

class ProductFieldsController extends BaseadminController{
    
    function init(){
        HelperAdmin::checkAccessController("productfields");
        HelperAdmin::addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $context = "jshoping.list.admin.productfields";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "F.ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $group = $app->getUserStateFromRequest($context.'group', 'group', 0, 'int');
        $category_id = $app->getUserStateFromRequest($context.'category_id', 'category_id', 0, 'int');
        $text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
        
        $filter = array("group"=>$group, "text_search"=>$text_search, 'category_id'=>$category_id);
        
        $_productfields = JSFactory::getModel("productfields");
		$rows = $_productfields->getList(0, $filter_order, $filter_order_Dir, $filter, 1);
	    foreach ($rows as $k => $row){
		    $rows[$k]->count_products = $_productfields->getProductCount($row->id);
	    }
        
        $_productfieldvalues = JSFactory::getModel("productfieldvalues");
        $vals = $_productfieldvalues->getAllList(2);
    
        foreach($rows as $k=>$v){
            if (isset($vals[$v->id])){
                if (is_array($vals[$v->id])){
                    $rows[$k]->count_option = count($vals[$v->id]);
                }else{
                    $rows[$k]->count_option = 0;
                }
            }else{
                $vals[$v->id] = [];
                $rows[$k]->count_option = 0;
            }    
        }
		$lists = array();
        $lists['group'] = HTMLHelper::_('select.genericlist', SelectOptions::getProductFieldGroups(), 'group', 'class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $group);
        $lists['treecategories'] = HTMLHelper::_('select.genericlist', SelectOptions::getCategories(), 'category_id', 'class="form-select" onchange="document.adminForm.submit();"', 'category_id', 'name', $category_id);
        $types = $_productfields->getTypes(1);

        $view = $this->getView("product_fields", 'html');
        $view->setLayout("list");
		$view->set('lists', $lists);
        $view->set('rows', $rows);
        $view->set('vals', $vals);
        $view->set('types', $types);
		$view->set('text_search', $text_search);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->set('config', $jshopConfig);
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductField', array(&$view));
        $view->displayList();
    }
    
    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);      
        $id = $this->input->getInt("id");
        $productfield = JSFactory::getTable('productfield');
        $productfield->load($id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        $categories_selected = $productfield->getCategorys();
        $categories = SelectOptions::getCategories(0);
        if (!isset($productfield->type)) $productfield->type = 0;
        if ($productfield->multilist) $productfield->type = -1;
        if (!isset($productfield->allcats)) $productfield->allcats = 1;
        
        $lists['allcats'] = HTMLHelper::_('select.radiolist', SelectOptions::getProductFieldShowCategory(), 'allcats', 'class="form-control" onclick="jshopAdmin.PFShowHideSelectCats()"', 'id', 'value', $productfield->allcats);
        $lists['categories'] = HTMLHelper::_('select.genericlist', $categories, 'category_id[]', 'class="inputbox form-select" size="10" multiple = "multiple"', 'category_id', 'name', $categories_selected);
        $show_deprecated = $productfield->type == 1 ? 1 : 0;
        $lists['type'] = HTMLHelper::_('select.radiolist', SelectOptions::getProductFieldTypes($show_deprecated), 'type', 'class="form-select"', 'id', 'value', $productfield->type);
        $lists['group'] = HTMLHelper::_('select.genericlist', SelectOptions::getProductFieldGroups('- - -'), 'group', 'class="inputbox form-select"', 'id', 'name', $productfield->group);
        
        OutputFilter::objectHTMLSafe($productfield, ENT_QUOTES);

        $view = $this->getView("product_fields", 'html');
        $view->setLayout("edit");
        $view->set('row', $productfield);
        $view->set('lists', $lists);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
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