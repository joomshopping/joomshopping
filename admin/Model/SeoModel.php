<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class SeoModel extends BaseadminModel{ 

    public function getList(){
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();         
        $query = "SELECT id, alias, `".$lang->get('title')."` as title, `".$lang->get('keyword')."` as keyword, `".$lang->get('description')."` as description FROM `#__jshopping_config_seo` ORDER BY ordering";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    public function save(array $post){
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveConfigSeo', array(&$post));        
        $seo = \JSFactory::getTable("seo");
        if (isset($post['f-id'])){
            $post['id'] = $post['f-id'];
            unset($post['f-id']);
        }
        $seo->load((int)$post['id']);
        $seo->bind($post);
        if (!$post['id']){
            $seo->ordering = null;
            $seo->ordering = $seo->getNextOrder();            
        }
        $seo->store($post);
        $dispatcher->triggerEvent('onAfterSaveConfigSeo', array(&$seo));
        return $seo;
    }
}