<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Service;
defined('_JEXEC') or die();

include_once __DIR__.'/../bootstrap.php';
use Joomla\Component\Jshopping\Site\Lib\ShopItemMenu;

class Router extends \Joomla\CMS\Component\Router\RouterBase{

	function build(&$query){
		$segments = array();
		\JSHelper::initLoadJoomshoppingLanguageFile();
		$lang = isset($query['lang']) ? $query['lang'] : '';
		$shim = ShopItemMenu::getInstance($lang);
		\JPluginHelper::importPlugin('jshoppingrouter');
		$dispatcher = \JFactory::getApplication();
		$dispatcher->triggerEvent('onBeforeBuildRoute', array(&$query, &$segments));
		$categoryitemidlist = $shim->getListCategory();
		$app = \JFactory::getApplication();
		$menu = $app->getMenu();
        
		if (isset($query['view']) && !isset($query['controller'])){
			$query['controller'] = $query['view'];
		}
        unset($query['view']);

		if (isset($query['controller'])){
			$controller = $query['controller'];
		}else{
			$controller = "";
		}
		if (in_array($controller, array('category', 'manufacturer', 'vendor'))){
			unset($query['layout']);
		}
        
		if (isset($query['Itemid']) && $query['Itemid']){
            $clearQuery = 1;
            $menuItem = $menu->getItem($query['Itemid']);
            if ($query['controller']=='category' && $query['task']!='' && !$menuItem->query['category_id']){
                $clearQuery = 0;
            }
            if ($query['controller']=='manufacturer' && $query['task']!='' && !$menuItem->query['manufacturer_id']){
                $clearQuery = 0;
            }
            if ($query['controller']!=$menuItem->query['view']){
                $clearQuery = 0;
            }
            if ($menuItem->query['task'] && $query['task']!=$menuItem->query['task']){
                $clearQuery = 0;
            }
            if ($clearQuery){                
                foreach($menuItem->query as $k=>$v){
                    if ($k=='option') continue;
                    if ($query[$k]==$v){
                        unset($query[$k]);
                    }
                    if ($k=='view' && $query['controller']==$v){
                        unset($query['controller']);
                    }
                }
            }
		}

		if ($controller=="category" && $query['task']=="view" && $query['category_id']){
            $catalias = \JSFactory::getAliasCategory($lang);
            if (isset($catalias[$query['category_id']])){
                $segments[] = $catalias[$query['category_id']];
                unset($query['controller']);
                unset($query['task']);
                unset($query['category_id']);
            }
		}
        

		if ($controller=="product" && $query['task']=="view" && $query['category_id'] && $query['product_id']){
			$prodalias = \JSFactory::getAliasProduct($lang);
			$catalias = \JSFactory::getAliasCategory($lang);
			if (isset($categoryitemidlist[$query['category_id']]) && isset($prodalias[$query['product_id']])){				
				unset($query['controller']);
				unset($query['category_id']);
				unset($query['task']);
				$segments[] = $prodalias[$query['product_id']];
				unset($query['product_id']);
			}else if (isset($catalias[$query['category_id']]) && isset($prodalias[$query['product_id']])){
				$segments[] = $catalias[$query['category_id']];
				$segments[] = $prodalias[$query['product_id']];
				unset($query['controller']);
				unset($query['task']);
				unset($query['category_id']);
				unset($query['product_id']);
			}
		}

		if ($controller=="manufacturer" && $query['task']=="view" && $query['manufacturer_id']){
            $manalias = \JSFactory::getAliasManufacturer($lang);
            if (isset($manalias[$query['manufacturer_id']])){
                $segments[] = $manalias[$query['manufacturer_id']];
                unset($query['controller']);
                unset($query['task']);
                unset($query['manufacturer_id']);
            }
		}

		if (isset($query['controller'])){
			$segments[] = $query['controller'];
			unset($query['controller']);
		}

		if (isset($query['task']) && $query['task']!='') {
			$segments[] = $query['task'];
			unset($query['task']);
		}

		if ($controller=="category" || $controller=="product"){
			if (isset($query['category_id'])) {
				$segments[] = $query['category_id'];
				unset($query['category_id']);
			}

			if (isset($query['product_id'])) {
				$segments[] = $query['product_id'];
				unset($query['product_id']);
			}
		}

		if ($controller=="manufacturer"){
			if (isset($query['manufacturer_id'])) {
				$segments[] = $query['manufacturer_id'];
				unset($query['manufacturer_id']);
			}
		}

		if ($controller=="content"){
			if (isset($query['page'])) {
				$segments[] = $query['page'];
				unset($query['page']);
			}
		}

		$dispatcher->triggerEvent('onAfterBuildRoute', array(&$query, &$segments));
		return $segments;
	}

	function parse(&$segments){
		$vars = array();
		\JSHelper::initLoadJoomshoppingLanguageFile();
		$reservedFirstAlias = \JSFactory::getReservedFirstAlias();
		$menu = \JFactory::getApplication()->getMenu();
		$menuItem = $menu->getActive();
		if (!isset($menuItem)) $menuItem = new \stdClass();
		\JPluginHelper::importPlugin('jshoppingrouter');
		$dispatcher = \JFactory::getApplication();
		$dispatcher->triggerEvent('onBeforeParseRoute', array(&$vars, &$segments));
		foreach($segments as $k=>$v){
			$segments[$k] = \JSHelper::getSeoSegment($v);
		}
		if (!isset($segments[1])){
			$segments[1] = '';
		}

		if (!isset($menuItem->query['controller']) && isset($menuItem->query['view'])){
			$menuItem->query['controller'] = $menuItem->query['view'];
		}

		if (isset($menuItem->query['controller'])){
			if ($menuItem->query['controller']=="cart"){
				$vars['controller'] = "cart";
				$vars['task'] = $segments[0];
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($menuItem->query['controller']=="wishlist"){
				$vars['controller'] = "wishlist";
				$vars['task'] = $segments[0];
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($menuItem->query['controller']=="search"){
				$vars['controller'] = "search";
				$vars['task'] = $segments[0];
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($menuItem->query['controller']=="user" && $menuItem->query['task']==""){
				$vars['controller'] = "user";
				$vars['task'] = $segments[0];
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($menuItem->query['controller']=="checkout"){
				$vars['controller'] = "checkout";
				$vars['task'] = $segments[0];
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($menuItem->query['controller']=="vendor" && $menuItem->query['task']==""){
				$vars['controller'] = "vendor";
				$vars['task'] = $segments[0];
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($menuItem->query['controller']=="content" && $menuItem->query['task']=="view"){
				$vars['controller'] = "content";
                if (count($segments)==2){
                    $vars['task'] = $segments[0];
                    $vars['page'] = $segments[1];
                }else{
                    $vars['page'] = $segments[0];
                }
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($menuItem->query['controller']=="category" && $menuItem->query['category_id'] && $segments[1]==""){
				$prodalias = \JSFactory::getAliasProduct();
				$product_id = array_search($segments[0], $prodalias, true);
				if (!$product_id){
					\JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
				}

				$vars['controller'] = "product";
				$vars['task'] = "view";
				$vars['category_id'] = $menuItem->query['category_id'];
				$vars['product_id'] = $product_id;
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
		}

		if ($segments[0] && !in_array($segments[0], $reservedFirstAlias)){
			$catalias = \JSFactory::getAliasCategory();
			$category_id = array_search($segments[0], $catalias, true);
			if ($category_id && $segments[1]==""){
				$vars['controller'] = "category";
				$vars['task'] = "view";
				$vars['category_id'] = $category_id;
				$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}

			if ($category_id && $segments[1]!=""){
				$prodalias = \JSFactory::getAliasProduct();
				$product_id = array_search($segments[1], $prodalias, true);
				if (!$product_id){
					\JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
				}
				if ($category_id && $product_id){
					$vars['controller'] = "product";
					$vars['task'] = "view";
					$vars['category_id'] = $category_id;
					$vars['product_id'] = $product_id;
					$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
					$segments = [];
					return $vars;
				}
			}

			if (!$category_id && $segments[1]==""){
				$manalias = \JSFactory::getAliasManufacturer();
				$manufacturer_id = array_search($segments[0], $manalias, true);
				if ($manufacturer_id){
					$vars['controller'] = "manufacturer";
					$vars['task'] = "view";
					$vars['manufacturer_id'] = $manufacturer_id;
					$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
					$segments = [];
					return $vars;
				}
			}

			\JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));

		}else{
			$vars['controller'] = $segments[0];
			$vars['task'] = $segments[1];

			if ($vars['controller']=="category" && $vars['task']=="view"){
				$vars['category_id'] = $segments[2];
			}

			if ($vars['controller']=="product" && $vars['task']=="view"){
				$vars['category_id'] = $segments[2];
				$vars['product_id'] = $segments[3];
			}

			if ($vars['controller']=="product" && $vars['task']=="ajax_attrib_select_and_price"){
				$vars['product_id'] = $segments[2];
			}

			if ($vars['controller']=="manufacturer" && isset($segments[2])){
				$vars['manufacturer_id'] = $segments[2];
			}

			if ($vars['controller']=="content"){
				$vars['page'] = $segments[2];
			}

		}

	$dispatcher->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
	$segments = [];
	return $vars;
	}
}