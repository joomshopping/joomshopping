<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
use Joomla\Component\Jshopping\Site\Helper\Selects;

defined('_JEXEC') or die();

class UsersController extends BaseadminController{
    
    protected $urlEditParamId = 'user_id';
    protected $checkToken = array('save' => 1, 'remove' => 1);
    
    public function init(){
        \JSHelperAdmin::checkAccessController("users");
        \JSHelperAdmin::addSubmenu("users");
    }

    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $context = "jshopping.list.admin.users";
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
        $text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
        $usergroup_id = $app->getUserStateFromRequest($context.'l_usergroup_id', 'l_usergroup_id', 0, 'int');
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "u_name", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $e_name = $this->input->getCmd("e_name");
        $select_user = $this->input->getInt('select_user');

        $filter = array();
        if ($usergroup_id){
            $filter['usergroup_id'] = $usergroup_id;
        }
        
        $users = \JSFactory::getModel("users");        
        $total = $users->getCountAllUsers($text_search, $filter);
        
        jimport('joomla.html.pagination');
        $pageNav = new \JPagination($total, $limitstart, $limit);
        $rows = $users->getAllUsers($pageNav->limitstart, $pageNav->limit, $text_search, $filter_order, $filter_order_Dir, $filter);

        foreach ($rows as $row) {
            $row->tmp_html_col_after_email = "";
            $row->tmp_html_col_after_id = "";
        }

        $select_group = \JHTML::_('select.genericlist', SelectOptions::getUserGroups(1), 'l_usergroup_id', 'class="form-select" onchange="document.adminForm.submit();"', 'usergroup_id', 'usergroup_name', $usergroup_id);
        
        $view=$this->getView("users", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('pageNav', $pageNav);
        $view->set('text_search', $text_search);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->set('e_name', $e_name);
        $view->set('select_user', $select_user);
        $view->set('select_group', $select_group);
        $view->sidebar = \JHTMLSidebar::render();
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_col_after_email = "";
        $view->tmp_html_col_after_id = "";
        $view->tmp_html_col_before_td_foot = "";
        $view->tmp_html_col_after_td_foot = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayUsers', array(&$view));
        $view->displayList();
    }
    
    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $jshopConfig = \JSFactory::getConfig();        
        $me =  \JFactory::getUser();
        $user_id = $this->input->getInt("user_id");
        $user = \JSFactory::getTable('usershop');
        $user->load($user_id);
        $user->loadDataFromEdit();
		
        $user_site = new \JUser($user_id);
		
		$lists['country'] = Selects::getCountry($user->country, 'class = "form-control"');
		$lists['d_country'] = Selects::getCountry($user->d_country, 'class = "inputbox endes form-control"', 'd_country');
		$lists['select_titles'] = Selects::getTitle($user->title, 'class = "form-control"');
		$lists['select_d_titles'] = Selects::getTitle($user->d_title, 'class = "inputbox endes form-control"', 'd_title');
		$lists['select_client_types'] = Selects::getClientType($user->client_type, 'class = "form-control"');
        $lists['usergroups'] = \JHTML::_('select.genericlist', SelectOptions::getUserGroups(), 'usergroup_id', 'class = "inputbox form-control"', 'usergroup_id', 'usergroup_name', $user->usergroup_id);
        $lists['block'] = \JHTML::_('select.booleanlist',  'block', 'class="inputbox"', $user_site->get('block') );  
        
        \JSHelper::filterHTMLSafe($user, ENT_QUOTES);
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['editaccount'];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('editaccount');
        
		//\JHTML::_('behavior.calendar');
		
        $view=$this->getView("users", 'html');
        $view->setLayout("edit");
		$view->set('config', $jshopConfig);
        $view->set('user', $user);  
        $view->set('me', $me);       
        $view->set('user_site', $user_site);
        $view->set('lists', $lists);
        $view->set('etemplatevar', '');
        $view->set('config_fields', $config_fields);
        $view->set('count_filed_delivery', $count_filed_delivery);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->etemplatevar = "";
        $view->etemplatevar0 = "";
        $view->etemplatevar1 = "";
        $view->etemplatevarend = "";
        \JFactory::getApplication()->triggerEvent('onBeforeEditUsers', array(&$view));
        $view->displayEdit();        
    }
    
    function get_userinfo(){
        $db = \JFactory::getDBO();
        $id = $this->input->getInt('user_id');
        if (!$id){
            print '{}';
            die;
        }
        $query = 'SELECT * FROM `#__jshopping_users` WHERE `user_id`='.(int)$id;
        $db->setQuery($query);
        $user = $db->loadAssoc();
        echo json_encode((array)$user);
        die();
    }

}