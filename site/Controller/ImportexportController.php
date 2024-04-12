<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
defined('_JEXEC') or die();
JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/Model');
include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/iecontroller.php");

class ImportExportController extends BaseController{
    
    function display($cachable = false, $urlparams = false){
        \JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
    }

    function start(){
		$_GET['noredirect'] = 1;
		$_POST['noredirect'] = 1;
		$_REQUEST['noredirect'] = 1;
		$key = $this->input->getVar("key");
        $alias = $this->input->getVar("alias");
        $id = $this->input->getInt("id");
		$model = \JSFactory::getModel('importExportStart', 'Site');
		if ($model->checkKey($key)){
			$model->executeList(null, 1, $alias, $id);
		}
        die();
    }
}