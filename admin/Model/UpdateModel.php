<?php
/**
* @version      5.4.1 23.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Installer\Installer;

defined('_JEXEC') or die;

class UpdateModel extends BaseadminModel{

    protected $backup_folder = 'jsbk';
    protected $warnings = [];
    protected $back_url = '';

    public function install($install_path, $installtype) {
        $this->warnings = [];
        $this->back_url = '';

        $install_item_log = $installtype == 'package' ? $install_path['name'] : $install_path;
        Helper::saveToLog("install.log", "\nStart install: ".$install_item_log." ".$installtype." IP:".$_SERVER['REMOTE_ADDR']." UID:".Factory::getUser()->id);

        if ($installtype == 'package') {
            $archivename = $this->fileUpload($install_path);
        }
        if ($installtype == 'url') {
            $archivename = $this->fileDownload($install_path);
        }
        if ($installtype == 'file') {
            $archivename = $install_path;
        }
        if ($installtype == 'folder') {
            $dir = $install_path;
        }
        if (!isset($dir)) {
            $dir = JPATH_ROOT .'/tmp/'.uniqid('install_');
        }

        if ($installtype != 'folder') {
            if (!extension_loaded('zlib')){
                throw new Exception(Text::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
            }
            $archive = new \Joomla\Archive\Archive();
            $result = $archive->extract($archivename, $dir);
            if ($result === false) {
                throw new Exception("Archive extract error");
            }
            $pathinfo = pathinfo($archivename);
		    $this->backup_folder = 'jsbk'.date('ymdHis').'_'.$pathinfo['filename'];
        } else {
            $this->backup_folder = 'jsbk'.date('ymdHis').'_'.basename($dir);
        }

        if ($this->getExistJoomlaInstallFile($dir)) {
            Helper::saveToLog("install.log", 'installTypeJoomla');
            $this->installTypeJoomla($dir);
        } else {
            Helper::saveToLog("install.log", 'installTypeJoomshopping');
            $this->installTypeJoomshopping($dir);
        }

        $app = Factory::getApplication();
        $app->triggerEvent('onAfterUpdateShop', array($dir));
        if ($installtype != 'folder') {            
            Folder::delete($dir);
        }
        if (in_array($installtype, ['package', 'url'])) {
            @unlink($archivename);
        }

        $this->setJSLangageRecheck();
    }

    public function installTypeJoomshopping($dir) {
        if (file_exists($dir."/checkupdate.php")) include($dir."/checkupdate.php");
        if (file_exists($dir."/configupdate.php")) include($dir."/configupdate.php");

        if (isset($configupdate['version'])) {
            $this->checkVersionUpdate($configupdate['version']);
        }

        $this->copyFiles($dir);

        if (file_exists($dir."/update.sql")){
            $this->updateSqlExecute($dir."/update.sql");
        }

        if (file_exists($dir."/update.php")) include($dir."/update.php");

        if (isset($back)) {
            $this->back_url = $back;
        }

        $autoloadFile = JPATH_CACHE . '/autoload_psr4.php';
        if (file_exists($autoloadFile)) {
            File::delete($autoloadFile);
        }

        return 1;
    }

    public function installTypeJoomla($dir) {
        $type = InstallerHelper::detectType($dir);
        if (!$type) {
            throw new Exception(Text::_('COM_INSTALLER_MSG_INSTALL_PATH_DOES_NOT_HAVE_A_VALID_PACKAGE'));
        }
        $installer = Installer::getInstance();        
        $installer->setPath('source', $dir);
        if (!$installer->install($dir)) {
            $msg = Text::sprintf('COM_INSTALLER_INSTALL_ERROR', Text::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($type)));
            throw new Exception($msg);
        }
        return 1;
    }

    public function updateSqlExecute($file) {
        $db = Factory::getDBO();
        $lines = file($file);
        $fullline = implode(" ", $lines);
        $queryes = $db->splitSql($fullline);
        foreach($queryes as $query){
            if (trim($query) != '') {
                $db->setQuery($query);
                $db->execute();
            }
        }
    }    

    protected function copyFiles($startdir, $subdir = ""){
        if ($subdir!="" && !file_exists(JPATH_ROOT.$subdir)){
            @mkdir(JPATH_ROOT.$subdir, 0755);
        }
        
        $files = Folder::files($startdir.$subdir, '', false, false, array(), array());
        foreach($files as $file){
            $skip_copy_root = ["update.sql", "update.php", "checkupdate.php", "configupdate.php", "index.html", "index.php", "configuration.php"];
            if ($subdir=="" && in_array($file, $skip_copy_root)){
                Helper::saveToLog("install.log", "skip copy file: ".$subdir."/".$file);
                continue;
            }
			if (JSFactory::getConfig()->auto_backup_addon_files && file_exists(JPATH_ROOT.$subdir."/".$file)){
				Folder::create(JPATH_ROOT.'/tmp/'.$this->backup_folder.$subdir);
				copy(JPATH_ROOT.$subdir."/".$file, JPATH_ROOT.'/tmp/'.$this->backup_folder.$subdir."/".$file);
			}
            if (@copy($startdir.$subdir."/".$file, JPATH_ROOT.$subdir."/".$file)) {
                Helper::saveToLog("install.log", "Copy file: ".$subdir."/".$file);
            } else {
                $this->warnings[] = "Copy file: ".$subdir."/".$file." ERROR";
                Helper::saveToLog("install.log", "Copy file: ".$subdir."/".$file." ERROR");
            }
        }
        
        $folders = Folder::folders($startdir.$subdir, '');
        foreach($folders as $folder){
            $dir = $subdir."/".$folder;
            $this->copyFiles($startdir, $dir);
        }
        return 1;
    }

    protected function getExistJoomlaInstallFile($startdir){
        $files = Folder::files($startdir, '', false, false, array(), array());
        foreach($files as $file){
            $fileinfo = pathinfo($file);
            if (strtolower($fileinfo['extension'])=='xml'){
                return 1;
            }
        }
        return 0;
    }

    protected function checkVersionUpdate($version){
        $jshopConfig = JSFactory::getConfig();
        
        $currentVersion = $jshopConfig->getVersion();
        $groupVersion = intval($currentVersion);
        
        if (isset($version[$groupVersion])){
            $min = $version[$groupVersion]['min'];
            $max = $version[$groupVersion]['max'];
            $min_cmp = version_compare($currentVersion, $min);
            $max_cmp = version_compare($currentVersion, $max);
            if ($min_cmp < 0){
                Helper::saveToLog("install.log", "Error: ".sprintf(Text::_('JSHOP_MIN_VERSION_ERROR'), $min));
                throw new Exception(sprintf(Text::_('JSHOP_MIN_VERSION_ERROR'), $min));
            }
            if ($max_cmp > 0){
                Helper::saveToLog("install.log", "Error: ".sprintf(Text::_('JSHOP_MAX_VERSION_ERROR'), $max));
                throw new Exception(sprintf(Text::_('JSHOP_MAX_VERSION_ERROR'), $max));
            }
        }
        return 1;
    }

    public function getWarnings() {
        return $this->warnings;
    }

    public function getBackUrl() {
        return $this->back_url;
    }

    protected function setJSLangageRecheck() {
        $session = Factory::getSession();
        $checkedlanguage = array();
        $session->set("jshop_checked_language", $checkedlanguage);
    }

    protected function fileUpload($userfile) {
        if (!(bool)ini_get('file_uploads')) {
            throw new Exception(Text::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
        }
        if (!is_array($userfile) ) {
            throw new Exception('No file selected');
        }
        if ($userfile['error'] || $userfile['size'] < 1){            
            throw new Exception(Text::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
        }
        $config = Factory::getConfig();
        $tmp_dest = $config->get('tmp_path').'/'.$userfile['name'];
        $tmp_src = $userfile['tmp_name'];
        $uploaded = File::upload($tmp_src, $tmp_dest, false, true);
        $archivename = $tmp_dest;
        $archivename = Path::clean($archivename);
        return $archivename;
    }

    protected function fileDownload($url) {
        $jshopConfig = JSFactory::getConfig();
        if (preg_match('/(sm\d+):(.*)/', $url, $matches)){
            $url = $jshopConfig->updates_server[$matches[1]]."/".$matches[2];
        }
        if (!$url){
            throw new Exception(Text::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
        }
        $p_file = InstallerHelper::downloadPackage($url);
        if (!$p_file) {
            throw new Exception(Text::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
        }
        $config = Factory::getConfig();
        $tmp_dest = $config->get('tmp_path');
        return Path::clean($tmp_dest.'/'.$p_file);
    }

}