<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.6.0 08.03.2025
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
    protected $table;
    public $debug = 0;

    public function __construct($addon_alias = ''){
		if ($addon_alias != ''){
			$this->addon_alias = $addon_alias;
		}
        if ($this->addon_alias == ''){
            throw new Exception('addon_alias empty');
        }
        $this->table = JSFactory::getTable('addon');
        $this->table->loadAlias($this->addon_alias);

        if (JSFactory::getConfig()->shop_mode == 1) {
            $config = $this->table->getConfig();
            if (isset($config['debug']) && $config['debug']) {
                $this->debug = $config['debug'];
            }
        }
    }
    
    public function getAddonAlias(){
        return $this->addon_alias;
    }

    public function getAddonParams(){        
        $this->addon_params = $this->table->getParams();
        return $this->addon_params;
    }

    public function getAddonConfig(){
        return $this->table->getConfig();
    }

    public function getTable(){
        return $this->table;
    }
    
    public function getAddonId(){
        if (!$this->addon_id){
            $this->addon_id = $this->table->id;
        }
        return $this->addon_id;
    }

	public function getView($layout = '', $dir = null, $ovdir = null){
        $dir = $dir ?? 'components/com_jshopping/templates/addons/'.$this->addon_alias;
		$template = $this->getJoomlaTemplate();
        $config = $this->table->getConfig();
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
            print '#Addon getView: '.$layout."\n";
            print '#Addon getView dir: '.$dir."\n";
            print '#Addon getView ovdir: '.$ovdir."\n";
            print '</pre>';
        }
        return $view;
    }

    public function loadLanguage($langtag = ""){
        JSFactory::loadExtLanguageFile($this->addon_alias, $langtag);
    }
    
    public function loadCss($extname = '', $dir = null, $ovdir = null, $name_as_alias = 1, $wap = null){
        $template = $this->getJoomlaTemplate();
        $asset_name = $this->getAssetName($extname, $dir, $ovdir, $name_as_alias);
        $config = $this->table->getConfig();
        $dir = $dir ?? 'components/com_jshopping/css/addons';
        $ovdir = $ovdir ?? $config['folder_overrides_css'] ?? 'templates/'.$template.'/css/addons';
        if ($name_as_alias) {
            $filename = $this->addon_alias.$extname.'.css';
        } else {
            $filename = $extname.'.css';
        }
        if ($this->debug) {
            print '<pre>';
            print '#Addon loadCss: '.$filename."\n";
            print '#Addon loadCss dir: '.$dir."\n";
            print '#Addon loadCss ovdir: '.$ovdir."\n";
            print '#Addon loadCss asset_name: '.$asset_name."\n";
            print '</pre>';
        }
        if (file_exists(JPATH_ROOT.'/'.$ovdir.'/'.$filename)) {
            $dir = $ovdir;
        }
        $wa = JSFactory::getWebAssetManager();
        $wap = $wap ?? JSFactory::getConfig()->getWebAssetParams('style', $asset_name);
        $wa->registerAndUseStyle($asset_name, Uri::root().$dir.'/'.$filename, $wap['options'], $wap['attributes'], $wap['dependencies']);
    }
    
    public function loadJs($extname = '', $dir = null, $ovdir = null, $name_as_alias = 1, $wap = null){
        $template = $this->getJoomlaTemplate();
        $asset_name = $this->getAssetName($extname, $dir, $ovdir, $name_as_alias);
        $config = $this->table->getConfig();
        $dir = $dir ?? 'components/com_jshopping/js/addons';
        $ovdir = $ovdir ?? $config['folder_overrides_js'] ?? 'templates/'.$template.'/js/addons';
        if ($name_as_alias) {
            $filename = $this->addon_alias.$extname.'.js';
        } else {
            $filename = $extname.'.js';
        }
        if ($this->debug) {
            print '<pre>';
            print '#Addon loadJs: '.$filename."\n";
            print '#Addon loadJs dir: '.$dir."\n";
            print '#Addon loadJs ovdir: '.$ovdir."\n";
            print '#Addon loadJs asset_name: '.$asset_name."\n";
            print '</pre>';
        }
        if (file_exists(JPATH_ROOT.'/'.$ovdir.'/'.$filename)) {
            $dir = $ovdir;
        }
        $wa = JSFactory::getWebAssetManager();
        $wap = $wap ?? JSFactory::getConfig()->getWebAssetParams('script', $asset_name);
        $wa->registerAndUseScript($asset_name, Uri::root().$dir.'/'.$filename, $wap['options'], $wap['attributes'], $wap['dependencies']);
    }
    
    public function getPathImages(){
        return Uri::root().'components/com_jshopping/images/'.$this->addon_alias;
    }
    
    public function checkLicKey(){
		return Helper::compareX64(Helper::replaceWWW(Helper::getJHost().$this->addon_alias), Helper::getLicenseKeyAddon($this->addon_alias));
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