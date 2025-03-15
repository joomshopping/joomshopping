<?php
/**
* @version      5.6.0 21.02.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Dispatcher;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Controller\BaseController;

class Dispatcher extends ComponentDispatcher{
	
	public function getController(string $name, string $client = '', array $config = array()): BaseController{
        Factory::getApplication()->triggerEvent('onAfterGetJsAdminRequestController', array(&$name));
		return parent::getController($name, $client, $config);
	}
	
	public function dispatch(){
		$this->initShop();
		parent::dispatch();
	}

    private function initShop(){
        $user = Factory::getUser();
        if (!$user->authorise('core.manage', 'com_jshopping')) {
            return JSError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR'));
        }
        $app = Factory::getApplication();
        $ajax = $app->input->getInt('ajax');
        $controller = $app->input->getCmd('controller');
        $task = $app->input->getCmd('task');
        $admin_load_user_id = $app->input->getInt('admin_load_user_id');
        if ($admin_load_user_id){
            JSFactory::setLoadUserId($admin_load_user_id);
        }
        if (!$app->input->getVar("js_nolang")){
            JSFactory::loadAdminLanguageFile();
        }
        $jshopConfig = JSFactory::getConfig();
        $currLang = Factory::getLanguage()->getTag();
        $lang_tags = JSFactory::getModel("languages")->getAllTags(1);
        if (in_array($currLang, $lang_tags) && $jshopConfig->admin_shop_lang_as_admin_lang) {
            $jshopConfig->setLang($currLang);
        } else {
            $jshopConfig->setLang($jshopConfig->getFrontLang());
        }
        $checkInstall = !$ajax && !$task;
        $checkInstall = $checkInstall || ($controller=='config' && $task!='save');
		
        if ($checkInstall && $user->authorise('core.admin.install', 'com_jshopping')){
            Helper::installNewLanguages();
            JSFactory::getModel("addondependencies")->autoInstallAll();
            JSFactory::getModel("sysmsg")->show();
        }

        if ($ajax) {
            header('Content-Type: text/html;charset=UTF-8');
        }

        PluginHelper::importPlugin('jshopping');
        PluginHelper::importPlugin('jshoppingadmin');
        PluginHelper::importPlugin('jshoppingmenu');
        $app->triggerEvent('onAfterLoadShopParamsAdmin', array());

        HTMLHelper::_('bootstrap.framework');
		HTMLHelper::_('jquery.framework');
        $wa = JSFactory::getWebAssetManager();
        $wap = $jshopConfig->getWebAssetParams('script', 'com.jshopping.admin.function');
        $wa->registerAndUseScript('com.jshopping.function', $jshopConfig->file_functions_js, $wap['options'], $wap['attributes'], $wap['dependencies']);
        $wa->registerAndUseScript('com.jshopping.admin.function', $jshopConfig->live_admin_path.'js/functions.js', $wap['options'], $wap['attributes'], $wap['dependencies']);
        $wap = $jshopConfig->getWebAssetParams('style', 'com.jshopping.admin');
        $wa->registerAndUseStyle('com.jshopping.admin', $jshopConfig->live_admin_path.'css/style.css', $wap['options'], $wap['attributes'], $wap['dependencies']);
        if (version_compare(JVERSION, '5.0.0') >= 0 && version_compare(JVERSION, '5.1.0') < 0) {
            $wa->registerAndUseStyle('com.jshopping.admin.j5', $jshopConfig->live_admin_path.'css/stylej5.css', $wap['options'], $wap['attributes'], $wap['dependencies']);
        }
    }

}