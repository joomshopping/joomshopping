<?php
/**
* @version      5.2.1 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Router\Route;
defined('_JEXEC') or die();

class UseractivateModel  extends UserbaseModel{
    
    public function __construct(){
		$this->loadUserParams();
        $obj = $this;
        Factory::getApplication()->triggerEvent('onConstructJshopUseractivate', array(&$obj));
    }
	
	public function check($token){
		$params = $this->getUserParams();
		if ($params->get('useractivation') == 0 || $params->get('allowUserRegistration') == 0) {
            $this->setError(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
            return 0;
        }
        if ($token === null || strlen($token) !== 32) {
            $this->setError(Text::_('JINVALID_TOKEN'));
            return 0;
        }
		return 1;
	}
	    
	public function activate($token){
        $config = Factory::getConfig();
        $userParams = $this->getUserParams();
		PluginHelper::importPlugin('user');

        $userId = $this->getUserId($token);
        if (!$userId){
            $this->setError(Text::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'));
            return false;
        }

        $user = Factory::getUser($userId);
		$obj = $this;
        Factory::getApplication()->triggerEvent('onBeforeUserActivate', array(&$obj, &$token, &$user, &$userParams));
		$usermail = JSFactory::getModel('usermailactivation', 'Site');
		$uri = Uri::getInstance();
		$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$data = $user->getProperties();
		$data['fromname'] = $config->get('fromname');
		$data['mailfrom'] = $config->get('mailfrom');
		$data['sitename'] = $config->get('sitename');
		$data['siteurl'] = Uri::base();

        // Admin activation is on and user is verifying their email
        if (($userParams->get('useractivation') == 2) && !$user->getParam('activate', 0)){
            $data['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
            $data['activate'] = $base.Route::_('index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'], false);

			$user->set('activation', $data['activation']);
            $user->setParam('activate', 1);
			
			$usermail->setData($data);
			if (!$usermail->sendToAdmin()){
				$this->setError($usermail->getError());
				return false;
			}
        }
		//Admin activation is on and admin is activating the account
		elseif (($userParams->get('useractivation') == 2) && $user->getParam('activate', 0)){
            $user->set('activation', '');
            $user->set('block', '0');
			$user->setParam('activate', 0);

			$usermail->setData($data);
			if (!$usermail->send()){
				$this->setError($usermail->getError());
				return false;
			}
        }else{
            $user->set('activation', '');
            $user->set('block', '0');
        }
        if (!$user->save()) {
            $this->setError(Text::sprintf('COM_USERS_REGISTRATION_ACTIVATION_SAVE_FAILED', $user->getError()));
            $user = false;
        }
        $obj = $this;
		Factory::getApplication()->triggerEvent('onAfterUserActivate', array(&$obj, &$token, &$user));
        return $user;
    }
	
	public function getMessageUserActivation($user){
		$useractivation = $this->getUserParams()->get('useractivation');
        if ($useractivation == 0){
            $msg = Text::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');            
        }elseif ($useractivation == 1){
            $msg = Text::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS');            
        }elseif ($user->getParam('activate')){
            $msg = Text::_('COM_USERS_REGISTRATION_VERIFY_SUCCESS');            
        }else{
            $msg = Text::_('COM_USERS_REGISTRATION_ADMINACTIVATE_SUCCESS');            
        }
		return $msg;
	}
	
	private function getUserId($token) {
		$db = Factory::getDBO();
		$db->setQuery(
            'SELECT '.$db->qn('id').' FROM '.$db->qn('#__users') .
            ' WHERE '.$db->qn('activation').' = '.$db->q($token) .
            ' AND '.$db->qn('block').' = 1'
        );
        return (int)$db->loadResult();
	}

}