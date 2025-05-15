<?php
/**
* @version      5.6.3 08.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\User\User;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
use Joomla\Component\Jshopping\Site\Helper\Selects;

defined('_JEXEC') or die();

class UsersController extends BaseadminController{
    
    protected $urlEditParamId = 'user_id';
    protected $checkToken = array('save' => 1, 'remove' => 1);
    
    public function init(){
        HelperAdmin::checkAccessController("users");
        HelperAdmin::addSubmenu("users");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshopping.list.admin.users";
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
        $text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
        $usergroup_id = $app->getUserStateFromRequest($context.'l_usergroup_id', 'l_usergroup_id', 0, 'int');
        $user_enable = $app->getUserStateFromRequest($context.'user_enable', 'user_enable', 0, 'int');
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "u_name", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $e_name = $this->input->getCmd("e_name");
        $select_user = $this->input->getInt('select_user');

        $filter = array();
        if ($usergroup_id){
            $filter['usergroup_id'] = $usergroup_id;
        }
        if ($user_enable){
            $filter['user_enable'] = $user_enable;
        }
        
        $users = JSFactory::getModel("users");
        $total = $users->getCountAllUsers($text_search, $filter);
        $pageNav = new Pagination($total, $limitstart, $limit);
        $rows = $users->getAllUsers($pageNav->limitstart, $pageNav->limit, $text_search, $filter_order, $filter_order_Dir, $filter);

        foreach ($rows as $row) {
            $row->tmp_html_col_after_email = "";
            $row->tmp_html_col_after_id = "";
        }

        $select_group = HTMLHelper::_('select.genericlist', SelectOptions::getUserGroups(1), 'l_usergroup_id', 'class="form-select" onchange="document.adminForm.submit();"', 'usergroup_id', 'usergroup_name', $usergroup_id);
        $select_enable = HTMLHelper::_('select.genericlist', SelectOptions::getUserEnabled(1), 'user_enable', 'class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $user_enable);
        
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
        $view->set('select_enable', $select_enable);

        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_html_col_after_email = "";
        $view->tmp_html_col_after_id = "";
        $view->tmp_html_col_before_td_foot = "";
        $view->tmp_html_col_after_td_foot = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayUsers', array(&$view));
        $view->displayList();
    }
    
    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $jshopConfig = JSFactory::getConfig();        
        $me =  Factory::getUser();
        $user_id = $this->input->getInt("user_id");
        $user = JSFactory::getTable('usershop');
        $user->load($user_id);
	    if (!$user_id){
		    $model = JSFactory::getModel('userregister', 'Site');
		    $user_data = (object)$model->getPostData();
		    $user->bind($user_data);
		    $model->clearPostData();
	    }

        $user->loadDataFromEdit();

        $user_site = new User($user_id);

		$lists['country'] = Selects::getCountry($user->country, 'class = "form-select"');
		$lists['d_country'] = Selects::getCountry($user->d_country, 'class = "inputbox endes form-select"', 'd_country');
		$lists['select_titles'] = Selects::getTitle($user->title, 'class = "form-select"');
		$lists['select_d_titles'] = Selects::getTitle($user->d_title, 'class = "inputbox endes form-select"', 'd_title');
		$lists['select_client_types'] = Selects::getClientType($user->client_type, 'class = "form-select"');
        $lists['usergroups'] = HTMLHelper::_('select.genericlist', SelectOptions::getUserGroups(), 'usergroup_id', 'class = "inputbox form-select"', 'usergroup_id', 'usergroup_name', $user->usergroup_id);
        $lists['block'] = HTMLHelper::_('select.booleanlist',  'block', 'class="inputbox"', $user_site->get('block') );  
        
        Helper::filterHTMLSafe($user, ENT_QUOTES);
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['editaccount'];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('editaccount');
		
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
        Factory::getApplication()->triggerEvent('onBeforeEditUsers', array(&$view));
        $view->displayEdit();        
    }
    
    function get_userinfo(){
        $db = Factory::getDBO();
        $id = $this->input->getInt('user_id');
        $copy_delivery_adress = $this->input->getInt('copy_empty_delivery_adress') ?? '0';
        if (!$id){
            print '{}';
            die;
        }
        $query = 'SELECT * FROM `#__jshopping_users` WHERE `user_id`='.(int)$id;
        $db->setQuery($query);
        $user = $db->loadAssoc();
        if ($copy_delivery_adress && !$user['delivery_adress']) {
            $user['d_title'] = $user['title'];
            $user['d_f_name'] = $user['f_name'];
            $user['d_l_name'] = $user['l_name'];
            $user['d_m_name'] = $user['m_name'];
            $user['d_firma_name'] = $user['firma_name'];
            $user['d_home'] = $user['home'];
            $user['d_apartment'] = $user['apartment'];
            $user['d_street'] = $user['street'];
            $user['d_street_nr'] = $user['street_nr'];
            $user['d_zip'] = $user['zip'];
            $user['d_city'] = $user['city'];
            $user['d_state'] = $user['state'];
            $user['d_email'] = $user['email'];
            $user['d_birthday'] = $user['birthday'];
            $user['d_country'] = $user['country'];
            $user['d_phone'] = $user['phone'];
            $user['d_mobil_phone'] = $user['mobil_phone'];
            $user['d_fax'] = $user['fax'];
            $user['d_ext_field_1'] = $user['ext_field_1'];
            $user['d_ext_field_2'] = $user['ext_field_2'];
            $user['d_ext_field_3'] = $user['ext_field_3'];
        }
        echo json_encode((array)$user);
        die();
    }

}