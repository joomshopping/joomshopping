<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.4.2 25.05.2024
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
    public $debug = 0;    
    
    public function __construct($addon_alias = ''){
		if ($addon_alias != ''){
			$this->addon_alias = $addon_alias;
		}
        if ($this->addon_alias == ''){
            throw new Exception('addon_alias empty');
        }
    }
    
    public function getAddonAlias(){
        return $this->addon_alias;
    }

    public function getAddonParams(){
        if (!$this->addon_params){
            $addon = JSFactory::getTable('addon');
            $addon->loadAlias($this->addon_alias);
            $this->addon_params = $addon->getParams();
        }
        return $this->addon_params;
    }
    
    public function getAddonId(){
        if (!$this->addon_id){
            $addon = JSFactory::getTable('addon');
            $addon->loadAlias($this->addon_alias);
            $this->addon_id = $addon->id;
        }
        return $this->addon_id;
    }

	public function getView($layout = '', $dir = null, $ovdir = null){
        $dir = $dir ?? 'components/com_jshopping/templates/addons/'.$this->addon_alias;
		$template = $this->getJoomlaTemplate();
		$ovdir = $ovdir ?? 'templates/'.$template.'/html/com_jshopping/addons/'.$this->addon_alias;
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

    public function loadLanguage(){
        JSFactory::loadExtLanguageFile($this->addon_alias);
    }
    
    public function loadCss($extname = '', $dir = null, $ovdir = null){
        $document = Factory::getDocument();
        $template = $this->getJoomlaTemplate();
        $dir = $dir ?? 'components/com_jshopping/css/addons';
        $ovdir = $ovdir ?? 'templates/'.$template.'/css/addons';
        $filename = $this->addon_alias.$extname.'.css';
        if ($this->debug) {
            print '<pre>';
            print '#Addon loadCss: '.$filename."\n";
            print '#Addon loadCss dir: '.$dir."\n";
            print '#Addon loadCss ovdir: '.$ovdir."\n";
            print '</pre>';
        }
        if (file_exists(JPATH_ROOT.'/'.$ovdir.'/'.$filename)) {
            $dir = $ovdir;
        }
        $document->addStyleSheet(Uri::root().$dir.'/'.$filename);
    }
    
    public function loadJs($extname = '', $dir = null, $ovdir = null){
        $document = Factory::getDocument();
        $template = $this->getJoomlaTemplate();
        $dir = $dir ?? 'components/com_jshopping/js/addons';
        $ovdir = $ovdir ?? 'templates/'.$template.'/js/addons';
        $filename = $this->addon_alias.$extname.'.js';
        if ($this->debug) {
            print '<pre>';
            print '#Addon loadJs: '.$filename."\n";
            print '#Addon loadJs dir: '.$dir."\n";
            print '#Addon loadJs ovdir: '.$ovdir."\n";
            print '</pre>';
        }
        if (file_exists(JPATH_ROOT.'/'.$ovdir.'/'.$filename)) {
            $dir = $ovdir;
        }
        $document->addScript(Uri::root().$dir.'/'.$filename);        
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

}