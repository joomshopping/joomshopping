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

class ProductFieldValuesController extends BaseadminController{

    function init(){
        \JSHelperAdmin::checkAccessController("productfieldvalues");
        \JSHelperAdmin::addSubmenu("other");
    }
    
    public function getUrlListItems(){
        $field_id = $this->input->getInt('field_id');
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&field_id=".$field_id;
    }
    
    public function getUrlEditItem($id = 0){
        $field_id = $this->input->getInt('field_id');
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&task=edit&field_id=".$field_id."&id=".$id;
    }

    function display($cachable = false, $urlparams = false){
        $field_id = $this->input->getInt("field_id");
        $_productfieldvalues = \JSFactory::getModel("productfieldvalues");
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.productfieldvalues";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');

        $filter = array("text_search"=>$text_search);

        $rows = $_productfieldvalues->getList($field_id, $filter_order, $filter_order_Dir, $filter);

        $view = $this->getView("product_field_values", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('field_id', $field_id);
		$view->set('text_search', $text_search);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
		$view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductFieldValues', array(&$view));
        $view->displayList();
    }

    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $field_id = $this->input->getInt("field_id");
        $id = $this->input->getInt("id");

        $productfieldvalue = \JSFactory::getTable('productfieldvalue');
        $productfieldvalue->load($id);

        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $view = $this->getView("product_field_values", 'html');
        $view->setLayout("edit");
        \JFilterOutput::objectHTMLSafe($productfieldvalue, ENT_QUOTES);
        $view->set('row', $productfieldvalue);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('field_id', $field_id);
		$view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->etemplatevar = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditProductFieldValues', array(&$view));
        $view->displayEdit();
    }

    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
    }
    
    protected function getOrderWhere(){
        $field_id = $this->input->getInt("field_id");
        return 'field_id='.(int)$field_id;
    }
    
    protected function getSaveOrderWhere(){
        $field_id = $this->input->getInt("field_id");
        return 'field_id='.(int)$field_id;
    }

}