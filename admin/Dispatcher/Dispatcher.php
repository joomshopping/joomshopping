<?php
/**
* @version      5.5.1 21.02.2024
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
        if (!Factory::getUser()->authorise('core.manage', 'com_jshopping')) {
            return JSError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR'));
        }		
        $app = Factory::getApplication();
        $ajax = $app->input->getInt('ajax');
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
		
        if (!$ajax){
            Helper::installNewLanguages();
        }else{
            header('Content-Type: text/html;charset=UTF-8');
        }

        PluginHelper::importPlugin('jshopping');
        PluginHelper::importPlugin('jshoppingadmin');
        PluginHelper::importPlugin('jshoppingmenu');
        $app->triggerEvent('onAfterLoadShopParamsAdmin', array());

        HTMLHelper::_('bootstrap.framework');
		HTMLHelper::_('jquery.framework');
        $doc = Factory::getDocument();
        $doc->addScript($jshopConfig->live_path.'js/functions.js');
        $doc->addScript($jshopConfig->live_admin_path.'js/functions.js?5.3.3');
        $doc->addStyleSheet($jshopConfig->live_admin_path.'css/style.css');       
        if (version_compare(JVERSION, '5.0.0') >= 0 && version_compare(JVERSION, '5.1.0') < 0) {
            $doc->addStyleSheet($jshopConfig->live_admin_path.'css/stylej5.css');
        }
    }

}