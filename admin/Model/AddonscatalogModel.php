<?php
/**
* @version      5.10.0 17.10.2026
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

defined('_JEXEC') or die();

class AddonscatalogModel extends BaseadminModel{

    public function getListCategory() {
        $api = JSFactory::getModel('Addonswebapi');
        $cats = $api->categorys();
        return $cats;
    }

    public function getListTypes() {
        $res = [
            1 => Text::_('JSHOP_PAID_DOWNLOAD'),
            2 => Text::_('JSHOP_FREE'),
        ];
        if (JSFactory::getConfig()->addonshop_api_key) {
            $res[3] = Text::_('JSHOP_MY_PAID_ADDONS');
        }
        return $res;
    }

    public function getListCount($filter = [], $domain = '') {
        $jshopConfig = JSFactory::getConfig();
        $api = JSFactory::getModel('Addonswebapi');
        $rows = $api->products(JSFactory::getConfig()->typeshop);
        $myPaidProducts = $this->getListMyPaidAddonsId($domain);
        
        if (isset($filter['type']) && $filter['type'] != 3) {
            $filter['free'] = ($filter['type'] == 1) ? 0 : 1;
        }
        foreach($rows as $k => $v) {
            if (isset($filter['type']) && $filter['type'] == 3 && !in_array($v->id, $myPaidProducts)) {
                unset($rows[$k]);
                continue;
            }
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

    public function getList($filter = [], $order = '', $orderDir = 'asc', $limitstart = 0, $limit = 0, $domain = '', $back_url = '') {
        $api = JSFactory::getModel('Addonswebapi');
        $rows = $api->products(JSFactory::getConfig()->typeshop);
        $myPaidProducts = $this->getListMyPaidAddonsId($domain);

        if (isset($filter['type']) && $filter['type'] != 3) {
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
            if (isset($filter['type']) && $filter['type'] == 3 && !in_array($v->id, $myPaidProducts)) {
                unset($rows[$k]);
                continue;
            }
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
        $jshopConfig = JSFactory::getConfig();
        foreach($rows as $k => $v) {
            if (isset($v->image)) {
                $rows[$k]->image_url = $config->url . $config->product_image_folder . '/' . $v->image;
            }
            if (isset($v->last_file) && (isset($v->last_file->download) || in_array($v->id, $myPaidProducts))){
                if (!$jshopConfig->downloadaddon_disable) {
                    $rows[$k]->download_url = 'index.php?option=com_jshopping&controller=addonscatalog&task=downloadaddon&wid='.$v->id.'&'.Session::getFormToken().'=1';
                }
                if (!$jshopConfig->installaddon_disable) {
                    $rows[$k]->install_url = 'index.php?option=com_jshopping&controller=addonscatalog&task=installaddon&wid='.$v->id.'&back='.urlencode($back_url).'&'.Session::getFormToken().'=1';
                }
            }
            $rows[$k]->url = $config->url . $config->redirect_url . $v->id;
        }
        return $rows;
    }

    public function getListByAlias() {
        $api = JSFactory::getModel('Addonswebapi');
        $config = $api->config();
        $rows = $api->products(JSFactory::getConfig()->typeshop);
        $list = [];
        foreach($rows as $v) {
            if ($v->addon_alias != '') {
                $v->url = $config->url . $config->redirect_url . $v->id;
                $list[$v->addon_alias] = $v;
            }
        }
        return $list;
    }

    public function getAddonData($id) {
        $api = JSFactory::getModel('Addonswebapi');
        $rows = $api->products(JSFactory::getConfig()->typeshop);
        foreach($rows as $v) {
            if ($v->id == $id) {
                $data = $v;
                return $data;
            }
        }
        return null;
    }

    /**
     * Downloads the addon package from the shop API (free and paid in one request).
     * @return string path to the zip in tmp
     * @throws Exception
     */
    public function downloadAddon($id) {
        $api = JSFactory::getModel('Addonswebapi');
        $jshopConfig = JSFactory::getConfig();
        return $api->downloadAddon($id, Helper::getClearHost(), $jshopConfig->addonshop_api_key, $jshopConfig->typeshop);
    }

    /**
     * Downloads and installs the addon on the server, the package URL is never shown to the user.
     * @return UpdateModel
     * @throws Exception
     */
    public function installAddon($id) {
        $file = $this->downloadAddon($id);
        $installtype = 'file';
        $addon = $this->getAddonData($id);
        $model = JSFactory::getModel('Update');
        $app = Factory::getApplication();
        $result = false;
        try {
            $app->triggerEvent('onBeforeInstallAddon', array(&$file, &$installtype, &$addon, 'catalog'));
            $model->install($file, $installtype, $addon->name ?? '');
            $result = true;
        } finally {
            $app->triggerEvent('onAfterInstallAddon', array(&$file, &$installtype, &$addon, 'catalog', $result));
            @unlink($file);
        }
        if (isset($addon->free) && !$addon->free && ($addon->addon_alias ?? '')) {
            $this->setAddonKeyAfterInstall($addon->addon_alias);
        }
        return $model;
    }
    
    public function setAddonKeyAfterInstall($alias) {
        if (!JSFactory::getConfig()->install_paid_addon_set_key) {
            return 0;
        }
        $row = JSFactory::getTable('addon');
        $row->loadAlias($alias);
        if (!$row->id || !$row->usekey) {
            return 0;
        }
        $key = $this->getAddonKey($alias, Helper::getClearHost());
        if ($key == '' || $row->key == $key) {
            return 0;
        }
        $row->key = $key;
        $row->store();
        $row->load($row->id);
        $row->syncBundledKeys();
        Factory::getApplication()->triggerEvent('onAfterSaveLicenseKeyAddons', array(&$row));
        Helper::saveToLog('addons.log', 'setAddonKeyAfterInstall: '.$alias);
        return 1;
    }

    public function getDownloadFileName($id) {
        $data = $this->getAddonData($id);
        $name = ($data->addon_alias ?? '') ?: 'addon_'.(int)$id;
        if (isset($data->last_file->version)) {
            $name .= '_'.$data->last_file->version;
        }
        return preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $name).'.zip';
    }

    public function getListInstallUrl($domain, $back_url) {
        $api = JSFactory::getModel('Addonswebapi');
        $rows = $api->products(JSFactory::getConfig()->typeshop);
        $list = [];
        if (JSFactory::getConfig()->installaddon_disable) {
            return $list;
        }
        $myPaidProducts = $this->getListMyPaidAddonsId($domain);
        foreach($rows as $v) {
            if ($v->addon_alias != '') {
                if (isset($v->last_file) && (isset($v->last_file->download) || in_array($v->id, $myPaidProducts))){                    
                    $list[$v->addon_alias] = 'index.php?option=com_jshopping&controller=addonscatalog&task=installaddon&wid='.$v->id.'&back='.urlencode($back_url).'&'.Session::getFormToken().'=1';
                }
            }
        }
        return $list;
    }

    public function refresh() {
        $api = JSFactory::getModel('Addonswebapi');
        $api->clearCache();
    }

    public function getListMyPaidAddonsId($domain) {
        $jshopConfig = JSFactory::getConfig();
        $api = JSFactory::getModel('Addonswebapi');
        if ($domain && $jshopConfig->addonshop_api_key) {
            $api->setCacheTime($jshopConfig->addonshop_api_get_my_paid_products_cache_time);
            $res = $api->getMyPaidProducts($domain, $jshopConfig->addonshop_api_key);
            if (isset($res->error)) {
                Helper::saveToLog('addons.log', 'getListMyPaidAddonsId: '.$res->error);
                return [];
            } else {
                return $res ?? [];
            }            
        } else {
            return [];
        }
    }

    public function getListMyPaidAddons($domain) {
        $api = JSFactory::getModel('Addonswebapi');
        $rows = $api->products(JSFactory::getConfig()->typeshop);
        $myPaidProducts = $this->getListMyPaidAddonsId($domain);
        foreach($rows as $k => $v) {
            if (!in_array($v->id, $myPaidProducts)) {
                unset($rows[$k]);
                continue;
            }
        }
        return array_values($rows);
    }

    public function getListMyPaidAddonsAlias($domain) {
        $list = $this->getListMyPaidAddons($domain);
        return array_map(function($item){return $item->addon_alias ?? '';}, $list);
    }

    public function getAddonKey($alias, $domain) {
        $jshopConfig = JSFactory::getConfig();
		if (!$jshopConfig->addonshop_api_key) {
            return '';
        }
        $api = JSFactory::getModel('Addonswebapi');
        $api->useCache(0);
        $res = $api->getAddonKey($alias, $domain, $jshopConfig->addonshop_api_key);
        if (isset($res->error)) {
            Helper::saveToLog('addons.log', 'getAddonKey ('.$alias.'): '.$res->error);
        }
        return $res->key ?? '';
    }

}