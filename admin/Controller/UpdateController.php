<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;

defined('_JEXEC') or die();
//jimport('joomla.filesystem.file');
//jimport('joomla.filesystem.folder');
//jimport('joomla.filesystem.archive');
//jimport('joomla.filesystem.path');

class UpdateController extends BaseadminController{
    
    function init(){
        \JSHelperAdmin::checkAccessController("update");
        \JSHelperAdmin::addSubmenu("update");
        $language = \JFactory::getLanguage(); 
        $language->load('com_installer');
    }

    function display($cachable = false, $urlparams = false){		                
		$view = $this->getView("update", 'html');  
        $view->set('etemplatevar1', '');
        $view->set('etemplatevar2', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
		$view->display(); 
    }

	function update(){       
        $installtype = $this->input->getVar('installtype');
        $jshopConfig = \JSFactory::getConfig();
        $back = $this->input->getVar('back');

        if (!extension_loaded('zlib')){
            \JSError::raiseWarning('', \JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
            $this->setRedirect("index.php?option=com_jshopping&controller=update");
            return false;
        }
        
        if ($installtype == 'package'){
            \JSession::checkToken() or die('Invalid Token');
            $userfile = $this->input->files->get('install_package', null, 'raw');
            if (!(bool) ini_get('file_uploads')) {
                \JSError::raiseWarning('', \JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            if (!is_array($userfile) ) {
                \JSError::raiseWarning('', \JText::_('No file selected'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            if ( $userfile['error'] || $userfile['size'] < 1 ){
                \JSError::raiseWarning('', \JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $config = \JFactory::getConfig();            
            $tmp_dest = $config->get('tmp_path').'/'.$userfile['name'];            
            $tmp_src = $userfile['tmp_name'];
            jimport('joomla.filesystem.file');
            $uploaded = \JFile::upload($tmp_src, $tmp_dest, false, true);
            $archivename = $tmp_dest;            
            $tmpdir = uniqid('install_');
            $extractdir = \JPath::clean(dirname($archivename).'/'.$tmpdir);
            $archivename = \JPath::clean($archivename);
        }else {
            jimport('joomla.installer.helper');
            $url = $this->input->getVar('install_url');
            if (preg_match('/https?:\/\//', $url)){
                \JSession::checkToken() or die('Invalid Token');
            }
            if (preg_match('/(sm\d+):(.*)/',$url, $matches)){
                $url = $jshopConfig->updates_server[$matches[1]]."/".$matches[2];
            }
            if (!$url){
                \JSError::raiseWarning('', \JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $p_file = \JInstallerHelper::downloadPackage($url);
            if (!$p_file) {
                \JSError::raiseWarning('', \JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $config = \JFactory::getConfig();
            $tmp_dest = $config->get('tmp_path');
            $tmpdir = uniqid('install_');
            $extractdir = \JPath::clean(dirname(JPATH_BASE).'/tmp/'.$tmpdir);
            $archivename = \JPath::clean($tmp_dest.'/'.$p_file);
        }
        
		\JSHelper::saveToLog("install.log", "\nStart install: ".$archivename." IP:".$_SERVER['REMOTE_ADDR']." UID:".\JFactory::getUser()->id);
		
        $archive = new \Joomla\Archive\Archive();
        $result = $archive->extract($archivename, $extractdir);
        if ($result === false){
            \JSError::raiseWarning('500', "Archive error");
            \JSHelper::saveToLog("install.log", "Archive error");
            $this->setRedirect("index.php?option=com_jshopping&controller=update");
            return false;
        }
		
		$pathinfo = pathinfo($archivename);
		$this->backup_folder = 'jsbk'.date('ymdHis').'_'.$pathinfo['filename'];
        
        if (file_exists($extractdir."/checkupdate.php")) include($extractdir."/checkupdate.php");                        
        if (file_exists($extractdir."/configupdate.php")) include($extractdir."/configupdate.php");
        
        if (isset($configupdate['version']) && !$this->checkVersionUpdate($configupdate['version'])){
            $this->setRedirect("index.php?option=com_jshopping&controller=update"); 
            return 0;
        }
        
        if (!$this->copyFiles($extractdir)){
            \JSError::raiseWarning(500, \JText::_('JSHOP_INSTALL_THROUGH_JOOMLA'));
            \JSHelper::saveToLog("install.log", 'INSTALL_THROUGH_JOOMLA');
            $this->setRedirect("index.php?option=com_jshopping&controller=update"); 
            return 0;
        }
		
        if (file_exists($extractdir."/update.sql")){
            $db = \JFactory::getDBO();
            $lines = file($extractdir."/update.sql");
            $fullline = implode(" ", $lines);
            $queryes = $db->splitSql($fullline);            
            foreach($queryes as $query){
                if (trim($query)!=''){
                    try{
                        $db->setQuery($query);
                        $db->execute();
                    }catch(\Exception $e){
                        \JSError::raiseWarning(500, $e->getMessage());
                        \JSHelper::saveToLog("install.log", "Update - ".$e->getMessage());
                    }
                }
            }            
        }
        if (file_exists($extractdir."/update.php")) include($extractdir."/update.php");
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onAfterUpdateShop', array($extractdir));
        @unlink($archivename);
		\JFolder::delete($extractdir);
        
        $session = \JFactory::getSession();
        $checkedlanguage = array();
        $session->set("jshop_checked_language", $checkedlanguage);        
        $msg = \JText::_('JSHOP_COMPLETED');
        if (isset($configupdate['MASSAGE_COMPLETED'])){
            $msg = $configupdate['MASSAGE_COMPLETED'];
        }
        if ($back==''){
            $this->setRedirect("index.php?option=com_jshopping&controller=update", $msg); 
        }else{
            $this->setRedirect($back, $msg);
        }
    }
    
    function copyFiles($startdir, $subdir = ""){
        
        if ($subdir!="" && !file_exists(JPATH_ROOT.$subdir)){
            @mkdir(JPATH_ROOT.$subdir, 0755);
        }
        
        $files = \JFolder::files($startdir.$subdir, '', false, false, array(), array());
        foreach($files as $file){
            if ($subdir=="" && ($file=="update.sql" || $file=="update.php" || $file=="checkupdate.php" || $file=="configupdate.php")){
                continue;
            }
            if ($subdir==""){
                $fileinfo = pathinfo($file);
                if (strtolower($fileinfo['extension'])=='xml'){
                    return 0;
                }
            }
            
			if (\JSFactory::getConfig()->auto_backup_addon_files && file_exists(JPATH_ROOT.$subdir."/".$file)){
				\JFolder::create(JPATH_ROOT.'/tmp/'.$this->backup_folder.$subdir);
				copy(JPATH_ROOT.$subdir."/".$file, JPATH_ROOT.'/tmp/'.$this->backup_folder.$subdir."/".$file);
			}
            if (@copy($startdir.$subdir."/".$file, JPATH_ROOT.$subdir."/".$file)){
                \JSHelper::saveToLog("install.log", "Copy file: ".$subdir."/".$file);
            }else{
                \JSError::raiseWarning("", "Copy file: ".$subdir."/".$file." ERROR");
                \JSHelper::saveToLog("install.log", "Copy file: ".$subdir."/".$file." ERROR");
            }
        }
        
        $folders = \JFolder::folders($startdir.$subdir, '');
        foreach($folders as $folder){
            $dir = $subdir."/".$folder;            
            $this->copyFiles($startdir, $dir);
        }
        return 1;
    }
    
    function checkVersionUpdate($version){
        $jshopConfig = \JSFactory::getConfig();
        
        $currentVersion = $jshopConfig->getVersion();
        $groupVersion = intval($currentVersion);
        
        if (isset($version[$groupVersion])){
            $min = $version[$groupVersion]['min'];
            $max = $version[$groupVersion]['max'];
            $min_cmp = version_compare($currentVersion, $min);
            $max_cmp = version_compare($currentVersion, $max);            
            if ($min_cmp<0){
                \JSError::raiseWarning("", sprintf(\JText::_('JSHOP_MIN_VERSION_ERROR'), $min));
                \JSHelper::saveToLog("install.log", "Error: ".sprintf(\JText::_('JSHOP_MIN_VERSION_ERROR'), $min));
                return 0;
            }
            if ($max_cmp>0){
                \JSError::raiseWarning("", sprintf(\JText::_('JSHOP_MAX_VERSION_ERROR'), $max));
                \JSHelper::saveToLog("install.log", "Error: ".sprintf(\JText::_('JSHOP_MAX_VERSION_ERROR'), $max));
                return 0;
            }
        }
        return 1;
    }

}