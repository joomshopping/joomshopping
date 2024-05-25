<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
defined('_JEXEC') or die();

class UserloginModel{
    
    private $return_url = '';
    
    function login($login, $passwd, $params = array()){        
        $app = Factory::getApplication();
        $dispatcher = Factory::getApplication();		       
        $return = $this->getRequestReturnUrl();
        $options = array();
        $options['remember'] = $params['remember'];
        $options['return'] = $return;

        $credentials = array();
        $credentials['username'] = $login;
        $credentials['password'] = $passwd;
        
        $dispatcher->triggerEvent('onBeforeLogin', array(&$options, &$credentials) );
        
        $error = $app->login($credentials, $options);

        if ((!JSError::isError($error)) && ($error !== FALSE)){
            if (!$return ){
                $return = Uri::base();
            }
            $this->setReturnUrl($return);
            $dispatcher->triggerEvent('onAfterLogin', array(&$options, &$credentials));
            $logged = 1;
        }else{
            $dispatcher->triggerEvent('onAfterLoginEror', array(&$options, &$credentials));
            $this->setReturnUrl($return);
            $logged = 0;
        }
        return $logged;
    }
	
	public function logout(){
		$app = Factory::getApplication();
        $dispatcher = Factory::getApplication();
		$session = Factory::getSession();
		
        $dispatcher->triggerEvent('onBeforeLogout', array());

        $error = $app->logout();

        $session->set('user_shop_guest', null);
        $session->set('cart', null);

        if (!JSError::isError($error)){
            $return = $this->getRequestReturnUrl();
			
            $dispatcher->triggerEvent('onAfterLogout', array(&$return));
			
            if ( $return && !( strpos( $return, 'com_user')) ){
                $this->setReturnUrl( $return );
            }else{
                $this->setReturnUrl(Uri::base());
            }
            $obj = $this;
			$dispatcher->triggerEvent('onAfterShopLogout', array(&$obj));
        }
	}
	
	public function getUrlHash(){
		$session = Factory::getSession();
        $app = Factory::getApplication();
		if ($app->input->getVar('return')){
            $return = $app->input->getVar('return');
        }else{
            $return = $session->get('return');
        }
		return $return;
	}
	
	public function getPayWithoutReg(){
		$session = Factory::getSession();
		return $session->get("show_pay_without_reg");
	}
    
    public function setPayWithoutReg(){
        $session = Factory::getSession();
        return $session->set("show_pay_without_reg", 1);
    }
    
    public function getRequestReturnUrl(){
        if ($return = Factory::getApplication()->input->getBase64('return', '')){
            $return = base64_decode($return);
            if (!\Joomla\Component\Jshopping\Site\Lib\JSUri::isInternal($return)){
                $return = '';
            }
        }
        return $return;
    }

    public function setReturnUrl($url){
        $this->return_url = $url;
    }

    public function getReturnUrl(){
        return $this->return_url;
    }
    
    public function getUrlBackToLogin(){
        $jshopConfig = JSFactory::getConfig();
        $return = $this->getReturnUrl();
        $back = Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=login&return='.base64_encode($return), 1, 1, $jshopConfig->use_ssl);
        return $back;
    }
    
}