<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\Component\Jshopping\Site\Table\UsershopbaseTable;
defined('_JEXEC') or die();

class UserGuestModel  extends UsershopbaseTable{
    
    function __construct(){
        $db = \JFactory::getDBO();
		parent::__construct($db);
    }
    
    function load($keys = null, $reset = true){
        $jshopConfig = \JSFactory::getConfig();
        $session = \JFactory::getSession();
        $objuser = $session->get('user_shop_guest');
        if (isset($objuser) && $objuser!=''){
            $tmp = unserialize($objuser);
            foreach($tmp as $k=>$v){
                $this->$k = $v;
            }
        }
        $this->user_id = -1;
        $usergroup = \JSFactory::getTable('usergroup');
        $this->usergroup_id = isset($jshopConfig->default_usergroup_id_guest) ? intval($jshopConfig->default_usergroup_id_guest) : 0;
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onLoadJshopUserGust', array(&$obj));
    return true;
    }
    	
    function store($updateNulls = false){
        $this->user_id = -1;
        $session = \JFactory::getSession();
        $properties = $this->getProperties();
        $session->set('user_shop_guest', serialize($properties));
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterStoreJshopUserGust', array(&$obj));
    return true;
    }
	
	function check($type = ''){
		return $this->checkData($type, 0);
	}

}