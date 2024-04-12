<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\Component\Jshopping\Administrator\Service\HTML\Jshopping;
use Psr\Container\ContainerInterface;

class JshoppingComponent extends MVCComponent implements BootableExtensionInterface, RouterServiceInterface{
	use RouterServiceTrait;
	use HTMLRegistryAwareTrait;

	public function boot(ContainerInterface $container){
		$this->getRegistry()->register('jshopping', new Jshopping);
	}
}