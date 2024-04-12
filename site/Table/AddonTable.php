<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die();

class AddonTable extends ShopbaseTable{
    
    var $id = null;
    var $alias = null;
    var $key = null;
    var $version = null;
    var $params = null;
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_addons', 'id', $_db);
    }
    
    function setParams($params){
        $this->params = serialize($params);
    }
        
    function getParams(){
        if ($this->params!=""){
            return unserialize($this->params);
        }else{
            return array();
        }
    }

    function loadAlias($alias){
        $this->load(array('alias'=>$alias));
        $this->alias = $alias;
    }
    
    function getKeyForAlias($alias){
        $db = \JFactory::getDbo();
        $query = "select `key` from #__jshopping_addons where `alias`='".$db->escape($alias)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
	
	function installJoomlaExtension($data, $installexist = 0){
        $db = \JFactory::getDbo();
        $db->setQuery("SELECT extension_id FROM `#__extensions` WHERE element='".$db->escape($data['element'])."' AND folder='".$db->escape($data['folder'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = \JSFactory::getTable('extension', 'Joomla\\CMS\\Table\\');
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
	
	function installJoomlaModule($data, $installexist = 0){
        $db = \JFactory::getDbo();
        $db->setQuery("SELECT id FROM `#__modules` WHERE module='".$db->escape($data['module'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = \JSFactory::getTable('module', 'Joomla\\CMS\\Table\\');
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
    
    function installShipping($data, $installexist = 0){
        $db = \JFactory::getDbo();
        $db->setQuery("SELECT id FROM `#__jshopping_shipping_ext_calc` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = \JSFactory::getTable('shippingExt');
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
	
	function installShippingMethod($data, $installexist = 0){
        $db = \JFactory::getDbo();
        $db->setQuery("SELECT shipping_id FROM `#__jshopping_shipping_method` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = \JSFactory::getTable('shippingMethod');
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
    
    function installPayment($data, $installexist = 0){
        $db = \JFactory::getDbo();
        $db->setQuery("SELECT payment_id FROM `#__jshopping_payment_method` WHERE `payment_class`='".$db->escape($data['payment_class'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = \JSFactory::getTable('paymentMethod');
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
    function installImportExport($data, $installexist = 0){
        $db = \JFactory::getDbo();
        $db->setQuery("SELECT id FROM `#__jshopping_import_export` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = \JSFactory::getTable('importExport');
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
    
    function addFieldTable($table, $field, $type){
        $db = \JFactory::getDBO();
        $listfields = $db->getTableColumns($table);
        if (!isset($listfields[$field])){
            $query = "ALTER TABLE ".$db->quoteName($table)." ADD ".$db->quoteName($field)." ".$type;
            $db->setQuery($query);
            $db->execute();
        }
    }
	
	function deleteFieldTable($table, $field){
		$db = \JFactory::getDBO();
		$query = "ALTER TABLE ".$db->quoteName($table)." DROP ".$db->quoteName($field);
		$db->setQuery($query);
		$db->execute();
	}

	function deleteTable($name) {
        $db = \JFactory::getDbo();
        $query = 'DROP TABLE IF EXISTS '.$db->quoteName($name);
        $db->setQuery($query);
		$db->execute();
    }
	
	function unInstallJoomlaExtension($type, $element, $folder){
		$db = \JFactory::getDbo();
		$query = "delete from `#__extensions` WHERE element='".$db->escape($element)."' AND folder='".$db->escape($folder)."' AND `type`='".$db->escape($type)."'";
		$db->setQuery($query);
		return $db->execute();
	}
	
	function unInstallJoomlaModule($name){
		$db = \JFactory::getDbo();
		$query = "DELETE FROM `#__modules` WHERE module='".$db->escape($name)."'";
		$db->setQuery($query);
		return $db->execute();
	}
	
	function deleteFolders($folders){
		jimport('joomla.filesystem.folder');
		foreach($folders as $folder){
			if ($folder!=''){
				\JFolder::delete(JPATH_ROOT."/".$folder);
			}
		}
	}
	
	function deleteFiles($files){
		jimport('joomla.filesystem.file');
		foreach($files as $file){
			if ($file!=''){
				\JFile::delete(JPATH_ROOT."/".$file);
			}
		}
	}

}