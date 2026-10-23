<?php
/**
* @version      5.10.0 18.10.2026
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

    protected $_json_fields = ['params', 'config'];

    public function __construct(&$_db){
        parent::__construct('#__jshopping_addons', 'id', $_db);
    }

    public function setParams($params, $only_new = 0){
        if ($only_new) {
            $params = $this->addNewParams($this->getParams(), $params);
        }
        $this->params = $this->encodeData($params);
    }

    public function getParams(){
        return $this->decodeData($this->params);
    }

    public function setConfig($params, $only_new = 0){
        if ($only_new) {
            $params = $this->addNewParams($this->getConfig(), $params);
        }
        $this->config = $this->encodeData($params);
    }

    public function getConfig(){
        return $this->decodeData($this->config);
    }

    protected function addNewParams($save_params, $params){
        $save_params = (array)$save_params;
        foreach($params as $k=>$v) {
            if (!isset($save_params[$k])) {
                $save_params[$k] = $v;
            }
        }
        return $save_params;
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
        $query = "select `key` from #__jshopping_addons where `alias`=".$db->q($alias);
        $db->setQuery($query);
        return $db->loadResult();
    }
	
	public function installJoomlaExtension($data, $installexist = 0){
        $db = Factory::getDbo();
        $db->setQuery("SELECT extension_id FROM `#__extensions` WHERE element=".$db->q($data['element'])." AND folder=".$db->q($data['folder']));
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
        $db->setQuery("SELECT id FROM `#__modules` WHERE module=".$db->q($data['module']));
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
        $db->setQuery("SELECT id FROM `#__jshopping_shipping_ext_calc` WHERE `alias`=".$db->q($data['alias']));
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
        $db->setQuery("SELECT shipping_id FROM `#__jshopping_shipping_method` WHERE `alias`=".$db->q($data['alias']));
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
        $db->setQuery("SELECT payment_id FROM `#__jshopping_payment_method` WHERE `payment_class`=".$db->q($data['payment_class']));
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
        $db->setQuery("SELECT id FROM `#__jshopping_import_export` WHERE `alias`=".$db->q($data['alias']));
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
		$query = "delete from `#__extensions` WHERE element=".$db->q($element)." AND folder=".$db->q($folder)." AND `type`=".$db->q($type);
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function unInstallJoomlaModule($name){
		$db = Factory::getDbo();
		$query = "DELETE FROM `#__modules` WHERE module=".$db->q($name);
		$db->setQuery($query);
		return $db->execute();
	}

    public function uninstallShipping($alias){
        $db = Factory::getDbo();
        $db->setQuery("SELECT id FROM `#__jshopping_shipping_ext_calc` WHERE `alias`=".$db->q($alias));
        $exid = (int)$db->loadResult();
        if (!$exid){
            return -1;
        }
        $extension = JSFactory::getTable('shippingExt');
        $extension->load($exid);
        return $extension->delete($exid) ? 1 : 0;
    }
	
	public function deleteFolders($folders){
		foreach($folders as $folder){
			if ($folder!='' && file_exists(JPATH_ROOT."/".$folder)){
				Folder::delete(JPATH_ROOT."/".$folder);
			}
		}
	}
	
	public function deleteFiles($files){
		foreach($files as $file){
			if ($file!='' && file_exists(JPATH_ROOT."/".$file)){
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

    public function getManifestCache($version, $creationDate = '', array $extra = []){
        $data = array_merge([
            'creationDate' => $creationDate ?: date('d.m.Y'),
            'author' => 'MAXXmarketing GmbH',
            'authorEmail' => 'marketing@maxx-marketing.net',
            'authorUrl' => 'http://www.webdesigner-profi.de',
            'version' => $version,
        ], $extra);
        return json_encode($data);
    }

    public function placeJoomlaModule(array $data, $assignment = 0){
        $db = Factory::getDbo();
        $db->setQuery("SELECT COUNT(*) FROM `#__modules` WHERE `module`=".$db->q($data['module'])." AND `position`=".$db->q($data['position'] ?? '')." AND `client_id`=0 AND `published`=1");
        if ((int)$db->loadResult()) {
            return -1;
        }
        $data = array_merge(['published' => 1, 'showtitle' => 0, 'access' => 1, 'language' => '*', 'client_id' => 0], $data);
        $module = JSFactory::getTable('module', 'Joomla\\CMS\\Table\\');
        $module->bind($data);
        if (!$module->check() || !$module->store()) {
            return 0;
        }
        $this->setJoomlaModuleAssignment($module->id, $assignment);
        return (int)$module->id;
    }

    public function moveJoomlaModule($module, $position, array $data = []){
        $db = Factory::getDbo();
        $db->setQuery("SELECT `id` FROM `#__modules` WHERE `module`=".$db->q($module)." AND `client_id`=0 AND `published`>=0 ORDER BY (`position`=".$db->q($position)." AND `published`=1) DESC, `published` DESC, `id`", 0, 1);
        $id = (int)$db->loadResult();
        if (!$id) {
            return 0;
        }
        $row = JSFactory::getTable('module', 'Joomla\\CMS\\Table\\');
        $row->load($id);
        $row->bind(array_merge($data, ['position' => $position, 'published' => 1]));
        $row->store();
        $db->setQuery("SELECT COUNT(*) FROM `#__modules_menu` WHERE `moduleid`=".$id);
        if (!(int)$db->loadResult()) {
            $this->setJoomlaModuleAssignment($id, 0);
        }
        return $id;
    }

    public function positionHasModules($position){
        $db = Factory::getDbo();
        $db->setQuery("SELECT COUNT(*) FROM `#__modules` WHERE `position`=".$db->q($position)." AND `client_id`=0 AND `published`=1");
        return (int)$db->loadResult();
    }

    public function setJoomlaModuleAssignment($moduleId, $assignment = 0){
        $db = Factory::getDbo();
        $db->setQuery("DELETE FROM `#__modules_menu` WHERE `moduleid`=".(int)$moduleId)->execute();
        $menuid = 0;
        if ($assignment == 1 || $assignment == 2) {
            $db->setQuery("SELECT `id` FROM `#__menu` WHERE `home`=1 AND `client_id`=0 ORDER BY `language`='*' DESC", 0, 1);
            $home = (int)$db->loadResult();
            $menuid = $assignment == 1 ? $home : -$home;
        }
        $db->setQuery("INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (".(int)$moduleId.", ".(int)$menuid.")")->execute();
    }

    public function installJoomlaTemplate($element, $title, $home = 1, $params = null, array $data = []){
        $this->installJoomlaExtension(array_merge([
            'name' => $element,
            'type' => 'template',
            'element' => $element,
            'folder' => '',
            'client_id' => 0,
            'enabled' => 1,
            'access' => 1,
            'protected' => 0,
        ], $data));

        $db = Factory::getDbo();
        $db->setQuery("SELECT `id` FROM `#__template_styles` WHERE `template`=".$db->q($element)." AND `client_id`=0 ORDER BY `home` DESC, `id`", 0, 1);
        $id = (int)$db->loadResult();
        if ($id) {
            return $id;
        }
        if ($home) {
            $db->setQuery("UPDATE `#__template_styles` SET `home`='0' WHERE `client_id`=0")->execute();
        }
        $style = (object)[
            'template' => $element,
            'client_id' => 0,
            'home' => $home ? '1' : '0',
            'title' => $title,
            'params' => $params ?? '{}',
        ];
        $db->insertObject('#__template_styles', $style, 'id');
        return (int)$style->id;
    }

    public function migrateJoomlaTemplate($old, $new){
        $db = Factory::getDbo();
        $db->setQuery("SELECT `params`, `home` FROM `#__template_styles` WHERE `template`=".$db->q($old)." AND `client_id`=0 ORDER BY `home` DESC, `id`", 0, 1);
        $oldStyle = $db->loadObject();
        if (!$oldStyle) {
            return false;
        }
        $set = [];
        if ($oldStyle->params != '' && $oldStyle->params != '{}') {
            $set[] = "`params`=".$db->q($oldStyle->params);
        }
        if ($oldStyle->home) {
            $db->setQuery("UPDATE `#__template_styles` SET `home`='0' WHERE `client_id`=0")->execute();
            $set[] = "`home`='1'";
        }
        if ($set) {
            $db->setQuery("SELECT `id` FROM `#__template_styles` WHERE `template`=".$db->q($new)." AND `client_id`=0 ORDER BY `id`", 0, 1);
            $newId = (int)$db->loadResult();
            if ($newId) {
                $db->setQuery("UPDATE `#__template_styles` SET ".implode(',', $set)." WHERE `id`=".$newId)->execute();
            }
        }
        $this->uninstallJoomlaTemplate($old, '');
        return true;
    }

    public function uninstallJoomlaTemplate($element, $fallback = 'cassiopeia'){
        $element = trim((string)$element);
        if ($element == '' || preg_match('/[^a-zA-Z0-9_\-.]/', $element) || strpos($element, '..') !== false) {
            return;
        }
        $db = Factory::getDbo();
        // default styles of the template: home = '1' or a language code (multilingual site)
        $db->setQuery("SELECT `home` FROM `#__template_styles` WHERE `template`=".$db->q($element)." AND `client_id`=0 AND `home`<>'0'");
        $homes = $db->loadColumn();
        if ($homes && $fallback !== '') {
            // style of $fallback, or any other site style if $fallback is not installed
            $db->setQuery("SELECT `id` FROM `#__template_styles` WHERE `template`<>".$db->q($element)." AND `client_id`=0 ORDER BY (`template`=".$db->q($fallback).") DESC, `id`", 0, 1);
            $fallbackId = (int)$db->loadResult();
            if ($fallbackId) {
                $home = in_array('1', $homes) ? '1' : $homes[0];
                $db->setQuery("UPDATE `#__template_styles` SET `home`=".$db->q($home)." WHERE `id`=".$fallbackId)->execute();
            }
        }
        $db->setQuery("DELETE FROM `#__template_styles` WHERE `template`=".$db->q($element)." AND `client_id`=0")->execute();
        $db->setQuery("DELETE FROM `#__extensions` WHERE `type`='template' AND `client_id`=0 AND `element`=".$db->q($element))->execute();
        $this->deleteFolders(['templates/'.$element]);
    }

    public function setShopConfig(array $data){
        $config = JSFactory::getTable('Config');
        $config->id = JSFactory::getConfig()->load_id;
        foreach ($data as $k => $v) {
            $config->$k = $v;
        }
        return $config->store();
    }

    public function restoreShopTemplate($template, $to = 'default'){
        if (JSFactory::getConfig()->template == $template) {
            $this->setShopConfig(['template' => $to]);
        }
    }

    public function getBundled(){
        return $this->getConfig()['bundled'] ?? [];
    }

    public function syncBundledKeys(){
        $bundled = $this->getBundled();
        if (!$bundled || !$this->key || !$this->alias) {
            return 0;
        }
        if (!Helper::compareX64(Helper::replaceWWW(Helper::getJHost()).$this->alias, $this->key)) {
            return 0;
        }
        $items = explode(',', (string)base64_decode($this->key));
        $db = Factory::getDbo();
        $updated = 0;
        foreach ($bundled as $alias) {
            $addonItems = [];
            foreach ($items as $item) {
                if (substr($item, -strlen($this->alias)) === $this->alias) {
                    $addonItems[] = substr($item, 0, -strlen($this->alias)).$alias;
                }
            }
            if (!$addonItems) {
                continue;
            }
            $db->setQuery("UPDATE `#__jshopping_addons` SET `key`=".$db->q(base64_encode(implode(',', $addonItems)))." WHERE `alias`=".$db->q($alias));
            $db->execute();
            $updated += $db->getAffectedRows() > 0 ? 1 : 0;
        }
        return $updated;
    }

	public function store($updateNulls = false){
        if (isset($this->_bundled)) {
            $config = $this->getConfig();
            $config['bundled'] = array_values(array_unique($this->_bundled));
            $this->setConfig($config);
        }
        if (isset($this->_dependencies)){
            foreach($this->_dependencies as $item){
	            $addonDep = JSFactory::getTable('addondependencies');
	            $addonDep->loadAlias($item['alias'], $item['parent'] ?? $this->alias);
	            $addonDep->name = $item['name'];
	            $addonDep->version = $item['version'];
	            $addonDep->required = $item['required'] ?? 1;
	            $addonDep->installed = $item['installed'] ?? 0;
	            $addonDep->error = $item['error'] ?? 0;
	            $addonDep->scriptinstall = $item['scriptinstall'] ?? '';
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