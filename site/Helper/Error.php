<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
defined('_JEXEC') or die();

#Fix code reise

class Error{
	
	public static function raiseWarning($code, $msg){
        $app = \JFactory::getApplication();
		$app->enqueueMessage($msg, 'warning');
	}
    
    public static function raiseNotice($code, $msg){
        $app = \JFactory::getApplication();
		$app->enqueueMessage($msg, 'notice');
	}
    
    public static function raiseError($code, $msg){
        $app = \JFactory::getApplication();
		$app->enqueueMessage($msg, 'error');
	}
	
	public static function raiseMessage($code, $msg){
        $app = \JFactory::getApplication();
		$app->enqueueMessage($msg, 'message');
	}

    public static function isError($object){
		return $object instanceof Exception;
	}
    
    public static function getErrors(){
        return \JFactory::getApplication()->getMessageQueue();
    }
}