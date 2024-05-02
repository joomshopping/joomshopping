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
defined('_JEXEC') or die;

class ProductFieldGroupsController extends BaseadminController{

    function init(){
        HelperAdmin::checkAccessController("productfieldgroups");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $_productfieldgroups = JSFactory::getModel("productfieldgroups");
        $rows = $_productfieldgroups->getList();

        $view = $this->getView("product_field_groups", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);

        $view->tmp_html_start = '';
        $view->tmp_html_end = '';
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductsFieldGroups', array(&$view));
        $view->displayList();
    }

    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
        $productfieldgroup = JSFactory::getTable('productfieldgroup');
        $productfieldgroup->load($id);

        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $view = $this->getView("product_field_groups", 'html');
        $view->setLayout("edit");
        OutputFilter::objectHTMLSafe($productfieldgroup, ENT_QUOTES);
        $view->set('row', $productfieldgroup);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->tmp_html_start = '';
        $view->tmp_html_end = '';
        $view->etemplatevar = '';
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditProductFieldGroups', array(&$view));
        $view->displayEdit();
    }

    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
    }

}