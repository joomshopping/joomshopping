<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
defined('_JEXEC') or die();

class AddonsModel extends BaseadminModel{

    function getList($details = 0){
        $db = \JFactory::getDBO(); 
        $query = "SELECT * FROM `#__jshopping_addons`";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($details){
            foreach($rows as $k=>$v){
                if (file_exists(JPATH_COMPONENT_SITE."/addons/".$v->alias."/config.tmpl.php")){
                    $rows[$k]->config_file_exist = 1;
                }else{
                    $rows[$k]->config_file_exist = 0;
                }
                if (file_exists(JPATH_COMPONENT_SITE."/addons/".$v->alias."/info.tmpl.php")){
                    $rows[$k]->info_file_exist = 1;
                }else{
                    $rows[$k]->info_file_exist = 0;
                }
                if (file_exists(JPATH_COMPONENT_SITE."/addons/".$v->alias."/version.tmpl.php")){
                    $rows[$k]->version_file_exist = 1;
                }else{
                    $rows[$k]->version_file_exist = 0;
                }
            }
        }
        return $rows;
    }
    
    public function save(array $post){
        $row = \JSFactory::getTable('addon');
        $params = $post['params'];
        if (!is_array($params)){
            $params = array();
        }
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveAddons', array(&$params, &$post, &$row));
        $row->bind($post);
        $row->setParams($params);
        $row->store();
		$dispatcher->triggerEvent('onAfterSaveAddons', array(&$params, &$post, &$row));
        return $row;
    }
    
    public function delete($id){
        $text = '';
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveAddons', array(&$id));
        $row = \JSFactory::getTable('addon');
        $row->load($id);
        if ($row->uninstall){
            include(JPATH_ROOT.$row->uninstall);
        }
        $row->delete();
        $dispatcher->triggerEvent('onAfterRemoveAddons', array(&$id, &$text));
        if ($text){
            \JFactory::getApplication()->enqueueMessage($text, 'message');
        }
    }
}