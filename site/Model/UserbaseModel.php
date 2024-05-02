<?php
/**
* @version      5.3.2 06.02.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\CMS\Component\ComponentHelper;
defined('_JEXEC') or die();

abstract class UserbaseModel  extends BaseModel{
	
	public $userparams = null;
	
	public function getUserParams(){
        return $this->userparams;
    }
	
	protected function loadUserParams(){	
		$this->userparams = ComponentHelper::getParams('com_users');
	}
	
}