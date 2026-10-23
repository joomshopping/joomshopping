<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Language\Text;
/**
* @version      5.10.0 18.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class IeController extends CMSObject{

    function execute( $task ){
        if ($task == '' || !method_exists($this, $task)){
            throw new \Exception(Text::_('JSHOP_PAGE_NOT_FOUND'), 404);
        }
        $this->$task();
    }
    
    function save(){
    }
    
    function loadLanguageFile(){
        $adminlang = Factory::getLanguage();
        $alias = $this->get('alias'); 
        if(file_exists(dirname(__FILE__).'/'.$alias.'/lang/'.$adminlang->getTag().'.php')) {
            require_once (dirname(__FILE__).'/'.$alias.'/lang/'.$adminlang->getTag().'.php');
        } else {
            require_once (dirname(__FILE__).'/'.$alias.'/lang/en-GB.php');
        }
    }
    
}