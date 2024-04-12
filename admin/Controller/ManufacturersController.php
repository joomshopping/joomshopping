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

class ManufacturersController extends BaseadminController{
    
    protected $modelSaveItemFileName = 'manufacturer_logo';

    function init(){
        \JSHelperAdmin::checkAccessController("manufacturers");
        \JSHelperAdmin::addSubmenu("other");
    }
    
    public function getUrlEditItem($id = 0){
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&task=edit&man_id=".$id;
    }

    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $context = "jshopping.list.admin.manufacturers";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $manufacturer = \JSFactory::getModel("manufacturers");
        $rows = $manufacturer->getAllManufacturers(0, $filter_order, $filter_order_Dir);        
        $view = $this->getView("manufacturer", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayManufacturers', array(&$view));
        $view->displayList();
    }

    function edit() {
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $man_id = $this->input->getInt("man_id");
        $manufacturer = \JSFactory::getTable('manufacturer');
        $manufacturer->load($man_id);
        $edit = ($man_id)?(1):(0);
        
        if (!$man_id){
            $manufacturer->manufacturer_publish = 1;
        }
        
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        $nofilter = array();
        \JFilterOutput::objectHTMLSafe($manufacturer, ENT_QUOTES, $nofilter);

        $view=$this->getView("manufacturer", 'html');
        $view->setLayout("edit");
        $view->set('manufacturer', $manufacturer);        
        $view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('etemplatevar', '');
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditManufacturers', array(&$view));        
        $view->displayEdit();
    }

    function delete_foto(){
        $id = $this->input->getInt("id");
        \JSFactory::getModel('manufacturers')->deleteFoto($id);        
        die();
    }

}