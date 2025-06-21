<?php
/**
* @version      5.3.0 15.09.2018
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
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die();

class ProductfieldvaluesController extends BaseadminController{

    function init(){
        HelperAdmin::checkAccessController("productfieldvalues");
        HelperAdmin::addSubmenu("other");
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
        $_productfieldvalues = JSFactory::getModel("productfieldvalues");
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.productfieldvalues";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');

        $filter = array("text_search"=>$text_search);

        $rows = $_productfieldvalues->getList($field_id, $filter_order, $filter_order_Dir, $filter);
		foreach ($rows as $k => $row){
			$rows[$k]->count_products = $_productfieldvalues->getProductCount($field_id, $row->id);
		}
        $productfield = JSFactory::getTable('productfield');
        $productfield->load($field_id);

        $view = $this->getView("product_field_values", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('field_id', $field_id);
		$view->set('text_search', $text_search);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->set('productfield', $productfield);
		$view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_end = "";
        $view->tmp_html_name_sort_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductFieldValues', array(&$view));
        $view->displayList();
    }

    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $field_id = $this->input->getInt("field_id");
        $id = $this->input->getInt("id");

        $productfieldvalue = JSFactory::getTable('productfieldvalue');
        $productfieldvalue->load($id);        

        $languages = JSFactory::getModel("languages")->getAllLanguages(1);
        $multilang = count($languages)>1;

        $view = $this->getView("product_field_values", 'html');
        $view->setLayout("edit");
        OutputFilter::objectHTMLSafe($productfieldvalue, ENT_QUOTES);
        $view->set('row', $productfieldvalue);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('field_id', $field_id);
		$view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->etemplatevar = "";
        $dispatcher = Factory::getApplication();
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

    public function edit_ajax(){
        $options = $this->input->getVar('options');
        $ef_id = $this->input->getInt('ef_id');
        $ef_val_id = $this->input->getInt('ef_val_id');
        if (!$ef_id) die();

        $data = [];
        $data['id'] = $ef_val_id > 0 ? $ef_val_id : 0;
        $data['field_id'] = $ef_id;
        foreach($options as $k => $v) {
            $data['name_'.$k] = $v;
        }
        $model = JSFactory::getModel("productfieldvalues");
        $productfieldvalue = $model->save($data);
        $ef_val_id = $productfieldvalue->id;

        $lang = JSFactory::getLang()->getLang();
        $active_val_text = $options[$lang];        
        print json_encode(['id' => $ef_val_id, 'text' => $active_val_text]);
        die();
    }

    public function clear_double() {
        $field_id = $this->input->getInt("field_id");
        $model = JSFactory::getModel('ProductFieldValues');
        if ($model->clearDoubleValues($field_id)) {
            JSError::raiseMessage(100, Text::_('JSHOP_DATA_SUCC_UPDATED'));
        }
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldvalues&field_id=".$field_id);
    }

}