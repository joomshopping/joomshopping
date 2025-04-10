<?php
/**
* @version      5.2.2 20.11.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
use Joomla\Component\Jshopping\Site\Helper\Selects;
defined('_JEXEC') or die();

class UserController extends BaseController{

    public function init(){
        PluginHelper::importPlugin('jshoppingcheckout');
        PluginHelper::importPlugin('jshoppingorder');
        $obj = $this;
        Factory::getApplication()->triggerEvent('onConstructJshoppingControllerUser', array(&$obj));
    }
    
    function display($cachable = false, $urlparams = false){
        $this->myaccount();
    }
    
    function login(){
        $jshopConfig = JSFactory::getConfig();
		$dispatcher = Factory::getApplication();
		$model = JSFactory::getModel('userlogin', 'Site');		
		
        if (Factory::getUser()->id){   
            $this->logoutpage();
            return 0;
        }
		
        $checkout_navigator = JSFactory::getModel('checkout', 'Site')->showCheckoutNavigation('1');
   
		$return = $model->getUrlHash();
        $show_pay_without_reg = $model->getPayWithoutReg();
        
        Metadata::userLogin();

		$modelUserRegister = JSFactory::getModel('userregister', 'Site');
        $adv_user = $modelUserRegister->getRegistrationDefaultData();
                
		$config_fields = $jshopConfig->getListFieldsRegisterType('register');
        $cssreq_fields = $jshopConfig->getListFieldsRegisterTypeClassRequired('register', $config_fields);
		$allowUserRegistration = $modelUserRegister->getUserParams()->get('allowUserRegistration');

        $dispatcher->triggerEvent('onBeforeDisplayLogin', array() );
		if ($jshopConfig->show_registerform_in_logintemplate){
            $model_register = JSFactory::getModel('userregister', 'jshop');
            $adv_user = $model_register->getRegistrationDefaultData();
            $dispatcher->triggerEvent('onBeforeDisplayRegister', array(&$adv_user));
        }

        $view = $this->getView('user');
        $view->setLayout("login");
        $view->set('href_register', Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=register',1,0, $jshopConfig->use_ssl));
        $view->set('href_lost_pass', Helper::SEFLink('index.php?option=com_users&view=reset',0,0, $jshopConfig->use_ssl));
        $view->set('return', $return);
        $view->set('Itemid', $this->input->getVar('Itemid'));
        $view->set('config', $jshopConfig);
        $view->set('show_pay_without_reg', $show_pay_without_reg);
        $view->set('config_fields', $config_fields);
        $view->set('cssreq_fields', $cssreq_fields);
        $view->set('live_path', Uri::base());
        $view->set('urlcheckdata', Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=check_user_exist_ajax&ajax=1", 1, 1, $jshopConfig->use_ssl));
        $view->set('urlcheckpassword', Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=check_user_password&ajax=1",1,1, $jshopConfig->use_ssl));
        $view->set('checkout_navigator', $checkout_navigator);
        $view->set('allowUserRegistration', $allowUserRegistration);
        if (isset($adv_user)) {
            $view->set('user', $adv_user);
        }
        $view->tmpl_login_html_1 = "";
        $view->tmpl_login_html_2 = "";
        $view->tmpl_login_html_3 = "";
        $view->tmpl_login_html_4 = "";
        $view->tmpl_login_html_5 = "";
        $view->tmpl_login_html_6 = "";
        $view->_tmpl_register_html_1 = "";
        $view->_tmpl_register_html_2 = "";
        $view->_tmpl_register_html_3 = "";
        $view->_tmpl_register_html_4 = "";
        $view->_tmpl_register_html_5 = "";
		$view->_tmpl_register_html_51 = "";
        $view->_tmpl_register_html_6 = "";
        $dispatcher->triggerEvent('onBeforeDisplayLoginView', array(&$view));
		if ($jshopConfig->show_registerform_in_logintemplate){
            $dispatcher->triggerEvent('onBeforeDisplayRegisterView', array(&$view));
        }
        $view->display();
    }
    
    function loginsave(){        
        $app = Factory::getApplication();        
		Factory::getApplication()->triggerEvent('onBeforeLoginSave', array());
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        
        $method = $this->input->getMethod();        
        $remember = $this->input->getBool('remember', false);
        $username = $this->input->$method->get('username', '', 'USERNAME');
        $password = (string)$this->input->$method->get('passwd', '', 'RAW');        

        $model = JSFactory::getModel('userlogin', 'Site');
        if ($model->login($username, $password, array('remember'=>$remember))){
			Helper::setNextUpdatePrices();
            $app->redirect($model->getReturnUrl());
        }else{            
            $app->redirect($model->getUrlBackToLogin());
        }
    }
    
    function check_user_exist_ajax(){
        $username = $this->input->getVar("username");
        $email = $this->input->getVar("email");
		print JSFactory::getTable('userShop')->checkUserExistAjax($username, $email);
        die();
    }
    
    function check_user_password(){
        $pass = $this->input->getVar("pass");
        Factory::getLanguage()->load('com_users');
        $checkfield = JSFactory::getModel('usercheckfield', 'Site');
        $res = (int)$checkfield->password($pass);
        print json_encode(array('res'=>$res, 'msg'=>$checkfield->getLastErrorMsg()));
        die();
    }
    
    function register(){
        $jshopConfig = JSFactory::getConfig();
		$dispatcher = Factory::getApplication();
        $model = JSFactory::getModel('userregister', 'Site');
        $adv_user = $model->getRegistrationDefaultData();

        Metadata::userRegister();
        
        if ($model->getUserParams()->get('allowUserRegistration') == '0'){
            JSError::raiseError(403, Text::_('Access Forbidden - Allowing user registration in Joomla configuration'));
            return;
        }
        
		$config_fields = $jshopConfig->getListFieldsRegisterType('register');
        $cssreq_fields = $jshopConfig->getListFieldsRegisterTypeClassRequired('register', $config_fields);

        $dispatcher->triggerEvent('onBeforeDisplayRegister', array(&$adv_user));
        
        Helper::filterHTMLSafe($adv_user, ENT_QUOTES);
        
        $checkout_navigator = JSFactory::getModel('checkout', 'Site')->showCheckoutNavigation('1');
        
        $view = $this->getView('user');
        $view->setLayout("register"); 
        $view->set('config', $jshopConfig);        
        $view->set('config_fields', $config_fields);
        $view->set('cssreq_fields', $cssreq_fields);
        $view->set('user', $adv_user);
        $view->set('live_path', Uri::base());        
        $view->set('urlcheckdata', Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=check_user_exist_ajax&ajax=1",1,1));
        $view->set('urlcheckpassword', Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=check_user_password&ajax=1",1,1));
        $view->set('checkout_navigator', $checkout_navigator);
        $view->_tmpl_register_html_1 = "";
        $view->_tmpl_register_html_2 = "";
        $view->_tmpl_register_html_3 = "";
        $view->_tmpl_register_html_4 = "";
        $view->_tmpl_register_html_5 = "";
		$view->_tmpl_register_html_51 = "";
        $view->_tmpl_register_html_6 = "";
        $dispatcher->triggerEvent('onBeforeDisplayRegisterView', array(&$view));
        $view->display();
    }
    
    function registersave(){
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));        
        $jshopConfig = JSFactory::getConfig();
        $dispatcher = Factory::getApplication();
        Factory::getLanguage()->load('com_users');
        $model = JSFactory::getModel('userregister', 'Site');
        $params = $model->getUserParams();
        $useractivation = $params->get('useractivation');
        $post = $this->input->post->getArray();

        if ($params->get('allowUserRegistration')==0){
            JSError::raiseError(403, Text::_('Access Forbidden'));
            return;
        }
		
		$back_url = Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=register&lrd=1",1,1, $jshopConfig->use_ssl);

        $model->setData($post);
        
        if (!$model->check()){
            JSError::raiseWarning('', $model->getError());
            $this->setRedirect($back_url);
            return 0;
        }
		if (!$model->save()){
            JSError::raiseWarning('', $model->getError());            
            $this->setRedirect($back_url);
            return 0;
        }
        $model->mailSend();
        
        $user = $model->getUserJoomla();
        $usershop = $model->getUser();
        
        $dispatcher->triggerEvent('onAfterRegister', array(&$user, &$usershop, &$post, &$useractivation));

        $message = $model->getMessageUserRegistration($useractivation);
        $return = Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=login",1,1,$jshopConfig->use_ssl);

        $this->setRedirect($return, $message);
    }
    
    function activate(){
        $jshopConfig = JSFactory::getConfig();
		$model = JSFactory::getModel('useractivate', 'Site');
        Factory::getLanguage()->load('com_users');		
		$token = $this->input->getVar('token');
        if (Factory::getUser()->get('id')){
            $this->setRedirect('index.php');
            return true;
        }
		if (!$model->check($token)){
			JSError::raiseError(403, $model->getError());
            return false;
		}

        $return = $model->activate($token);

        if ($return === false){
            $this->setMessage(Text::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=login",1,1,$jshopConfig->use_ssl));
            return false;
        }
		
		$msg = $model->getMessageUserActivation($return);
        $this->setMessage($msg);
        $this->setRedirect(Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=login",1,1,$jshopConfig->use_ssl));
        return true;
    }
    
    function editaccount(){
        Helper::checkUserLogin();        
        $adv_user = JSFactory::getUserShop()->loadDataFromEdit();
        $jshopConfig = JSFactory::getConfig();
            
        Metadata::userEditaccount();
		
		$select_countries = Selects::getCountry($adv_user->country);
		$select_d_countries = Selects::getCountry($adv_user->d_country, null, 'd_country');
		$select_titles = Selects::getTitle($adv_user->title);
		$select_d_titles = Selects::getTitle($adv_user->d_title, null, 'd_title');
		$select_client_types = Selects::getClientType($adv_user->client_type);
        
		$config_fields = $jshopConfig->getListFieldsRegisterType('editaccount');
        $cssreq_fields = $jshopConfig->getListFieldsRegisterTypeClassRequired('editaccount', $config_fields);
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('editaccount');

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayEditUser', array(&$adv_user));
        
        Helper::filterHTMLSafe( $adv_user, ENT_QUOTES);      

        $view = $this->getView('user');
        $view->setLayout("editaccount");        
		$view->set('config',$jshopConfig);
        $view->set('select_countries',$select_countries);
        $view->set('select_d_countries',$select_d_countries);
        $view->set('select_titles',$select_titles);
        $view->set('select_d_titles',$select_d_titles);
        $view->set('select_client_types', $select_client_types);
        $view->set('live_path', Uri::base());
        $view->set('user', $adv_user);
        $view->set('delivery_adress', $adv_user->delivery_adress);
        $view->set('action', Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=accountsave',1,0,$jshopConfig->use_ssl));
        $view->set('config_fields', $config_fields);
        $view->set('cssreq_fields', $cssreq_fields);
        $view->set('count_filed_delivery', $count_filed_delivery);
        $view->_tmpl_editaccount_html_1 = "";
        $view->_tmpl_editaccount_html_2 = "";
        $view->_tmpl_editaccount_html_3 = "";
        $view->_tmpl_editaccount_html_4 = "";
        $view->_tmpl_editaccount_html_4_1 = "";
        $view->tmp_fields = "";
        $view->tmp_d_fields = "";
        $view->_tmpl_editaccount_html_5 = "";
        $view->_tmpl_editaccount_html_6 = "";
        $view->_tmpl_editaccount_html_7 = "";
        $view->_tmpl_editaccount_html_8 = "";
        $dispatcher->triggerEvent('onBeforeDisplayEditAccountView', array(&$view));
        $view->display();
    }
    
    function accountsave(){
        Helper::checkUserLogin();
		$post = $this->input->post->getArray();
        $jshopConfig = JSFactory::getConfig();
		Factory::getLanguage()->load('com_users');
		$model = JSFactory::getModel('useredit', 'Site');
		
		$error_back_url = Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=editaccount",1,1, $jshopConfig->use_ssl);
		
		Factory::getApplication()->triggerEvent('onBeforeAccountSave', array(&$post));
		
		$model->setUserId(Factory::getUser()->id);
		$model->setData($post);
		if (!$model->check("editaccount")){
            JSError::raiseWarning('', $model->getError());
            $this->setRedirect($error_back_url);
            return 0;
        }
		if (!$model->save()){
            JSError::raiseWarning('500', Text::_('JSHOP_REGWARN_ERROR_DATABASE'));
            $this->setRedirect($error_back_url);
            return 0;
        }
		$model->updateJoomlaUserCurrentProfile();
        
        Helper::setNextUpdatePrices();
        Factory::getApplication()->triggerEvent('onAfterAccountSave', array(&$model));
        
        $this->setRedirect(Helper::SEFLink("index.php?option=com_jshopping&controller=user",1,1,$jshopConfig->use_ssl), Text::_('JSHOP_ACCOUNT_UPDATE'));
    }
    
    function orders(){
        $jshopConfig = JSFactory::getConfig();
		$dispatcher = Factory::getApplication();
        Helper::checkUserLogin();
		$model = JSFactory::getModel('userOrders', 'Site');
        
        Metadata::userOrders();
		
		$model->setUserId(Factory::getUser()->id);
		$orders = $model->getListOrders();
		$total = $model->getTotal();

        $dispatcher->triggerEvent('onBeforeDisplayListOrder', array(&$orders, &$model));

        $view = $this->getView('order');
        $view->setLayout("listorder");
        $view->set('orders', $orders);
        $view->set('image_path', $jshopConfig->live_path."images");
        $view->set('total', $total);
        $view->_tmp_html_before_user_order_list = "";
        $view->_tmp_html_after_user_order_list = "";
        $dispatcher->triggerEvent('onBeforeDisplayOrdersView', array(&$view));
        $view->display();
    }
    
    function order(){
        $jshopConfig = JSFactory::getConfig();
        Helper::checkUserLogin();
        $user = Factory::getUser();
        $dispatcher = Factory::getApplication();
        
        $order_id = $this->input->getInt('order_id');
		
        $order = JSFactory::getTable('order');
        $order->load($order_id);
        $dispatcher->triggerEvent('onAfterLoadOrder', array(&$order, &$user));
		
		Metadata::userOrder($order);
        
        if ($user->id!=$order->user_id) {
            JSError::raiseError(500, "Error order number. You are not the owner of this order");
			return 0;
        }
		
		$order->prepareOrderPrint('order_show');
		$allow_cancel = $order->getClientAllowCancel();        
        $show_percent_tax = $order->getShowPercentTax();
        $hide_subtotal = $order->getHideSubtotal();
        $text_total = $order->getTextTotal();
		$order->fixConfigShowWeightOrder();        
		$config_fields = $jshopConfig->getListFieldsRegisterType('address');
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');
        $order->loadItemsNewDigitalProducts();

        $dispatcher->triggerEvent('onBeforeDisplayOrder', array(&$order));

        $view = $this->getView('order');
        $view->setLayout("order");
        $view->set('order', $order);
        $view->set('config', $jshopConfig);
        $view->set('text_total', $text_total);
        $view->set('show_percent_tax', $show_percent_tax);
        $view->set('hide_subtotal', $hide_subtotal);
        $view->set('image_path', $jshopConfig->live_path."images");
        $view->set('config_fields', $config_fields);
        $view->set('count_filed_delivery', $count_filed_delivery);
        $view->set('allow_cancel', $allow_cancel);
        $view->_tmp_html_start = "";
        $view->_tmp_html_after_customer_info = "";
        $view->_tmpl_html_order_items_end = "";
        $view->_tmp_ext_subtotal = "";
        $view->_tmp_html_after_subtotal = "";
        $view->_tmp_ext_discount_text = "";
        $view->_tmp_ext_discount = "";
        $view->_tmp_ext_shipping = "";
        $view->_tmp_ext_shipping_package = "";
        $view->_tmp_ext_payment = "";
        $view->_tmp_ext_total = "";
        $view->_tmp_ext_tax = array();
        foreach($order->order_tax_list as $percent=>$value) {
            $view->_tmp_ext_tax[$percent] = "";
        }
        $view->_tmp_html_after_total = "";
        $view->_tmp_html_shipping_block_info_end = "";
        $view->_tmp_html_after_comment = "";
        $view->_tmp_html_after_history = "";
        $view->_tmp_html_end = "";
        $view->tmp_fields = "";
        $view->tmp_d_fields  = "";
        $dispatcher->triggerEvent('onBeforeDisplayOrderView', array(&$view));
        $view->display();
    }
    
    function cancelorder(){
        $jshopConfig = JSFactory::getConfig();
        Helper::checkUserLogin();
		$order_id = $this->input->getInt('order_id');
		$back_url = Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=order&order_id=".$order_id,1,1,$jshopConfig->use_ssl);
		
		$model = JSFactory::getModel('userOrder', 'Site');
		$model->setUserId(Factory::getUser()->id);
		$model->setOrderId($order_id);
		if (!$model->userOrderCancel()){
			JSError::raiseWarning('', $model->getError());
			$this->setRedirect($back_url);
			return 0;
		}
		
		$this->setRedirect($back_url, Text::_('JSHOP_ORDER_CANCELED'));
    }

    function myaccount(){
        $jshopConfig = JSFactory::getConfig();
        Helper::checkUserLogin();

        $adv_user = JSFactory::getUserShop();
		$adv_user->prepareUserPrint();

        Metadata::userMyaccount();
        
		$config_fields = $jshopConfig->getListFieldsRegisterType('editaccount');

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayMyAccount', array(&$adv_user, &$config_fields));
		
        $view = $this->getView('user');
        $view->setLayout("myaccount");
        $view->set('config', $jshopConfig);
        $view->set('user', $adv_user);
        $view->set('config_fields', $config_fields);
        $view->set('href_user_group_info', Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=groupsinfo'));
        $view->set('href_edit_data', Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=editaccount', 1,0,$jshopConfig->use_ssl));
        $view->set('href_show_orders', Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=orders', 1,0,$jshopConfig->use_ssl));
        $view->set('href_logout', Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=logout', 1));
        $view->tmpl_my_account_html_start = "";
        $view->tmpl_my_account_html_content = "";
        $view->tmpl_my_account_html_end = "";
        $dispatcher->triggerEvent('onBeforeDisplayMyAccountView', array(&$view));
        $view->display();
    }
    
    function groupsinfo(){
        $jshopConfig = JSFactory::getConfig();
        Metadata::userGroupsinfo();
        
        $group = JSFactory::getTable('userGroup');
        $list = $group->getList();

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayGroupsInfo', array());

        $view = $this->getView('user');
        $view->setLayout("groupsinfo");
        $view->set('rows', $list);
        $view->_tmpl_start = '';
		$view->_tmpl_end = '';
        $dispatcher->triggerEvent('onBeforeDisplayGroupsInfoView', array(&$view));
        $view->display();
    }
    
    function logout(){
		$model = JSFactory::getModel('userlogin', 'Site');
		$model->logout();
		Helper::setNextUpdatePrices();
		Factory::getApplication()->redirect($model->getReturnUrl());
    }
	
	function logoutpage(){        
        $checkout_navigator = JSFactory::getModel('checkout', 'Site')->showCheckoutNavigation('1');

		$view = $this->getView('user');
		$view->setLayout("logout");
		$view->set('checkout_navigator', $checkout_navigator);            
		$view->display();
	}
    
}