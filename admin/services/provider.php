<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Jshopping\Administrator\Extension\JshoppingComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

include_once JPATH_SITE.'/components/com_jshopping/bootstrap.php';

return new class implements ServiceProviderInterface{

	public function register(Container $container){

		$container->registerServiceProvider(new MVCFactory('\\Joomla\\Component\\Jshopping'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Joomla\\Component\\Jshopping'));
		$container->registerServiceProvider(new RouterFactory('\\Joomla\\Component\\Jshopping'));

		$container->set(
			ComponentInterface::class,
			function (Container $container){
				$component = new JshoppingComponent($container->get(ComponentDispatcherFactoryInterface::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));
				$component->setRegistry($container->get(Registry::class));
				return $component;
			}
		);
	}
};