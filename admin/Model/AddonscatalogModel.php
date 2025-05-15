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
        $rows = $api->products();
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
        $rows = $api->products();
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
        foreach($rows as $k => $v) {
            if (isset($v->image)) {
                $rows[$k]->image_url = $config->url . $config->product_image_folder . '/' . $v->image;
            }
            if (isset($v->last_file) && (isset($v->last_file->download) || in_array($v->id, $myPaidProducts))){
                $rows[$k]->download_url = 'index.php?option=com_jshopping&controller=addonscatalog&task=downloadaddon&wid='.$v->id;
                $rows[$k]->install_url = 'index.php?option=com_jshopping&controller=addonscatalog&task=installaddon&wid='.$v->id.'&back='.urlencode($back_url);
            }
            $rows[$k]->url = $config->url . $config->redirect_url . $v->id;
        }
        return $rows;
    }

    public function getListByAlias() {
        $api = JSFactory::getModel('Addonswebapi');
        $config = $api->config();
        $rows = $api->products();
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
        $rows = $api->products();
        foreach($rows as $v) {
            if ($v->id == $id) {
                $data = $v;
                return $data;
            }
        }
        return null;
    }

    public function getDownloadUrlAddon($addon_data, $domain) {
        if ($addon_data->free && isset($addon_data->last_file->download)) {
            return $this->getDownloadUrlFreeAddon($addon_data->last_file->download);
        }
        if ($addon_data->free == 0) {
            return $this->getDownloadUrlPaidAddon($addon_data->id, $domain);
        }
        return '';
    }

    public function getDownloadUrlFreeAddon($file) {
        $api = JSFactory::getModel('Addonswebapi');
        $config = $api->config();
        return $config->url . $config->download_folder . '/' . $file;
    }

    public function getDownloadUrlPaidAddon($id, $domain) {
        $jshopConfig = JSFactory::getConfig();
		if (!$jshopConfig->addonshop_api_key) {
            return '';
        }
        $api = JSFactory::getModel('Addonswebapi');
        $api->useCache(0);
        $res = $api->getUrlPaidAddon($id, $domain, $jshopConfig->addonshop_api_key);
        if (isset($res->error)) {
            Helper::saveToLog('addons.log', 'getDownloadUrlPaidAddon ('.$id.'): '.$res->error);
        }
        return $res->url ?? '';
    }

    public function getInstallUrlAddon($addon_data, $domain, $back_url = ''){
        if ($addon_data->free && isset($addon_data->last_file->download)) {
            return $this->getInstallUrlFreeAddon($addon_data->last_file->download, $back_url);
        }
        if ($addon_data->free == 0) {
            return $this->getInstallUrlPaidAddon($addon_data->id, $domain, $back_url);
        }
        return '';
    }

    public function getInstallUrlFreeAddon($file, $back_url = ''){
        $instalurl = 'index.php?option=com_jshopping&controller=update&task=update&installtype=url&install_url=sm2:'.$file.'&back='.urlencode($back_url);
        return $instalurl;
    }

    public function getInstallUrlPaidAddon($id, $domain, $back_url = ''){
        $url = $this->getDownloadUrlPaidAddon($id, $domain);        
        if ($url) {
            $ft = Session::getFormToken();
            $instalurl = 'index.php?option=com_jshopping&controller=update&task=update&installtype=url&install_url='.urlencode($url).'&back='.urlencode($back_url)."&".$ft."=1&ct=1";
            return $instalurl;
        }
        return '';
    }

    public function getListInstallUrl($domain, $back_url) {
        $api = JSFactory::getModel('Addonswebapi');
        $rows = $api->products();
        $myPaidProducts = $this->getListMyPaidAddonsId($domain);
        $list = [];
        foreach($rows as $v) {
            if ($v->addon_alias != '') {
                if (isset($v->last_file) && (isset($v->last_file->download) || in_array($v->id, $myPaidProducts))){                    
                    $list[$v->addon_alias] = 'index.php?option=com_jshopping&controller=addonscatalog&task=installaddon&wid='.$v->id.'&back='.urlencode($back_url);
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
        $rows = $api->products();
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