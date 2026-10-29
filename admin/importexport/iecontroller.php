<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;
/**
* @version      5.10.0 27.10.2026
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

    public function getIeParams($importexport){
        $str = (string)$importexport->get('params');
        $params = json_decode($str, true);
        if (!is_array($params)){
            // old format key=value per line
            $params = Helper::parseParamsToArray($str);
        }
        return $params;
    }

    public function setIeParams($importexport, $params){
        $importexport->set('params', json_encode($params));
    }

    public function getEncodings(){
        return array('UTF-8', 'ISO-8859-1');
    }

    public function getSafeEncoding($encoding){
        return in_array($encoding, $this->getEncodings(), true) ? $encoding : 'ISO-8859-1';
    }
    
}