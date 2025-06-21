<?php
/**
* @version      5.8.0 24.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\Component\Jshopping\Site\Helper\Helper;

defined('_JEXEC') or die();

class AddonTable extends ShopbaseTable{
    
    public function __construct(&$_db){
        parent::__construct('#__jshopping_addons', 'id', $_db);
    }
    
    public function setParams($params, $only_new = 0){
        if ($only_new == 0) {
            $this->params = serialize($params);
        } else {
            $save_params = $this->getParams();
            foreach($params as $k=>$v) {
                if (!isset($save_params[$k])) {
                    $save_params[$k] = $v;
                }
            }
            $this->params = serialize($save_params);
        }
    }
        
    public function getParams(){
        if ($this->params != ""){
            return unserialize($this->params);
        }else{
            return [];
        }
    }

    public function setConfig($params, $only_new = 0){
        if ($only_new == 0) {
            $this->config = serialize($params);
        } else {
            $save_params = $this->getConfig();
            foreach($params as $k=>$v) {
                if (!isset($save_params[$k])) {
                    $save_params[$k] = $v;
                }
            }
            $this->config = serialize($save_params);
        }
    }
        
    public function getConfig(){
        if ($this->config != ""){
            return unserialize($this->config);
        }else{
            return [];
        }
    }
    
    public function setTmpVars(array $vars = [], $only_new = 0) {
        $config = $this->getConfig();
        if ($only_new == 0) {
            $config['tmp_vars'] = $vars;
        } else {
            $config['tmp_vars'] = $config['tmp_vars'] ?? [];
            foreach($vars as $k=>$v) {
                $config['tmp_vars'][$k] = $config['tmp_vars'][$k] ?? $v;
            }
        }
        $this->setConfig($config);
    }

    public function loadAlias($alias){
        $this->load(array('alias'=>$alias));
        $this->alias = $alias;
    }
    
    public function getKeyForAlias($alias){
        $db = Factory::getDbo();
        $query = "select `key` from #__jshopping_addons where `alias`='".$db->escape($alias)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
	
	public function installJoomlaExtension($data, $installexist = 0){
        $db = Factory::getDbo();
        $db->setQuery("SELECT extension_id FROM `#__extensions` WHERE element='".$db->escape($data['element'])."' AND folder='".$db->escape($data['folder'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('extension', 'Joomla\\CMS\\Table\\');
        if ($exid){
            $extension->load($exid);
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
	
	public function installJoomlaModule($data, $installexist = 0){
        $db = Factory::getDbo();
        $db->setQuery("SELECT id FROM `#__modules` WHERE module='".$db->escape($data['module'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('module', 'Joomla\\CMS\\Table\\');
        if ($exid){
            $extension->load($exid);
        }
        $extension->bind($data);
        if ($extension->check()) {
            $extension->store();
            $db->setQuery('SELECT `moduleid` FROM `#__modules_menu` WHERE `moduleid`='.$extension->id);
			$moduleid = $db->loadResult();
            if (!$moduleid) {
                $db->setQuery('INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ('.$extension->id.', 0)');
                $db->execute();
            }
            return 1;
        }else{
            return 0;
        }
    }
    
    public function installShipping($data, $installexist = 0){
        $db = Factory::getDbo();
        $db->setQuery("SELECT id FROM `#__jshopping_shipping_ext_calc` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('shippingExt');
        if ($exid){
            $extension->load($exid);
        }
        if (!$exid){
            $query = "SELECT MAX(ordering) FROM `#__jshopping_shipping_ext_calc`";
            $db->setQuery($query);
            $extension->ordering = $db->loadResult() + 1;
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
	
	public function installShippingMethod($data, $installexist = 0){
        $db = Factory::getDbo();
        $db->setQuery("SELECT shipping_id FROM `#__jshopping_shipping_method` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('shippingMethod');
        if ($exid){
            $extension->load($exid);
        }
        if (!$exid){
            $query = "SELECT MAX(ordering) FROM `#__jshopping_shipping_method`";
            $db->setQuery($query);
            $extension->ordering = $db->loadResult() + 1;
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
    
    public function installPayment($data, $installexist = 0){
        $db = Factory::getDbo();
        $db->setQuery("SELECT payment_id FROM `#__jshopping_payment_method` WHERE `payment_class`='".$db->escape($data['payment_class'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('paymentMethod');
        if ($exid){
            $extension->load($exid);
        }
        if (!$exid){
            $query = "SELECT MAX(payment_ordering) FROM `#__jshopping_payment_method`";
            $db->setQuery($query);
            $extension->payment_ordering = $db->loadResult() + 1;
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
    public function installImportExport($data, $installexist = 0){
        $db = Factory::getDbo();
        $db->setQuery("SELECT id FROM `#__jshopping_import_export` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('importExport');
        if ($exid){
            $extension->load($exid);
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
    
    public function addFieldTable($table, $field, $type){
        $db = Factory::getDBO();
        $listfields = $db->getTableColumns($table);
        if (!isset($listfields[$field])){
            $query = "ALTER TABLE ".$db->quoteName($table)." ADD ".$db->quoteName($field)." ".$type;
            $db->setQuery($query);
            $db->execute();
        }
    }

    public function addMultiLangFieldTable($table, $field, $type){
        $db = Factory::getDBO();
        $listfields = $db->getTableColumns($table);
        $langs = JSFactory::getModel('languages')->getAllTags();
        foreach($langs as $lang) {
            $table_field = $field.'_'.$lang;
            if (!isset($listfields[$table_field])){
                $query = "ALTER TABLE ".$db->quoteName($table)." ADD ".$db->quoteName($table_field)." ".$type;
                $db->setQuery($query);
                $db->execute();
            }
        }
    }
	
	public function deleteFieldTable($table, $field){
		$db = Factory::getDBO();
		$query = "ALTER TABLE ".$db->quoteName($table)." DROP ".$db->quoteName($field);
		$db->setQuery($query);
		$db->execute();
	}

	public function deleteTable($name) {
        $db = Factory::getDbo();
        $query = 'DROP TABLE IF EXISTS '.$db->quoteName($name);
        $db->setQuery($query);
		$db->execute();
    }
	
	public function unInstallJoomlaExtension($type, $element, $folder){
		$db = Factory::getDbo();
		$query = "delete from `#__extensions` WHERE element='".$db->escape($element)."' AND folder='".$db->escape($folder)."' AND `type`='".$db->escape($type)."'";
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function unInstallJoomlaModule($name){
		$db = Factory::getDbo();
		$query = "DELETE FROM `#__modules` WHERE module='".$db->escape($name)."'";
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function deleteFolders($folders){
		foreach($folders as $folder){
			if ($folder!=''){
				Folder::delete(JPATH_ROOT."/".$folder);
			}
		}
	}
	
	public function deleteFiles($files){
		foreach($files as $file){
			if ($file!=''){
				File::delete(JPATH_ROOT."/".$file);
			}
		}
	}

    public function getFolder() {
        return JPATH_ROOT."/components/com_jshopping/addons/".$this->alias;
    }

    public function published() {
        $file = $this->getFolder()."/published.php";
        if (file_exists($file)) {
            include $file;
        } else {
            $je_list = $this->getJoomlaExtensions();
            $this->joomlaExtensionsPublish($je_list, 1);
        }
        $this->publish = 1;
        $this->store();
    }

    public function unpublished() {
        $file = $this->getFolder()."/unpublished.php";
        if (file_exists($file)) {
            include $file;
        } else {
            $je_list = $this->getJoomlaExtensions();
            $this->joomlaExtensionsPublish($je_list, 0);
        }
        $this->publish = 0;
        $this->store();
    }

    public function getJoomlaExtensionsFromConfigFile() {
        $file = $this->getFolder()."/joomla_extensions.php";
        if (file_exists($file)) {
            return include $file;
        } else {
            return [];
        }
    }

    public function getJoomlaExtensionsDefault() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select(['type','element','folder']);
        $query->from($db->qn('#__extensions'));
        $query->where($db->qn('type')."=".$db->q('plugin'));
        $query->where($db->qn('element')."=".$db->q($this->alias));
        $db->setQuery($query);
        return $db->loadAssocList();
    }

    public function getJoomlaExtensions() {
        $list = $this->getJoomlaExtensionsFromConfigFile();
        if (count($list) == 0) {
            $list = $this->getJoomlaExtensionsDefault();
        }
        return $list;
    }

    public function joomlaExtensionsPublish($list, $flag) {
        $db = Factory::getDbo();
        foreach($list as $item) {
            $query = $db->getQuery(true);
            $query->update('#__extensions')
            ->set($db->qn('enabled')." = ".(int)$flag)
            ->where($db->qn('type')." = ". $db->q($item['type']))
            ->where($db->qn('element')." = ". $db->q($item['element']))
            ->where($db->qn('folder')." = ". $db->q($item['folder']));
            $db->setQuery($query);
            $db->execute();
        }
    }

	public function store($updateNulls = false){
        if (isset($this->_dependencies)){
            foreach($this->_dependencies as $item){
	            $addonDep = JSFactory::getTable('addondependencies');
	            $addonDep->loadAlias($item['alias'], $item['parent'] ?? $this->alias);
	            $addonDep->name = $item['name'];
	            $addonDep->version = $item['version'];
	            $addonDep->installed = $item['installed'] ?? 0;
	            $addonDep->error = $item['error'] ?? 0;
	            $addonDep->store();
            }
        }
        if (isset($this->_publish) && (!$this->id || $this->publish == -1)) {
            $this->publish = $this->_publish;
        }
        if (isset($this->_tmp_vars)) {
            $this->setTmpVars($this->_tmp_vars, 1);
        }
		return parent::store($updateNulls);
	}

}