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
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
defined('_JEXEC') or die();

class AttributesGroupsController extends BaseadminController{
    
    function init(){
        HelperAdmin::checkAccessController("attributesgroups");
        HelperAdmin::addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $model = JSFactory::getModel("attributesgroups");
        $rows = $model->getList();
        $view = $this->getView("attributesgroups", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
		$view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayAttributesGroups', array(&$view));
        $view->displayList();
    }
    
    function edit(){    
        Factory::getApplication()->input->set('hidemainmenu', true);    
        $id = $this->input->getInt("id");
        $row = JSFactory::getTable('attributesgroup');
        $row->load($id);
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        OutputFilter::objectHTMLSafe($row, ENT_QUOTES);
                
        $view = $this->getView("attributesgroups", 'html');
        $view->setLayout("edit");
        $view->set('row', $row);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
		$view->etemplatevar = "";
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditAttributesGroups', array(&$view));
        $view->displayEdit();
    }

    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
    }
    
}