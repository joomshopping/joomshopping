<?php
/**
* @version      5.10.0 24.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;

use Exception;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;

defined('_JEXEC') or die();

class AddondependenciesModel extends BaseadminModel{

    const ERROR_INSTALL = 1;
    const ERROR_NO_API_KEY = 2;
    const ERROR_NOT_PURCHASED = 3;

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []) {
		return $this->getList($filters);
	}

    public function getList($filter = []){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__jshopping_addons_dependencies'));
        if (isset($filter['installed'])) {
            $query->where($db->qn('installed')."=".$db->q($filter['installed']));
        }
        if (isset($filter['alias'])) {
            $query->where($db->qn('alias')."=".$db->q($filter['alias']));
        }
        if (isset($filter['parent'])) {
            $query->where($db->qn('parent')."=".$db->q($filter['parent']));
        }
        if (isset($filter['required'])) {
            $query->where($db->qn('required')."=".(int)$filter['required']);
        }
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function deleteByParent($parent, $required = null) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->qn('#__jshopping_addons_dependencies'));
        $query->where($db->qn('parent')."=".$db->q($parent));
        if (isset($required)) {
            $query->where($db->qn('required')."=".(int)$required);
        }
        $db->setQuery($query);
        $db->execute();
    }

    public function autoInstallAll($msg = 1){
        Factory::getLanguage()->load('com_installer');
        $this->systemAddonsCheck();
        $list = $this->getList(['installed' => 0]);
        foreach($list as $item) {
            if ($this->checkInstall($item->alias, $item->version)) {
                Helper::saveToLog("install_dep.log", 'checkinstall ok '.$item->alias." ".$item->version);
                $this->saveAutoInstallResult($item->id, 1);
                $this->runScriptInstall($item);
            } elseif ($item->required == -1) {
                if ($msg && $this->checkDisplayMsg($item)) {
                    $this->msgOffer($item);
                    $this->saveDisplayCount($item->id);
                }
            } elseif ($item->error == 0 || $this->isPaidError($item->error)) {
                // paid errors are retried: the API key may be set or the addon purchased later
                $this->autoInstall($item, $msg);
            } elseif ($msg && $this->checkDisplayMsg($item)) {
                $this->msgInstall($item);
                $this->saveDisplayCount($item->id);
            }
        }
    }

    public function clearSystemAddons(){
        $this->deleteByParent('', 1);
    }

    public function systemAddonsCheck(){
        $jshopConfig = JSFactory::getConfig();
        foreach($jshopConfig->system_addons as $key => $data){
            if (!$this->checkNeedSystemAddon($key, $data)){
                continue;
            }
            $addonDep = JSFactory::getTable('addondependencies');
            $required = $data['required'] ?? 1;
            $addonDep->loadAlias($data['alias'], '');
            $addonDep->name = $data['name'];
            $addonDep->version = $data['version'];
            // keep the consent given for an offered addon (-1 -> 0)
            if (!($required == -1 && $addonDep->id && $addonDep->required == 0)) {
                $addonDep->required = $required;
            }
            $addonDep->store();
        }
    }

    protected function checkNeedSystemAddon($key, $data){
        $jshopConfig = JSFactory::getConfig();
        $need = 1;
        if ($key == 'tcpdf'){
            $lib_file = JPATH_ROOT.'/components/com_jshopping/Lib/tcpdf/tcpdf.php';
            $need = $jshopConfig->generate_pdf && !file_exists($lib_file);
        }
        Factory::getApplication()->triggerEvent('onCheckNeedSystemAddon', array(&$key, &$data, &$need));
        return $need;
    }

    public function checkInstall($alias, $version) {
        $installed_version = $this->getInstalledVersion($alias);
        if ($installed_version) {
            return version_compare($installed_version, $version, '>=');
        } else {
            return false;
        }
    }

    public function getInstalledVersion($alias){
        $addon = JSFactory::getTable('addon');
        $addon->loadAlias($alias);
        if ($addon->id) {
            return $addon->version;
        } else {
            return null;
        }
    }

    public function autoInstall($item, $msg = 1){
        if (JSFactory::getConfig()->installaddon_disable) {
            if ($msg && $this->checkDisplayMsg($item)) {
                $this->msgInstall($item);
                $this->saveDisplayCount($item->id);
            }
            return 0;
        }
        try {
            $version = $item->version;
            $error = self::ERROR_INSTALL;
            $file = $this->isPaidError($item->error) ? null : $this->getSearchFileInstallWeb($item);
            $installtype = 'url';
            if ($file && ($file->download ?? '')) {
                $install_path = 'sm2:'.$file->download;
            } else {
                $install_path = $this->getInstallFilePaid($item, $error);
                $installtype = 'file';
            }
            if (!$install_path) {
                $this->saveAutoInstallResult($item->id, 0, $error);
                if ($this->isPaidError($error)) {
                    if ($msg && $this->checkDisplayMsg($item)) {
                        $item->error = $error;
                        $this->msgInstall($item);
                        $this->saveDisplayCount($item->id);
                    }
                } else {
                    JSError::raiseError(501, Text::_('JSHOP_ERROR_INSTALL').' '.$item->name." ".$version);
                }
                return 0;
            }
            $model = JSFactory::getModel('Update');
            $app = Factory::getApplication();
            $result = false;
            try {
                $app->triggerEvent('onBeforeInstallAddon', array(&$install_path, &$installtype, &$item, 'dependencies'));
                $model->install($install_path, $installtype);
                $result = true;
            } finally {
                $app->triggerEvent('onAfterInstallAddon', array(&$install_path, &$installtype, &$item, 'dependencies', $result));
                if ($installtype == 'file') {
                    @unlink($install_path);
                }
            }
            if ($installtype == 'file') {
                JSFactory::getModel('Addonscatalog')->setAddonKeyAfterInstall($item->alias);
            }
            JSError::raiseMessage(100, Text::_('JSHOP_INSTALLED').' '.$item->name." ".$version);
            Helper::saveToLog("install_dep.log", 'Installed '.$item->name." ".$version);
            $this->saveAutoInstallResult($item->id, 1);
            $this->runScriptInstall($item);
            return 1;
        } catch (Exception $e) {
            $this->saveAutoInstallResult($item->id, 0);
            if ($msg) {
                JSError::raiseError(500, Text::_('JSHOP_ERROR_INSTALL').' '.$item->name." ".$version);
                Helper::saveToLog("install_dep.log", 'Error install '.$item->name." ".$version);
            }
            Helper::saveToLog("install_dep.log", $e->getMessage());
            return 0;
        }
    }

    // includes the parent addon script (path relative to components/com_jshopping) after the dependency is installed
    public function runScriptInstall($item) {
        $script = trim($item->scriptinstall ?? '');
        if ($script == '') {
            return 0;
        }
        $file = JPATH_ROOT.'/components/com_jshopping/'.ltrim($script, '/');
        if (str_contains($script, '..') || strtolower(pathinfo($file, PATHINFO_EXTENSION)) != 'php' || !is_file($file)) {
            Helper::saveToLog("install_dep.log", 'scriptinstall not found: '.$script.' ('.$item->alias.')');
            return 0;
        }
        try {
            (static function($item) use ($file) {
                include $file;
            })($item);
            Helper::saveToLog("install_dep.log", 'scriptinstall '.$script.' ('.$item->alias.')');
            return 1;
        } catch (\Throwable $e) {
            Helper::saveToLog("install_dep.log", 'scriptinstall error '.$script.' ('.$item->alias.'): '.$e->getMessage());
            return 0;
        }
    }

    public function getSearchFileInstallWeb($item) {
        $api = JSFactory::getModel('Addonswebapi');
        $api->useCache(0);
        $files = $api->files($item->alias);
        if (!$files) {
            Helper::saveToLog("install_dep.log", 'files for alias: '.$item->alias.' not found');
            return null;
        }
        foreach($files as $file) {
            if ($file->version == $item->version) {
                return $file;
            }
        }
        Helper::saveToLog("install_dep.log", 'file for alias: '.$item->alias.' version: '.$item->version.' not found');
        return null;
    }

    public function isPaidError($error) {
        return in_array((int)$error, [self::ERROR_NO_API_KEY, self::ERROR_NOT_PURCHASED]);
    }

    // downloads the paid addon package into tmp, returns the file path or '' (reason in $error)
    public function getInstallFilePaid($item, &$error = null) {
        $error = self::ERROR_INSTALL;
        $catalog = JSFactory::getModel('Addonscatalog');
        $products = $catalog->getListByAlias();
        $product = $products[$item->alias] ?? null;
        if (!$product || $product->free) {
            return '';
        }
        if (!JSFactory::getConfig()->addonshop_api_key) {
            $error = self::ERROR_NO_API_KEY;
            Helper::saveToLog("install_dep.log", 'paid addon, api key not set: '.$item->alias);
            return '';
        }
        $domain = Helper::getClearHost();
        if (!in_array($product->id, $catalog->getListMyPaidAddonsId($domain))) {
            $error = self::ERROR_NOT_PURCHASED;
            Helper::saveToLog("install_dep.log", 'paid addon not purchased: '.$item->alias.' domain: '.$domain);
            return '';
        }
        try {
            return $catalog->downloadAddon($product->id);
        } catch (Exception $e) {
            Helper::saveToLog("install_dep.log", 'paid addon download error: '.$item->alias.' '.$e->getMessage());
            return '';
        }
    }

    public function saveAutoInstallResult($id, $result, $error = self::ERROR_INSTALL) {
        $table = JSFactory::getTable('addondependencies');
        $table->id = $id;
        if ($result == 1) {
            $table->installed = 1;
            $table->error = 0;
        } else {
            $table->error = $error;
        }
        $table->store();
    }

    // offered addon (required = -1): the user agreed, it becomes optional and is installed
    public function agreeInstall($id) {
        Factory::getLanguage()->load('com_installer');
        $item = JSFactory::getTable('addondependencies');
        $item->load($id);
        if (!$item->id || $item->installed || $item->required != -1) {
            return 0;
        }
        Factory::getApplication()->triggerEvent('onBeforeAgreeInstallAddonDependency', array(&$item));
        $item->required = 0;
        $item->error = 0;
        $item->store();
        if ($this->checkInstall($item->alias, $item->version)) {
            $this->saveAutoInstallResult($item->id, 1);
            $this->runScriptInstall($item);
            return 1;
        }
        return $this->autoInstall($item);
    }

    public function checkDisplayMsg($item) {
        if ($item->required == 1) {
            return 1;
        }
        return $item->display_count < 1;
    }

    public function saveDisplayCount($id) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__jshopping_addons_dependencies'));
        $query->set($db->qn('display_count')." = ".$db->qn('display_count')." + 1");
        $query->where($db->qn('id')."=".(int)$id);
        $db->setQuery($query);
        $db->execute();
    }

    public function msgInstall($item) {
        JSError::raiseWarning(300, Text::_('JSHOP_PLEASE_INSTALL').' '.$item->name." ".$item->version);
        $caturl = 'index.php?option=com_jshopping&controller=addonscatalog';
        if ($item->error == self::ERROR_NO_API_KEY && !JSFactory::getConfig()->apikey_edit_disable) {
            JSError::raiseWarning(300, Text::sprintf('JSHOP_ADDON_DEP_NO_API_KEY', $item->name, 'index.php?option=com_jshopping&controller=addonscatalog&task=apikey'));
        } elseif ($item->error == self::ERROR_NOT_PURCHASED) {
            JSError::raiseWarning(300, Text::sprintf('JSHOP_ADDON_DEP_NOT_PURCHASED', $item->name, Helper::getClearHost()));
        }
        $site = JSFactory::getConfig()->website_addons_url;
        JSError::raiseWarning(300, Text::sprintf('JSHOP_ADDON_USE_CATALOG_OR_WEBSITE', $caturl, $site));
        
        return 1;
    }

    public function msgOffer($item) {
        $url = 'index.php?option=com_jshopping&controller=addons&task=agreeinstalldependency&id='.(int)$item->id.'&'.Session::getFormToken().'=1';
        JSError::raiseNotice(300, Text::sprintf('JSHOP_ADDON_DEP_OFFER_INSTALL', $item->name, $item->version, $url));
        return 1;
    }
}