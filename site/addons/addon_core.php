<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.3.5 07.03.2024
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

	public function getView($layout = ''){
        $path = JPATH_ROOT.'/components/com_jshopping';
        $addon_tmpl_path = $path."/templates/addons/".$this->addon_alias;
		$template = Factory::getApplication()->getTemplate(true);
		$joomla_tmpl_path = JPATH_ROOT.'/templates/'.$template->template.'/html/com_jshopping/addons/'.$this->addon_alias;
		$view_config = array("template_path"=>$addon_tmpl_path);
        $view = new Joomla\Component\Jshopping\Site\View\Addons\HtmlView($view_config);
		$view->addTemplatePath($joomla_tmpl_path);
        if ($layout){
            $view->setLayout($layout);
        }
        $view->set('addon_path_images', $this->getPathImages());
        return $view;
    }

    public function loadLanguage(){
        JSFactory::loadExtLanguageFile($this->addon_alias);
    }
    
    public function loadCss($extname = ''){
        $document = Factory::getDocument();
        $document->addStyleSheet(Uri::root().'components/com_jshopping/css/addons/'.$this->addon_alias.$extname.'.css');
    }
    
    public function loadJs($extname = ''){
        $document = Factory::getDocument();
        $document->addScript(Uri::root().'components/com_jshopping/js/addons/'.$this->addon_alias.$extname.'.js');
    }
    
    public function getPathImages(){
        return Uri::root().'components/com_jshopping/images/'.$this->addon_alias;
    }
    
    public function checkLicKey(){
		return Helper::compareX64(Helper::replaceWWW(Helper::getJHost().$this->addon_alias), Helper::getLicenseKeyAddon($this->addon_alias));
	}

}