<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class UserregisterModel  extends UserbaseModel{
        
    private $data = array();
    private $userjoomla_data = array();
    private $default_usergroup = null;
    private $user_joomla_id = 0;
    private $user;
    private $user_joomla;
    private $post_data = null;
	private $admin_registration = 0;
    
    public function __construct(){
        $this->loadUserParams();
        $this->user = \JSFactory::getTable('userShop');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshopUserregister', array(&$obj));
    }

    public function prepateData(&$post){
        $jshopConfig = \JSFactory::getConfig();
        $usergroup = \JSFactory::getTable('usergroup');
        $dispatcher = \JFactory::getApplication();
        
        $this->default_usergroup = $usergroup->getDefaultUsergroup();   
        if (!isset($post['u_name'])) $post['u_name'] = '';
        if (!isset($post['f_name'])) $post['f_name'] = '';
        if (!isset($post['l_name'])) $post['l_name'] = '';     
        $post['username'] = $post['u_name'];
		if (isset($post['password_2']) && $post['password_2']!=''){
			$post['password2'] = $post['password_2'];
		}
        if ($post['f_name']=="") $post['f_name'] = $post['email'];
        $post['name'] = $post['f_name'].' '.$post['l_name'];
        if (isset($post['birthday']) && $post['birthday']) $post['birthday'] = \JSHelper::getJsDateDB($post['birthday'], $jshopConfig->field_birthday_format);
        $post['lang'] = $jshopConfig->getLang();
		
        $dispatcher->triggerEvent('onBeforeRegister', array(&$post, &$this->default_usergroup, &$this->userparams));        
    }
    
    public function setData(&$data){
        if (!$this->post_data){
            $this->post_data = $data;
        }
		$this->prepateData($data);
        $this->data = &$data;
        $this->user->bind($data);
		if (!$this->admin_registration){
			$this->user->usergroup_id = $this->default_usergroup;
		}
    }
    
    public function check($type = "register"){
		$jshopConfig = \JSFactory::getConfig();
		foreach($jshopConfig->fields_client_only_check as $_field) {
			$this->user->$_field = isset($this->data[$_field]) ? $this->data[$_field] : null;
		}        
        if (!$this->user->check($type)){
            $this->savePostData();
            $this->setError($this->user->getError());
            $res = 0;
        }else{
            $res = 1;
        }
		foreach($jshopConfig->fields_client_only_check as $_field) {
			unset($this->user->$_field);
		}
        return $res;
    }
    
    private function savePostData(){
        $session = \JFactory::getSession();            
        $session->set('registrationdata', $this->post_data);		
    }
    
    public function getPostData(){
        $session = \JFactory::getSession();            
        return $session->get('registrationdata');
    }
    
    public function getRegistrationDefaultData(){
        if (\JFactory::getApplication()->input->getInt('lrd')){
            $data = (object)$this->getPostData();
            if (!isset($data->country)) $data->country = 0;
            if (!isset($data->title)) $data->title = 0;
            if (!isset($data->client_type)) $data->client_type = 0;
        }else{
            $data = new \stdClass();
            $data->country = 0;
            $data->title = 0;
            $data->client_type = 0;
            $data->f_name = "";
            $data->l_name = "";
            $data->m_name = "";
            $data->firma_name = "";
            $data->firma_code = "";
            $data->tax_number = "";
            $data->email = "";
            $data->email2 = "";
            $data->birthday = "";
            $data->home = "";
            $data->apartment = "";
            $data->street = "";
            $data->street_nr = "";
            $data->zip = "";
            $data->city = "";
            $data->state = "";
            $data->phone = "";
            $data->mobil_phone = "";
            $data->fax = "";
            $data->ext_field_1 = "";
            $data->ext_field_2 = "";
            $data->ext_field_3 = "";
            $data->u_name = "";
        }
        if (!$data->country){
            $data->country = \JSFactory::getConfig()->default_country;
        }
        return $data;
    }

    public function userJoomlaSave(){
		$jshopConfig = \JSFactory::getConfig();
        $post = $this->data;
        $params = $this->getUserParams();
		$fieldRegister = $jshopConfig->getListFieldsRegisterType('register');
        if ($post["u_name"]==""){
            $post["u_name"] = $post['email'];
            $this->user->u_name = $post["u_name"];
        }
		if (!$fieldRegister['password']['display'] && !$this->getAdminRegistration()){
			$post["password"] = '';
		}
        if ($post["password"]==""){
            $post["password"] = substr(md5('up'.time()), 0, 8);
        }
        $user = new \JUser;
        $data = array();
        $data['groups'][] = $params->get('new_usertype', 2);
        $data['email'] = $post['email'];
        $data['password'] = $post['password'];
        $data['password2'] = isset($post['password2']) ? $post['password2'] : null;
        $data['name'] = $post['f_name'].' '.$post['l_name'];
        $data['username'] = $post["u_name"];
        $useractivation = $params->get('useractivation');        

		if ($this->admin_registration){
			$data['block'] = $post['block'];
		}else{
			if ($useractivation == 1 || $useractivation == 2){
				jimport('joomla.user.helper');
				$data['activation'] = \JApplicationHelper::getHash(\JUserHelper::genRandomPassword());
				$data['block'] = 1;
			}
		}
        $this->userjoomla_data = $data;
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "beforeBind"));        
        $user->bind($data);
        if (!$user->save()){
            $this->user_joomla_id = 0;
			$this->savePostData();
            \JSHelper::saveToLog('error.log', 'Error registration-'.$user->getError());
            $this->setError($user->getError());
            return 0;
        }else{
            $this->user_joomla = $user;
            $this->user_joomla_id = $user->id;
            return $user->id;
        }
    }
    
    public function userSave(){
        if (!$this->user_joomla_id){
            throw new Exception('Error jshopUserregister->user_joomla_id');
        }
        $db = \JFactory::getDBO();
        $this->user->user_id = $this->user_joomla_id;		
        $this->user->number =  $this->user->getNewUserNumber();        
        if (!$db->insertObject($this->user->getTableName(), $this->user, $this->user->getKeyName())){
			$this->savePostData();
            \JSHelper::saveToLog('error.log', $db->getErrorMsg());
            $this->setError("Error insert in table ".$this->user->getTableName());
            return 0;
        }else{
            return 1;
        }
    }
	
	public function save(){
		if (!$this->userJoomlaSave()){
			return 0;
		}
		if (!$this->userSave()){
			return 0;
		}
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
		return 1;
	}
    
    public function mailSend($send_to_admin = 1){
        $config = \JFactory::getConfig();
        $data = $this->user_joomla->getProperties();
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['siteurl'] = \JURI::base();
        $uri = \JURI::getInstance();
        $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
        $data['activate'] = $base.\JRoute::_('index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'], false);
        $data['linkactivate'] = $data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'];
        
        $usermail = \JSFactory::getModel('userMailRegister', 'Site');
        $usermail->setData($data);
        $usermail->setParams($this->userparams);
        $usermail->setRegistrationRequestData($this->data);
        $usermail->send();
        if ($send_to_admin && ($this->userparams->get('useractivation') < 2) && ($this->userparams->get('mail_to_admin') == 1)){            
            $usermail->sendToAdmin();
        }
    }
    
    public function setUser($user){
        return $this->user = $user;
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function getUserJoomla(){
        return $this->user_joomla;
    }
    
    public function getUserJoomlaId(){
        return $this->user_joomla_id;
    }
    
    public function setUserJoomlaId($id){
        $this->user_joomla_id = $id;
    }
	
	public function setAdminRegistration($val){
		$this->admin_registration = $val;
	}
	
	public function getAdminRegistration(){
		return $this->admin_registration;
	}
	
	public function getMessageUserRegistration($useractivation){
		if ($useractivation == 2){
            $message  = \JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY');
        } elseif ($useractivation == 1){
            $message  = \JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE');
        } else {
            $message = \JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');
        }
		return $message;
	}
	
	public function getRequestData(){
		return $this->post_data;
	}
	
	public function getData(){
		return $this->data;
	}

}