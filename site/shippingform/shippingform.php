<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

abstract class ShippingFormRoot{
    
    public $_errormessage = "";
	private $_sh_params;
    
    abstract function showForm($shipping_id, $shippinginfo, $params);
    
    function check($params, $sh_method){
        return 1;
    }
    
    /**
    * Set message error check
    */
    function setErrorMessage($msg){
        $this->_errormessage = $msg;
    }
    
    /**
    * Get message error check
    */
    function getErrorMessage(){
        return $this->_errormessage;
    }
	
	/**
    * get current params
    */
    function getParams(){
        return $this->_sh_params;
    }
    
    /**
    * set params
    */
    function setParams($params){
        $this->_sh_params = $params;
    }
    
    /**
    * list display params name shipping saved to order
    */
    function getDisplayNameParams(){
        return array();
    }
    
    /**
    * exec before mail send
    */
    function prepareParamsDispayMail(&$order, &$sh_method){
    }

}