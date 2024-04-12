<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
defined('_JEXEC') or die;

class UserGroupsController extends BaseadminController{
    
    protected $urlEditParamId = 'usergroup_id';
    
    function init(){
        \JSHelperAdmin::checkAccessController("usergroups");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.usergroups";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "usergroup_id", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');

		$usergroups = \JSFactory::getModel("usergroups");
		$rows = $usergroups->getAllUsergroups($filter_order, $filter_order_Dir);

        $view = $this->getView("usergroups", 'html');
        $view->setLayout("list");
        $view->set("rows", $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayUserGroups', array(&$view));
        $view->displayList();
    }

	function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
		$usergroup_id = $this->input->getInt("usergroup_id");
		$usergroup = \JSFactory::getTable('usergroup');
		$usergroup->load($usergroup_id);
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $edit = ($usergroup_id) ? 1 : 0;
        \JFilterOutput::objectHTMLSafe($usergroup, ENT_QUOTES, "usergroup_description");

		$view = $this->getView("usergroups", 'html');
        $view->setLayout("edit");
        $view->set("usergroup", $usergroup);
        $view->set('etemplatevar', '');
        $view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditUserGroups', array(&$view));
        $view->displayEdit();
	}

}