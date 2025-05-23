<?php
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper;
use Joomla\CMS\Language\LanguageHelper;

/**
* @version      5.6.2 05.04.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

abstract class JshoppingHelperAssociation{

	public static function getAssociations($id = 0, $view = null, $layout = null){
        $jinput = Factory::getApplication()->input;
		$component = $jinput->getCmd('option');
		if ($component!='com_jshopping'){
            return [];
        }        
		if (!$view){
			$view = $jinput->get('controller');
		}
        if (!$view){
            $view = $jinput->get('view');
        }
        $languages = LanguageHelper::getLanguages();
        
        $app = Factory::getApplication();
        $menu = $app->getMenu();
        $active = $menu->getActive();
        $associations = MenusHelper::getAssociations($active->id);
        
        $urlparams = array('task', 'category_id', 'product_id', 'manufacturer_id', 'page', 'order_id', 'vendor_id', 'label_id');
        $urlparamsData = array('view'=>$view);
        foreach($urlparams as $param){
            $data = $jinput->getCmd($param);
            if ($data){
                $urlparamsData[$param] = $data;
            }
        }
        
        $enable = 0;
        $pagesEnable = self::pagesEnable();
        foreach($pagesEnable as $page){
            if (self::checkPageUrl($page, $urlparamsData)){
                $enable = 1;
                break;
            }
        }
        
        $return = [];
        
        if ($enable){
            foreach($languages as $lang){
                $return[$lang->lang_code] = 'index.php?option=com_jshopping&controller='.$view;
                foreach($urlparamsData as $param=>$data){
                    if ($param=='view'){
                        continue;
                    }
                    $return[$lang->lang_code] .= '&'.$param.'='.$data;
                }
				$return[$lang->lang_code] .= '&lang='.$lang->lang_code;
                if (isset($associations[$lang->lang_code])){
                    $return[$lang->lang_code] .= '&Itemid='.$associations[$lang->lang_code];
                }
				$return[$lang->lang_code] = Helper::SEFLink($return[$lang->lang_code], 1);
            }
        }
        return $return;
    }
    
    private static function pagesEnable(){
        $pages = array();
        $pages[] = array('url'=>array('view'=>'category', 'task'=>'view', 'category_id'=>'\d+'), 'exact'=>1);
        $pages[] = array('url'=>array('view'=>'product', 'task'=>'view', 'category_id'=>'\d+', 'product_id'=>'\d+'), 'exact'=>1);
        $pages[] = array('url'=>array('view'=>'manufacturer', 'task'=>'view', 'manufacturer_id'=>'\d+'), 'exact'=>1);
        $pages[] = array('url'=>array('view'=>'vendor', 'task'=>'info', 'vendor_id'=>'\d+'), 'exact'=>1);
        $pages[] = array('url'=>array('view'=>'vendor', 'task'=>'products', 'vendor_id'=>'\d+'), 'exact'=>1);        
        $pages[] = array('url'=>array('view'=>'cart'));
        $pages[] = array('url'=>array('view'=>'checkout'));
        $pages[] = array('url'=>array('view'=>'content'));
        $pages[] = array('url'=>array('view'=>'user'));
        $pages[] = array('url'=>array('view'=>'wishlist'));
        $pages[] = array('url'=>array('view'=>'search'));
        
        Factory::getApplication()->triggerEvent('onAfterAssociationPagesEnable', array(&$pages));
        return $pages;
    }
    
    private static function checkPageUrl($page, $urlData){
        $check = 1;
        foreach($page['url'] as $k=>$v){
			if (!isset($urlData[$k]) || !preg_match('/'.$v.'/', $urlData[$k])){
                $check = 0;
            }
        }
        if ($check && isset($page['exact']) && $page['exact']){
            if (count($page['url'])!=count($urlData)){
                $check = 0;
            }
        }
        return $check;
    }

}