<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die('');

class UserShopTable extends UsershopbaseTable{

    function __construct(&$_db){
        parent::__construct($_db);
    }
    
	function isUserInShop($id) {
		$db = \JFactory::getDBO();
		$query = "SELECT user_id FROM `#__jshopping_users` WHERE `user_id`='".$db->escape($id)."'";
		$db->setQuery($query);
		$res = $db->execute();
		return $db->getNumRows($res);
	}
    	
	function addUserToTableShop($user){
		$db = \JFactory::getDBO();
		$this->u_name = $user->username;
		$this->email = $user->email;
		$this->user_id = $user->id;
        $number = $this->getNewUserNumber();
        $default_usergroup = \JSFactory::getTable('usergroup')->getDefaultUsergroup();
        
		$query = "INSERT INTO `#__jshopping_users` SET `usergroup_id`='".$default_usergroup."', `u_name`='".$db->escape($user->username)."', 
				 `email`='".$db->escape($user->email)."', `user_id`='".$db->escape($user->id)."', f_name='".$db->escape($user->name)."', 
				 `number`='".$db->escape($number)."'";
		$db->setQuery($query);
		$db->execute();
		$obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterAddUserToTableShop', array(&$obj));
	}
    
    function store($updateNulls = false){
		if (isset($this->preparePrint) && $this->preparePrint==1){
            throw new Exception('Error JshopUserShop::store()');
        }
        if (isset($this->percent_discount)) {
            $tmp = $this->percent_discount;
            unset($this->percent_discount);
        }
		$obj = $this;
        \JFactory::getApplication()->triggerEvent('onBeforeStoreTableShop', array(&$obj));
        $res = parent::store($updateNulls);
		if (isset($tmp)) {
            $this->percent_discount = $tmp;
        }
        return $res;
    }
    
	function getCountryId($id_user) {
		$db = \JFactory::getDBO();
		$query = "SELECT country FROM `#__jshopping_users` WHERE user_id=".(int)$id_user;
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	function getDiscount(){
		$db = \JFactory::getDBO(); 
		$query = "SELECT usergroup.usergroup_discount FROM `#__jshopping_usergroups` AS usergroup
				  INNER JOIN `#__jshopping_users` AS users ON users.usergroup_id = usergroup.usergroup_id
				  WHERE users.user_id = '".$db->escape($this->user_id)."' ";
		$db->setQuery($query);
		return floatval($db->loadResult());
	}
	
	function getIdUserFromField($field, $value){
		$db = \JFactory::getDBO();
		$query = "SELECT id FROM `#__users` WHERE ".$db->quoteName($field)." = '".$db->escape($value)."'";
		$db->setQuery($query);
		return $db->loadResult();
	}
    
    function getNewUserNumber(){
        $number = $this->user_id;
		$obj = $this;
        \JFactory::getApplication()->triggerEvent('onBeforeGetNewUserNumber', array(&$obj, &$number));
        return $number;
    }
	
	function prepareUserPrint(){
		$this->preparePrint = 1;
		
		if (!isset($this->country_id)){
            $this->country_id = $this->country;
            $this->d_country_id = $this->d_country;
        }
		
		$country = \JSFactory::getTable('country');
        $country->load($this->country_id);
        $this->country = $country->getName();
        
        $d_country = \JSFactory::getTable('country');
        $d_country->load($this->d_country_id);
        $this->d_country = $d_country->getName();
		
		$group = \JSFactory::getTable('userGroup');
        $group->load($this->usergroup_id);
		$this->groupname = $group->getName();	
        $this->discountpercent = floatval($group->usergroup_discount);
	}
	
	function checkUserExistAJax($username='', $email=''){
		$dispatcher = \JFactory::getApplication();
        $mes = array();
        $dispatcher->triggerEvent('onBeforeUserCheck_user_exist_aJax', array(&$mes, &$username, &$email));        
		if ($username && $this->getIdUserFromField('username', $username)){
			$mes[] = sprintf(\JText::_('JSHOP_USER_EXIST'), $username);			
		}
        if ($email && $this->getIdUserFromField('email', $email)){			
			$mes[] = sprintf(\JText::_('JSHOP_USER_EXIST_EMAIL'), $email);			
		}
        $dispatcher->triggerEvent('onAfterUserCheck_user_exist_aJax', array(&$mes, &$username, &$email));
        if (count($mes)==0){
            return "1";
        }else{
            return implode("\n", $mes);
        }
	}
}