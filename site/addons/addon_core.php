<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.6.3 08.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class AddonCore{
    
    protected $addon_params;
    protected $addon_alias = '';
	protected $addon_id;
    protected $table = null;
    public $debug = 0;
    public $log = 0;
    public $tmp_vars = [];

    public function __construct($addon_alias = ''){
		if ($addon_alias != ''){
			$this->addon_alias = $addon_alias;
		}
        if ($this->addon_alias == ''){
            throw new Exception('addon_alias empty');
        }
        $this->initTmpVars();
        if (JSFactory::getConfig()->shop_mode > 0) {
            $config = $this->getTable()->getConfig();
            if (isset($config['debug']) && $config['debug']) {
                $this->debug = $config['debug'];
            }
            if (isset($config['log']) && $config['log']) {
                $this->log = $config['log'];
            }
        }
    }
    
    public function getAddonAlias(){
        return $this->addon_alias;
    }

    public function getAddonParams(){
        $this->addon_params = $this->getTable()->getParams();
        if ($this->debug > 1) {
            print '<pre>';
            print '#Addon '.$this->addon_alias.' params: '."\n";
            print_r($this->addon_params);
            print '</pre>';
        }
        return $this->addon_params;
    }

    public function getAddonParam($key, $default = null){
        $params = $this->getAddonParams();
        return $params[$key] ?? $default;
    }

    public function getAddonConfig(){
        return $this->getTable()->getConfig();
    }

    public function getTable(){
        if (!isset($this->table)){
            $this->table = JSFactory::getTable('addon');
            $this->table->loadAlias($this->addon_alias);
        }
        return $this->table;
    }
    
    public function getAddonId(){
        $this->addon_id = $this->getTable()->id;
        return $this->addon_id;
    }

    public function getPublish(){
        return $this->getTable()->publish;
    }

	public function getView($layout = '', $dir = null, $ovdir = null){
        $dir = $dir ?? 'components/com_jshopping/templates/addons/'.$this->addon_alias;
		$template = $this->getJoomlaTemplate();
        $config = $this->getAddonConfig();
		$ovdir = $ovdir ?? $config['folder_overrides_view'] ?? 'templates/'.$template.'/html/com_jshopping/addons/'.$this->addon_alias;
		$view_config = array("template_path" => JPATH_ROOT.'/'.$dir);
        $view = new Joomla\Component\Jshopping\Site\View\Addons\HtmlView($view_config);
		$view->addTemplatePath(JPATH_ROOT.'/'.$ovdir);
        if ($layout){
            $view->setLayout($layout);
        }
        $view->addon_path_images = $this->getPathImages();
        if ($this->debug) {
            print '<pre>';
            print '#Addon '.$this->addon_alias.' getView: '.$layout."\n";
            print '#Addon '.$this->addon_alias.' getView dir: '.$dir."\n";
            print '#Addon '.$this->addon_alias.' getView ovdir: '.$ovdir."\n";
            print '</pre>';
        }
        if ($this->log) {
            Helper::saveToLog($this->addon_alias.".log", 'getView: '.$layout);
            Helper::saveToLog($this->addon_alias.".log", 'getView dir: '.$dir);
            Helper::saveToLog($this->addon_alias.".log", 'getView ovdir: '.$ovdir);
        }
        return $view;
    }

    public function loadLanguage($langtag = ""){
        if ($this->debug > 1) {
            print '<pre>';
            print '#Addon '.$this->addon_alias.' language loaded '.$langtag."\n";
            print '</pre>';
        }
        if ($this->log) {
            Helper::saveToLog($this->addon_alias.".log", 'language loaded '.$langtag);
        }
        JSFactory::loadExtLanguageFile($this->addon_alias, $langtag);
    }
    
    public function loadCss($extname = '', $dir = null, $ovdir = null, $name_as_alias = 1, $wap = null){
        $template = $this->getJoomlaTemplate();
        $asset_name = $this->getAssetName($extname, $dir, $ovdir, $name_as_alias);
        $config = $this->getAddonConfig();
        $dir = $dir ?? 'components/com_jshopping/css/addons';
        $ovdir = $ovdir ?? $config['folder_overrides_css'] ?? 'templates/'.$template.'/css/addons';
        if ($name_as_alias) {
            $filename = $this->addon_alias.$extname.'.css';
        } else {
            $filename = $extname.'.css';
        }
        if ($this->debug) {
            print '<pre>';
            print '#Addon '.$this->addon_alias.' loadCss: '.$filename."\n";
            print '#Addon '.$this->addon_alias.' loadCss dir: '.$dir."\n";
            print '#Addon '.$this->addon_alias.' loadCss ovdir: '.$ovdir."\n";
            print '#Addon '.$this->addon_alias.' loadCss asset_name: '.$asset_name."\n";
            print '</pre>';
        }
        if (file_exists(JPATH_ROOT.'/'.$ovdir.'/'.$filename)) {
            $dir = $ovdir;
        }
        if ($this->log) {
            Helper::saveToLog($this->addon_alias.".log", 'loadCss: '.$dir.'/'.$filename);
        }
        $wa = JSFactory::getWebAssetManager();
        $wap = $wap ?? JSFactory::getConfig()->getWebAssetParams('style', $asset_name);
        $wa->registerAndUseStyle($asset_name, Uri::root().$dir.'/'.$filename, $wap['options'], $wap['attributes'], $wap['dependencies']);
    }
    
    public function loadJs($extname = '', $dir = null, $ovdir = null, $name_as_alias = 1, $wap = null){
        $template = $this->getJoomlaTemplate();
        $asset_name = $this->getAssetName($extname, $dir, $ovdir, $name_as_alias);
        $config = $this->getAddonConfig();
        $dir = $dir ?? 'components/com_jshopping/js/addons';
        $ovdir = $ovdir ?? $config['folder_overrides_js'] ?? 'templates/'.$template.'/js/addons';
        if ($name_as_alias) {
            $filename = $this->addon_alias.$extname.'.js';
        } else {
            $filename = $extname.'.js';
        }
        if ($this->debug) {
            print '<pre>';
            print '#Addon '.$this->addon_alias.' loadJs: '.$filename."\n";
            print '#Addon '.$this->addon_alias.' loadJs dir: '.$dir."\n";
            print '#Addon '.$this->addon_alias.' loadJs ovdir: '.$ovdir."\n";
            print '#Addon '.$this->addon_alias.' loadJs asset_name: '.$asset_name."\n";
            print '</pre>';
        }
        if (file_exists(JPATH_ROOT.'/'.$ovdir.'/'.$filename)) {
            $dir = $ovdir;
        }
        if ($this->log) {
            Helper::saveToLog($this->addon_alias.".log", 'loadJs: '.$dir.'/'.$filename);
        }
        $wa = JSFactory::getWebAssetManager();
        $wap = $wap ?? JSFactory::getConfig()->getWebAssetParams('script', $asset_name);
        $wa->registerAndUseScript($asset_name, Uri::root().$dir.'/'.$filename, $wap['options'], $wap['attributes'], $wap['dependencies']);
    }
    
    public function getPathImages(){
        return Uri::root().'components/com_jshopping/images/'.$this->addon_alias;
    }
    
    public function checkLicKey(){
		return Helper::compareX64(Helper::replaceWWW(Helper::getJHost().$this->addon_alias), $this->getTable()->key);
	}

    public function setTmpVar($name, $value){
        $this->tmp_vars[$name] = $value;
    }

    protected function initTmpVars() {
        $config = $this->getTable()->getConfig();
        $this->tmp_vars = $config['tmp_vars'] ?? JSFactory::getConfig()->override_tmp_vars[$this->addon_alias] ?? [];
    }

    protected function getJoomlaTemplate() {
        return Factory::getApplication()->getTemplate(true)->template;
    }

    protected function getAssetName($extname, $dir, $ovdir, $name_as_alias) {
        $name = 'jshopping.addon.'.$this->addon_alias.$extname;
        if ($dir.$ovdir.$name_as_alias != '1') {
            $name .= '.'.substr(md5($dir.$ovdir.$name_as_alias),0,8);
        }
        return $name;
    }

}