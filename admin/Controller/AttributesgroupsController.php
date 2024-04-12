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

class AttributesGroupsController extends BaseadminController{
    
    function init(){
        \JSHelperAdmin::checkAccessController("attributesgroups");
        \JSHelperAdmin::addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $model = \JSFactory::getModel("attributesgroups");
        $rows = $model->getList();
        $view = $this->getView("attributesgroups", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayAttributesGroups', array(&$view));
        $view->displayList();
    }
    
    function edit(){    
        \JFactory::getApplication()->input->set('hidemainmenu', true);    
        $id = $this->input->getInt("id");
        $row = \JSFactory::getTable('attributesgroup');
        $row->load($id);
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        \JFilterOutput::objectHTMLSafe($row, ENT_QUOTES);
                
        $view = $this->getView("attributesgroups", 'html');
        $view->setLayout("edit");
        $view->set('row', $row);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditAttributesGroups', array(&$view));
        $view->displayEdit();
    }

    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
    }
    
}