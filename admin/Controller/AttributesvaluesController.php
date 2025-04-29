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
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
defined('_JEXEC') or die();

class AttributesValuesController extends BaseadminController{
    
    protected $nameModel = 'attributvalue';
    protected $modelSaveItemFileName = 'image';

    function init(){
        HelperAdmin::checkAccessController("attributesvalues");
        HelperAdmin::addSubmenu("other");
    }
    
    public function getUrlListItems(){
        $attr_id = $this->input->getInt("attr_id");
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&attr_id=".$attr_id;
    }
    
    public function getUrlEditItem($id = 0){
        $attr_id = $this->input->getInt("attr_id");    
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&task=edit&attr_id=".$attr_id."&value_id=".$id;
    }

    function display($cachable = false, $urlparams = false){
		$attr_id = $this->input->getInt("attr_id");
        $jshopConfig = JSFactory::getConfig();
        
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.attr_values";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "value_ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $filter = array_filter($app->getUserStateFromRequest($context.'filter', 'filter', [], 'array'));
        
		$attributValues = JSFactory::getModel("attributvalue");
		$rows = $attributValues->getAllValues($attr_id, $filter_order, $filter_order_Dir, $filter);
	    foreach ($rows as $k => $row) {
		    $rows[$k]->count_products = $attributValues->getProductCount($attr_id, $row->value_id);
	    }
		$attribut = JSFactory::getModel("attribut");
		$attr_name = $attribut->getNameAttribut($attr_id);
        
		$view = $this->getView("attributesvalues", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);        
        $view->set('attr_id', $attr_id);
        $view->set('config', $jshopConfig);
        $view->set('attr_name', $attr_name);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->filter = $filter;
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayAttributesValues', array(&$view));
		$view->displayList(); 
	}
	
	function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
		$value_id = $this->input->getInt("value_id");
		$attr_id = $this->input->getInt("attr_id");
        
		$jshopConfig = JSFactory::getConfig();	
        
        $attributValue = JSFactory::getTable('attributValue');
        $attributValue->load($value_id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;	
        
        OutputFilter::objectHTMLSafe($attributValue, ENT_QUOTES);
		
		$view = $this->getView("attributesvalues", 'html');
        $view->setLayout("edit");		
        $view->set('attributValue', $attributValue);        
        $view->set('attr_id', $attr_id);        
        $view->set('config', $jshopConfig);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditAtributesValues', array(&$view));
		$view->displayEdit();
	}
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
    }
    
    function delete_foto(){
        $id = $this->input->getInt("id");
        $this->getAdminModel()->deleteFoto($id);
        die();               
    }
    
    protected function getOrderWhere(){
        $attr_id = $this->input->getInt("attr_id");
        return 'attr_id='.(int)$attr_id;
    }
    
    protected function getSaveOrderWhere(){
        $field_id = $this->input->getInt("attr_id");
        return 'attr_id='.(int)$field_id;
    }

}