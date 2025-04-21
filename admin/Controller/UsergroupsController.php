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
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Filter\OutputFilter;
defined('_JEXEC') or die;

class UserGroupsController extends BaseadminController{
    
    protected $urlEditParamId = 'usergroup_id';
    
    function init(){
        HelperAdmin::checkAccessController("usergroups");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.usergroups";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "usergroup_id", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $filter = array_filter($app->getUserStateFromRequest($context.'filter', 'filter', [], 'array'));

		$usergroups = JSFactory::getModel("usergroups");
		$rows = $usergroups->getAllUsergroups($filter_order, $filter_order_Dir, $filter);

        $view = $this->getView("usergroups", 'html');
        $view->setLayout("list");
        $view->set("rows", $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->filter = $filter;
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayUserGroups', array(&$view));
        $view->displayList();
    }

	function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
		$usergroup_id = $this->input->getInt("usergroup_id");
		$usergroup = JSFactory::getTable('usergroup');
		$usergroup->load($usergroup_id);
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $edit = ($usergroup_id) ? 1 : 0;
        OutputFilter::objectHTMLSafe($usergroup, ENT_QUOTES, "usergroup_description");

		$view = $this->getView("usergroups", 'html');
        $view->setLayout("edit");
        $view->set("usergroup", $usergroup);
        $view->set('etemplatevar', '');
        $view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditUserGroups', array(&$view));
        $view->displayEdit();
	}

}