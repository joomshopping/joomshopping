<?php
/**
* @version      5.6.3 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Dispatcher;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\MVC\Controller\BaseController;

include_once __DIR__.'/../bootstrap.php';
include_once __DIR__.'/../loadparams.php';

class Dispatcher extends ComponentDispatcher {

	public function getController(string $name, string $client = '', array $config = array()): BaseController{
		if ($name=='display'){
            $name = $this->app->input->get('view');
            if ($name=='' || $name == 'featured'){
                $name = 'category';
            }
        }
        Factory::getApplication()->triggerEvent('onAfterGetJsFrontRequestController', array(&$name));

		return parent::getController($name, $client, $config);
	}
}