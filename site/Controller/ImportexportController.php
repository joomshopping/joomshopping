<?php
/**
* @version      5.5.6 15.02.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
defined('_JEXEC') or die();
include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/iecontroller.php");

class ImportexportController extends BaseController{
    
    function display($cachable = false, $urlparams = false){
		throw new \Exception(Text::_('JSHOP_PAGE_NOT_FOUND'), 404);
    }

    function start(){
		$this->input->set('noredirect', 1);
		$key = $this->input->getVar("key");
        $alias = $this->input->getVar("alias");
        $id = $this->input->get->getInt("id");
        $print_alias = $this->input->getVar("print_alias", 0);
		$model = JSFactory::getModel('importExportStart', 'Site');
		if ($model->checkKey($key)){
			$model->executeList(null, $print_alias, $alias, $id);
		}
        die();
    }
}