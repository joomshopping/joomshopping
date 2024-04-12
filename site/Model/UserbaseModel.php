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

abstract class UserbaseModel  extends BaseModel{
	
	protected $userparams = null;
	
	public function getUserParams(){
        return $this->userparams;
    }
	
	protected function loadUserParams(){	
		$this->userparams = \JComponentHelper::getParams('com_users');
	}
	
}