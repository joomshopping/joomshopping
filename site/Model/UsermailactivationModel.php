<?php
/**
* @version      5.0.6 11.06.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class UserMailActivationModel  extends MailModel{
    
    public function getSubjectMail(){
        $data = $this->getData();
        $subject = \JText::_('COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_SUBJECT');
		$search = ['{NAME}', '{SITENAME}'];
        $replace = [$data['name'], $data['sitename']];
        $subject = str_replace($search, $replace, $subject);
        return $subject;
    }
    
    public function getMessageMail(){
        $data = $this->getData();
        $emailBody = \JText::_('COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY');
		$search = ['{NAME}', '{SITEURL}', '{USERNAME}'];
        $replace = [$data['name'], $data['siteurl'], $data['username']];
        $emailBody = str_replace($search, $replace, $emailBody);
		
        $view = $this->getView('user');
        $view->setLayout("activationmail");
        $view->set('data', $data);
        $view->set('emailBody', $emailBody);
        \JFactory::getApplication()->triggerEvent('onBeforeActivationmailView', array(&$view));
        return $view->loadTemplate();
    }
    
    public function send(){
        $dispatcher = \JFactory::getApplication();        
        $emailSubject = $this->getSubjectMail();
        $emailBody = $this->getMessageMail();
        $data = $this->getData();
        $mode = \JSFactory::getConfig()->activation_mail_html_format;
		$dispatcher->triggerEvent('onBeforeActivationSend', array(&$data, &$emailSubject, &$emailBody, &$mode));
        try{
            $return = \JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody, $mode);
        } catch (\Exception $e) {
            $return = false;
            \JSHelper::saveToLog('error.log', 'Usermailactivation mail send error: '.$e->getMessage());			
        }
		if ($return !== true){
			$this->setError(\JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
			return false;
		}
		return true;
    }
    
    public function getSubjectMailAdmin(){
        $data = $this->getData();
		$subject = \JText::_('COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_SUBJECT');
		$search = ['{NAME}', '{SITENAME}'];
        $replace = [$data['name'], $data['sitename']];
        $subject = str_replace($search, $replace, $subject);
        return $subject;
    }
    
    public function getMessageMailAdmin(){
        $data = $this->getData();
		$activateurl = $data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'];
		$emailBody = \JText::_('COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_BODY');
		$search = ['{SITENAME}', '{NAME}', '{EMAIL}', '{USERNAME}', '{ACTIVATE}'];
        $replace = [$data['sitename'], $data['name'], $data['email'], $data['username'], $activateurl];
        $emailBody = str_replace($search, $replace, $emailBody);

        $view = $this->getView('user');
        $view->setLayout("activationmailadmin");
        $view->set('data', $data);
        $view->set('emailBody', $emailBody);
        \JFactory::getApplication()->triggerEvent('onBeforeActivationmailAdminView', array(&$view));
        return $view->loadTemplate();
    }
    
    public function getListAdminUserSendEmail(){
        $db = \JFactory::getDBO();
        $query = 'SELECT name, email, sendEmail FROM #__users WHERE sendEmail=1';
        $db->setQuery( $query );
        return $db->loadObjectList();
    }
    
    public function sendToAdmin(){
        $dispatcher = \JFactory::getApplication();
        $data = $this->getData();
        $emailSubject = $this->getSubjectMailAdmin();
        $emailBody = $this->getMessageMailAdmin();
        $rows = $this->getListAdminUserSendEmail();        
        $mode = \JSFactory::getConfig()->activation_mail_admin_html_format;
        foreach($rows as $row){
			$dispatcher->triggerEvent('onBeforeActivationSendMailAdmin', array(&$data, &$emailSubject, &$emailBody, &$row, &$mode));
            try { 
                $return = \JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBody, $mode);
            } catch (\Exception $e) {
                $return = false;
                \JSHelper::saveToLog('error.log', 'Usermailactivation mail send error: '.$e->getMessage());			
            }
			if ($return !== true){
				$this->setError(\JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
				return false;
			}
        }
		return true;
    }
    
}