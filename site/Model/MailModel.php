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

abstract class MailModel  extends BaseModel{
	
    protected $params;
    protected $data = array();

    public function getParams(){
        return $this->params;
    }
    
    public function setParams($params){
        return $this->params = $params;
    }
    
    public function setData($data){
        $this->data = $data;
    }
    
    public function getData(){
        return $this->data;
    }
	
	public function getListAdminUserSendEmail(){
        $db = \JFactory::getDBO();
        $query = 'SELECT name, email, sendEmail FROM #__users WHERE sendEmail=1';
        $db->setQuery( $query );
        return $db->loadObjectList();
    }
    
    abstract public function send();
        
}