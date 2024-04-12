<?php
/**
* @version      5.1.3 19.01.2023
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
		$app = \JFactory::getApplication();
		$app->triggerEvent('onBeforeBuildRoute', array(&$query, &$segments));
		$categoryitemidlist = $shim->getListCategory();
		$menu = \Joomla\CMS\Menu\SiteMenu::getInstance('site');

		if (isset($query['view']) && !isset($query['controller'])){
			$query['controller'] = $query['view'];
		}
        unset($query['view']);
		if (!isset($query['controller'])) {
			$query['controller'] = null;
		}
		if (!isset($query['task'])) {
			$query['task'] = null;
		}

		if (isset($query['controller'])){
			$controller = $query['controller'];
		}else{
			$controller = "";
		}
		if (in_array($controller, array('category', 'manufacturer', 'vendor', 'product'))){
			unset($query['layout']);
		}
        $catalias = \JSFactory::getAliasCategory($lang);

		if (isset($query['Itemid']) && $query['Itemid']) {
            $clearQuery = 1;
			$app->triggerEvent('onBeforeBuildRouteClearQuery', array(&$query, &$segments, &$clearQuery));
            $menuItem = $menu->getItem($query['Itemid']);
			$miquery = $menuItem->query ?? [];
			if (isset($menuItem->type) && $menuItem->type == 'url') {
				$clearQuery = 0;
			}
            if ($query['controller']=='category' && $query['task']!='' && isset($miquery['category_id']) && !$miquery['category_id']){
                $clearQuery = 0;
            }
            if ($query['controller']=='category' && $query['task']=="view" && $query['category_id'] && !isset($catalias[$query['category_id']]) && !isset($miquery['category_id'])) {
                $clearQuery = 0;
            }
            if ($query['controller']=='manufacturer' && $query['task']!='' && isset($miquery['manufacturer_id']) && !$miquery['manufacturer_id']){
                $clearQuery = 0;
            }
			if ($query['controller']=='manufacturer' && $query['task']!='' && isset($query['manufacturer_id']) && !isset($miquery['manufacturer_id'])){
                $clearQuery = 0;
            }
            if (isset($miquery['view']) && $query['controller']!=$miquery['view']){
                $clearQuery = 0;
            }
            if (isset($miquery['task']) && $query['task']!=$miquery['task']){
                $clearQuery = 0;
            }
            if ($clearQuery) {
                foreach($miquery as $k=>$v){
                    if ($k=='option') continue;
                    if (isset($query[$k]) && $query[$k] == $v) {
                        unset($query[$k]);
                    }
                    if ($k=='view' && $query['controller']==$v){
                        unset($query['controller']);
                    }
                }
				if (isset($miquery['view']) && $miquery['view']=="product" && isset($miquery['category_id']) && isset($query['category_id'])) {
					unset($query['category_id']);
				}
            }
		}
        if (!isset($query['task'])) {
			$query['task'] = null;
		}

		if ($controller=="category" && $query['task']=="view" && $query['category_id']) {
            if (isset($catalias[$query['category_id']])){
                $segments[] = $catalias[$query['category_id']];
                unset($query['controller']);
                unset($query['task']);
                unset($query['category_id']);
            }
		}

		if ($controller=="product" && $query['task']=="view" && isset($query['category_id']) && isset($query['product_id'])){
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

		if ($controller=="vendor"){
			if (isset($query['vendor_id'])) {
				$segments[] = $query['vendor_id'];
				unset($query['vendor_id']);
			}
		}

		$app->triggerEvent('onAfterBuildRoute', array(&$query, &$segments));
		return $segments;
	}

	function parse(&$segments){
		$vars = array();
		\JSHelper::initLoadJoomshoppingLanguageFile();
		$reservedFirstAlias = \JSFactory::getReservedFirstAlias();
		$menu = \JFactory::getApplication()->getMenu();
		$menuItem = $menu->getActive();
		if (!isset($menuItem) || !isset($menuItem->query)) {
			$miquery = [];
		} else {
			$miquery = $menuItem->query;
		}
		\JPluginHelper::importPlugin('jshoppingrouter');
		$app = \JFactory::getApplication();
		$app->triggerEvent('onBeforeParseRoute', array(&$vars, &$segments));
		foreach($segments as $k=>$v){
			$segments[$k] = \JSHelper::getSeoSegment($v);
		}
		if (empty($segments) && count($vars)) {
			return $vars;
		}
		if (!isset($segments[1])){
			$segments[1] = '';
		}
		if (!isset($miquery['controller']) && isset($miquery['view'])){
			$miquery['controller'] = $miquery['view'];
		}
        $miquery['task'] = isset($miquery['task']) ? $miquery['task'] : "";

		if (isset($miquery['controller'])){
			if ($miquery['controller']=="cart"){
				$vars['controller'] = "cart";
				$vars['task'] = $segments[0];
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($miquery['controller']=="wishlist"){
				$vars['controller'] = "wishlist";
				$vars['task'] = $segments[0];
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($miquery['controller']=="search"){
				$vars['controller'] = "search";
				$vars['task'] = $segments[0];
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($miquery['controller']=="user" && $miquery['task']==""){
				$vars['controller'] = "user";
				$vars['task'] = $segments[0];
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($miquery['controller']=="checkout"){
				$vars['controller'] = "checkout";
				$vars['task'] = $segments[0];
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($miquery['controller']=="vendor" && $miquery['task']==""){
				$vars['controller'] = "vendor";
				$vars['task'] = $segments[0];
				if (isset($segments[1]) && $segments[1]) {
					$vars['vendor_id'] = $segments[1];
				}
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($miquery['controller']=="content" && $miquery['task']=="view"){
				$vars['controller'] = "content";
                if (count($segments)==2){
                    $vars['task'] = $segments[0];
                    $vars['page'] = $segments[1];
                }else{
                    $vars['page'] = $segments[0];
                }
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}
			if ($miquery['controller']=="category" && isset($miquery['category_id']) && $miquery['category_id'] && $segments[1]=="") {
				$prodalias = \JSFactory::getAliasProduct();
				$product_id = array_search($segments[0], $prodalias, true);
				if (!$product_id){
					throw new \Exception(\JText::_('JSHOP_PAGE_NOT_FOUND'), 404);
				}

				$vars['controller'] = "product";
				$vars['task'] = "view";
				$vars['category_id'] = $miquery['category_id'];
				$vars['product_id'] = $product_id;
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
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
				$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
				$segments = [];
				return $vars;
			}

			if ($category_id && $segments[1]!=""){
				$prodalias = \JSFactory::getAliasProduct();
				$product_id = array_search($segments[1], $prodalias, true);
				if (!$product_id){
					throw new \Exception(\JText::_('JSHOP_PAGE_NOT_FOUND'), 404);
				}
				if ($category_id && $product_id){
					$vars['controller'] = "product";
					$vars['task'] = "view";
					$vars['category_id'] = $category_id;
					$vars['product_id'] = $product_id;
					$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
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
					$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
					$segments = [];
					return $vars;
				}
			}
			
			$app->triggerEvent('onAfterParseRouteNodef', array(&$vars, &$segments, &$miquery));
			if (count($vars) > 0) {
				return $vars;
			}

			throw new \Exception(\JText::_('JSHOP_PAGE_NOT_FOUND'), 404);

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

			if ($vars['controller']=="vendor" && isset($segments[2])){
				$vars['vendor_id'] = $segments[2];
			}
		}

	$app->triggerEvent('onAfterParseRoute', array(&$vars, &$segments));
	$segments = [];
	return $vars;
	}
}