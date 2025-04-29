<?php
/**
* @version      5.6.0 01.03.2025
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
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;

defined('_JEXEC') or die();

class AddondependenciesModel extends BaseadminModel{

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
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function deleteByParent($parent) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->qn('#__jshopping_addons_dependencies'));
        $query->where($db->qn('parent')."=".$db->q($parent));
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
            } elseif ($item->error == 0) {
                $this->autoInstall($item, $msg = 1);
            } elseif ($msg) {
                $this->msgInstall($item);
            }
        }
    }

    public function clearSystemAddons(){
        $this->deleteByParent('');
    }

    public function systemAddonsCheck(){
        $jshopConfig = JSFactory::getConfig();
        $lib_file = JPATH_ROOT.'/components/com_jshopping/Lib/tcpdf/tcpdf.php';
        if ($jshopConfig->generate_pdf && !file_exists($lib_file)) {
            $data = $jshopConfig->system_addons['tcpdf'];
            $addonDep = JSFactory::getTable('addondependencies');
            $addonDep->loadAlias($data['alias'], '');
            $addonDep->name = $data['name'];
            $addonDep->version = $data['version'];
            $addonDep->store();
        }
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
        try {
            $version = $item->version;
            $file = $this->getSearchFileInstallWeb($item);
            if (!$file || !$file->download) {
                $this->saveAutoInstallResult($item->id, 0);
                JSError::raiseError(501, Text::_('JSHOP_ERROR_INSTALL').' '.$item->name." ".$version);
                return 0;
            }
            $install_path = 'sm2:'.$file->download;
            $model = JSFactory::getModel('Update');
		    $model->install($install_path, 'url');
            JSError::raiseMessage(100, Text::_('JSHOP_INSTALLED').' '.$item->name." ".$version);
            Helper::saveToLog("install_dep.log", 'Installed '.$item->name." ".$version);
            $this->saveAutoInstallResult($item->id, 1);
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

    public function saveAutoInstallResult($id, $result) {
        $table = JSFactory::getTable('addondependencies');
        $table->id = $id;
        if ($result == 1) {
            $table->installed = 1;
        } else {
            $table->error = 1;
        }
        $table->store();
    }

    public function msgInstall($item) {
        JSError::raiseWarning(300, Text::_('JSHOP_PLEASE_INSTALL').' '.$item->name." ".$item->version);
        $caturl = 'index.php?option=com_jshopping&controller=addons&task=listweb';
        $site = JSFactory::getConfig()->website_addons_url;
        JSError::raiseWarning(300, Text::sprintf('JSHOP_ADDON_USE_CATALOG_OR_WEBSITE', $caturl, $site));
        
        return 1;
    }
}