<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
use Joomla\CMS\Factory;
defined('_JEXEC') or die();

class Error{
	
	public static function raiseWarning($code, $msg){
        $app = Factory::getApplication();
		$app->enqueueMessage($msg, 'warning');
        self::setLastErrorCode($code);
	}
    
    public static function raiseNotice($code, $msg){
        $app = Factory::getApplication();
		$app->enqueueMessage($msg, 'notice');
        self::setLastErrorCode($code);
	}
    
    public static function raiseError($code, $msg){
        $app = Factory::getApplication();
		$app->enqueueMessage($msg, 'error');
        self::setLastErrorCode($code);
	}
	
	public static function raiseMessage($code, $msg){
        $app = Factory::getApplication();
		$app->enqueueMessage($msg, 'message');
        self::setLastErrorCode($code);
	}

    public static function isError($object){
		return $object instanceof Exception;
	}
    
    public static function getErrors(){
        return Factory::getApplication()->getMessageQueue();
    }
    
    public static function setLastErrorCode($code) {
        Factory::getSession()->set('js_error_message_code', $code);
    }
    
    public static function getLastErrorCode() {
        return Factory::getSession()->get('js_error_message_code');
    }
}