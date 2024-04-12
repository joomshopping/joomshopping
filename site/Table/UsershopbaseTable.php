<?php
/**
* @version      5.2.0 05.06.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die('');

abstract class UsershopbaseTable extends ShopbaseTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_users', 'user_id', $_db);
        \JPluginHelper::importPlugin('jshoppingcheckout');
        $obj = $this;
		\JFactory::getApplication()->triggerEvent('onConstruct'.ucfirst(get_class($this)), array(&$obj));
    }
	
	function check($type = ''){
		return $this->checkData($type, 1);
	}
    
	function checkData($type, $check_exist_email){
        $db = \JFactory::getDBO();
		$JshopConfig = \JSFactory::getConfig();
        $checkfield = \JSFactory::getModel('usercheckfield', 'Site');
        $return = true;

        $types = explode(".", $type);
        $type = $types[0];
        if (isset($types[1])){
            $type2 = $types[1];
        }else{
            $type2 = '';
        }
        
		$config_fields = $JshopConfig->getListFieldsRegisterType($type);
        $fields_client_check = $JshopConfig->fields_client_check;
		$obj = $this;
        \JFactory::getApplication()->triggerEvent('onBeforeCheck'.ucfirst(get_class($this)), array(&$obj, &$type, &$config_fields, &$type2, &$return, &$fields_client_check));

        foreach ($config_fields as $field => $v) {
            if ($field == 'password_2') {
                $field = 'password2';
            }
            if ((substr($field, 0, 2) != 'd_' || $this->delivery_adress) && isset($v['require']) && $v['require'] == 1) {
                $typecheck = $fields_client_check[$field][0];
                if (isset($fields_client_check[$field][2])) {
                    $callback = $fields_client_check[$field][2];
                    if (!$callback($this, $config_fields)) {
                        continue;
                    }
                }
				
				try {
					if ($typecheck && !$checkfield->$typecheck($this->$field)) {
						$this->_error = \JText::_($fields_client_check[$field][1]);
						return false;
					}
				} catch (\Exception $e) {
					$this->_error = $e->getMessage();
					return false;
				}
            }
        }        
		
		if ($this->u_name!=''){
			if (preg_match("#[<>\"'%;()&]#i", $this->u_name) || strlen(utf8_decode($this->u_name )) < 2) {
				$this->_error = sprintf((\JText::_('JSHOP_VALID_AZ09')),(\JText::_('JSHOP_USERNAME')),2);
				return false;
			}
			$query = "SELECT id FROM #__users WHERE username = '".$db->escape($this->u_name)."' AND id != ".(int)$this->user_id;
            $obj = $this;
			\JFactory::getApplication()->triggerEvent('onBeforeCheckUserNameExistJshopUserShop', array(&$obj, &$type, &$config_fields, &$type2, &$query));
			$db->setQuery($query);
			$xid = intval($db->loadResult());
			if ($xid && $xid != intval($this->user_id)){
				$this->_error = (\JText::_('JSHOP_REGWARN_INUSE'));
				return false;
			}
		}

		if (!isset($config_fields['password'])){
			$config_fields['password'] = ['display' => 0, 'require' => 0];
		}
		if (($config_fields['password']['require'] || ($config_fields['password']['display'] && $this->password)) && !$checkfield->password($this->password)){
			$this->_error = $checkfield->getLastErrorMsg();
			return false;
		}

		if (isset($config_fields['password_2']['display']) && $config_fields['password_2']['display'] && ($this->password || $this->password2) && $this->password!=$this->password2){
			$this->_error = \JText::_('JSHOP_REGWARN_PASSWORD_NOT_MATCH');
			return false;
		}

		if (isset($config_fields['email2']['display']) && $config_fields['email2']['display'] && ($this->email && $this->email2) && $this->email != $this->email2){
			$this->_error = \JText::_('JSHOP_REGWARN_EMAIL_NOT_MATCH');
			return false;
		}

		if ($this->email!='' && $check_exist_email){
			$query = "SELECT id FROM #__users WHERE email='".$db->escape($this->email)."' AND id != ".(int)$this->user_id;
            $obj = $this;
            \JFactory::getApplication()->triggerEvent('onBeforeCheckUserEmailExistJshopUserShop', array(&$obj, &$type, &$config_fields, &$type2, &$query));
			$db->setQuery($query);
			if (intval($db->loadResult())){
				$this->_error = (\JText::_('JSHOP_REGWARN_EMAIL_INUSE'));
				return false;
			}
		}
        
		return $return;
	}
    
    function saveTypePayment($id){
        $this->payment_id = $id;
        $this->store();
        return 1;
    }
    
    function saveTypeShipping($id){
        $this->shipping_id = $id;
        $this->store();
        return 1;
    }
    
    function getError($i = null, $toString = true){
        return $this->_error;
    }
    
    function setError($error){
        $this->_error = $error;
    }
    
	function loadDataFromEdit(){
		$this->prepareBirthdayFormat();
		$this->updateCountryToDefault();
		return $this;
	}
	
    function updateCountryToDefault(){
        $JshopConfig = \JSFactory::getConfig();
        if (!$this->country) $this->country = $JshopConfig->default_country;
        if (!$this->d_country) $this->d_country = $JshopConfig->default_country;
    }

    function prepareBirthdayFormat(){
        $JshopConfig = \JSFactory::getConfig();        
        $this->birthday = \JSHelper::getDisplayDate($this->birthday, $JshopConfig->field_birthday_format);
        $this->d_birthday = \JSHelper::getDisplayDate($this->d_birthday, $JshopConfig->field_birthday_format);
    }
	
}