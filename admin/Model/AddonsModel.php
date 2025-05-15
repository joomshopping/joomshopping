<?php
/**
* @version      5.7.0 01.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;

use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;

defined('_JEXEC') or die();

class AddonsModel extends BaseadminModel{

    protected $nameTable = 'addon';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []) {
		return $this->getList($params['details'] ?? 0, $filters, $params['domain'] ?? '', $params['back'] ?? '');
	}

    public function getList($details = 0, $filter = [], $domain = '', $back = ''){
        $db = Factory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        $where = '';
        if (isset($filter['text_search'])) {
            $word = addcslashes($db->escape($filter['text_search']), "_%");
            $where .= " AND (`name` LIKE ".$db->q('%'.$word.'%').")";
        }
        $query = "SELECT * FROM `#__jshopping_addons` WHERE 1 ".$where;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        if ($details){
            if ($jshopConfig->disable_admin['addons_catalog'] == 0) {
                $catalog = JSFactory::getModel('addonscatalog');
                $list_web = $catalog->getListByAlias();
                $updateUrls = $catalog->getListInstallUrl($domain, $back);
            }

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
                if (isset($list_web[$v->alias]) && isset($list_web[$v->alias]->last_file->version)) {
                    $rows[$k]->last_version = $list_web[$v->alias]->last_file->version ?? '';
                    $rows[$k]->catalog_url = $list_web[$v->alias]->url ?? '';
                    if (version_compare($rows[$k]->last_version, $v->version) > 0) {
                        $rows[$k]->avialable_version_update = 1;
                    }
                    $rows[$k]->web_addon = $list_web[$v->alias];
                }
                if (isset($updateUrls[$v->alias])) {
                    $rows[$k]->url_update = $updateUrls[$v->alias];
                }
            }
        }
        return $rows;
    }
    
    public function save(array $post){
        $row = JSFactory::getTable('addon');
        $params = $post['params'] ?? [];
        $app = Factory::getApplication();
        $app->triggerEvent('onBeforeSaveAddons', array(&$params, &$post, &$row));
        $row->bind($post);
        $row->setParams($params);
        $row->store();
		$app->triggerEvent('onAfterSaveAddons', array(&$params, &$post, &$row));
        return $row;
    }

    public function saveconfig(array $post){
        $row = JSFactory::getTable('addon');
        $config = $post['config'] ?? [];
        if (!$config['folder_overrides_view']) {
            unset($config['folder_overrides_view']);
        }
        if (!$config['folder_overrides_js']) {
            unset($config['folder_overrides_js']);
        }
        if (!$config['folder_overrides_css']) {
            unset($config['folder_overrides_css']);
        }
        $app = Factory::getApplication();
        $app->triggerEvent('onBeforeSaveConfigAddons', array(&$config, &$post, &$row));
        $row->bind($post);
        $row->setConfig($config);
        $row->store();
		$app->triggerEvent('onBeforeSaveConfigAddons', array(&$config, &$post, &$row));
        return $row;
    }

    public function deleteList(array $cid, $msg = 1){
        $res = [];
		foreach($cid as $id){
            $this->delete($id, $msg);
            $res[$id] = true;
		}
        return $res;
    }
    
    public function delete($id, $msg = 1){
        $text = '';
        $app = Factory::getApplication();
        $app->triggerEvent('onBeforeRemoveAddons', array(&$id));
        $adModel = JSFactory::getModel('Addondependencies');
        $row = JSFactory::getTable('addon');
        $row->load($id);
        $used = $adModel->getList(['alias' => $row->alias]);
        if (count($used)) {
            if ($msg){
                JSError::raiseWarning("", $row->name." - ".Text::_('JSHOP_ADDON_NO_DELETED'));
            }
            return 0;
        }
        if ($row->uninstall){
            include(JPATH_ROOT.$row->uninstall);
        }
        $alias = $row->alias;
        $row->delete();
        $adModel->deleteByParent($alias);
        $app->triggerEvent('onAfterRemoveAddons', array(&$id, &$text));
        if ($msg && $text){
            $app->enqueueMessage($text, 'message');
        }
        return 1;
    }

    public function publish(array $cid, $flag){
        foreach($cid as $id){
            $table = $this->getDefaultTable();
            $table->load($id);
            if ($flag) {
                $table->published();
            } else {
                $table->unpublished();
            }
		}
    }

}