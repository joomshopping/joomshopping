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

abstract class BaseModel{

	private $error;
	
	public function setError($error){
        $this->error = $error;
    }
    
    public function getError(){
        return $this->error;
    }
    
    public function getView($name){
		$jshopConfig = \JSFactory::getConfig();		
		include_once(JPATH_JOOMSHOPPING."/View/".ucfirst($name)."/HtmlView.php");
		$config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$name);
		$viewClass = '\\Joomla\\Component\\Jshopping\\Site\\View\\'.ucfirst($name).'\\HtmlView';
        $view = new $viewClass($config);
        return $view;
    }
	
}