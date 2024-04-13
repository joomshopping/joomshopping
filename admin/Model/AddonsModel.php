<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;

use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

defined('_JEXEC') or die();

class AddonsModel extends BaseadminModel{

    function getList($details = 0){
        $db = Factory::getDBO(); 
        $query = "SELECT * FROM `#__jshopping_addons`";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        if ($details){
            $versions = $this->getListWebLastVersions();

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
                if (isset($versions[$v->alias])) {
                    $rows[$k]->last_version = $versions[$v->alias]->last_file->version ?? '';
                    $rows[$k]->catalog_url = $versions[$v->alias]->url ?? '';
                    if (version_compare($rows[$k]->last_version, $v->version) > 0) {
                        $rows[$k]->avialable_version_update = 1;
                    }
                }
            }
        }
        return $rows;
    }
    
    public function save(array $post){
        $row = JSFactory::getTable('addon');
        $params = $post['params'];
        if (!is_array($params)){
            $params = array();
        }
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveAddons', array(&$params, &$post, &$row));
        $row->bind($post);
        $row->setParams($params);
        $row->store();
		$dispatcher->triggerEvent('onAfterSaveAddons', array(&$params, &$post, &$row));
        return $row;
    }
    
    public function delete($id){
        $text = '';
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveAddons', array(&$id));
        $row = JSFactory::getTable('addon');
        $row->load($id);
        if ($row->uninstall){
            include(JPATH_ROOT.$row->uninstall);
        }
        $row->delete();
        $dispatcher->triggerEvent('onAfterRemoveAddons', array(&$id, &$text));
        if ($text){
            Factory::getApplication()->enqueueMessage($text, 'message');
        }
    }

    public function getListWebCategory() {
        $api = JSFactory::getModel('Addonswebapi');
        $cats = $api->categorys();
        return $cats;
    }

    public function getListWebTypes() {
        return [
            1 => Text::_('JSHOP_PAID_DOWNLOAD'),
            2 => Text::_('JSHOP_FREE'),
        ];
    }

    public function getListWebCount($filter = []) {
        $api = JSFactory::getModel('Addonswebapi');
        $rows = $api->products();
        if (isset($filter['type'])) {
            $filter['free'] = ($filter['type'] == 1) ? 0 : 1;
        }
        foreach($rows as $k => $v) {
            if (isset($filter['category_id']) && $v->category_id != $filter['category_id']) {
                unset($rows[$k]);
                continue;
            }
            if (isset($filter['free']) && $v->free != $filter['free']) {
                unset($rows[$k]);
                continue;
            }
            if (isset($filter['text_search'])) {
                if (!substr_count(strtolower($v->name." ".$v->descr), strtolower($filter['text_search']))) {
                    unset($rows[$k]);
                    continue;
                }
            }
        }
        return count($rows);
    }

    public function getListWeb($filter = [], $order = '', $orderDir = 'asc', $limitstart = 0, $limit = 0) {        
        $api = JSFactory::getModel('Addonswebapi');
        $rows = $api->products();
        if (isset($filter['type'])) {
            $filter['free'] = ($filter['type'] == 1) ? 0 : 1;
        }
        if ($order != '') {
            usort($rows, function($a, $b) use ($order, $orderDir) {
                if ($orderDir == 'asc') {
                    return $a->$order <=> $b->$order;
                } else {
                    return $b->$order <=> $a->$order;
                }
            });
        }
        foreach($rows as $k => $v) {
            if (isset($filter['category_id']) && $v->category_id != $filter['category_id']) {
                unset($rows[$k]);
                continue;
            }
            if (isset($filter['free']) && $v->free != $filter['free']) {
                unset($rows[$k]);
                continue;
            }
            if (isset($filter['text_search'])) {
                if (!substr_count(strtolower($v->name." ".$v->descr), strtolower($filter['text_search']))) {
                    unset($rows[$k]);
                    continue;
                }
            }
        }
        $rows = array_slice($rows, $limitstart, $limit);
        $config = $api->config();
        $back_url = 'index.php?option=com_jshopping&controller=addons&task=listweb';
        foreach($rows as $k => $v) {
            if (isset($v->image)) {
                $rows[$k]->image_url = $config->url . $config->product_image_folder . '/' . $v->image;                
            }
            if (isset($v->last_file->download)) {
                $rows[$k]->download_url = $config->url . $config->download_folder . '/' . $v->last_file->download;                
            }
            if (isset($v->last_file->download) && $v->type_install == 1) {
                $instalurl = 'index.php?option=com_jshopping&controller=update&task=update&installtype=url&install_url=sm2:'.$v->last_file->download.'&back='.urlencode($back_url);
                $rows[$k]->install_url = $instalurl;
            }
            $rows[$k]->url = $config->url . $config->redirect_url . $v->id;
        }
        return $rows;
    }

    public function getListWebLastVersions() {
        $api = JSFactory::getModel('Addonswebapi');
        $config = $api->config();
        $rows = $api->products();
        $list = [];
        foreach($rows as $v) {
            if ($v->addon_alias != '' && isset($v->last_file->version)) {
                $v->url = $config->url . $config->redirect_url . $v->id;
                $list[$v->addon_alias] = $v;
            }
        }
        return $list;
    }

    public function listWebRefresh() {
        $api = JSFactory::getModel('Addonswebapi');
        $api->clearCache();
    }
}