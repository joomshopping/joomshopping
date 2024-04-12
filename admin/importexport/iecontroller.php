<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class IeController extends JObject{
    
    function execute( $task ){
        $this->$task();
    }
    
    function save(){
    }
    
    function loadLanguageFile(){
        $adminlang = \JFactory::getLanguage();
        $alias = $this->get('alias'); 
        if(file_exists(dirname(__FILE__).'/'.$alias.'/lang/'.$adminlang->getTag().'.php')) {
            require_once (dirname(__FILE__).'/'.$alias.'/lang/'.$adminlang->getTag().'.php');
        } else {
            require_once (dirname(__FILE__).'/'.$alias.'/lang/en-GB.php');
        }
    }
    
}