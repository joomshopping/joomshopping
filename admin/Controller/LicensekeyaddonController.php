<?php
/**
* @version      5.10.0 26.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

defined('_JEXEC') or die();

class LicenseKeyAddonController extends BaseadminController{
    
    function init(){
        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        HelperAdmin::checkAccessController("licensekeyaddon");
        HelperAdmin::checkConfigDisable("addonkey_edit_disable", "index.php?option=com_jshopping&controller=addons");
        HelperAdmin::addSubmenu("other");        
    }

	function display($cachable = false, $urlparams = false){
        $alias = $this->input->getVar("alias");
		$back = $this->input->getVar("back");
		$addon = JSFactory::getTable('addon');
		$addon->loadAlias($alias);
        
        $domain = Helper::getClearHost();
        $mypaid_addons = JSFactory::getModel('addonscatalog')->getListMyPaidAddonsAlias($domain);

		$view = $this->getView("addonkey", 'html');
        $view->row = $addon;
        $view->back = $back;
        $view->btn_genrate = in_array($alias, $mypaid_addons);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayLicenseKeyAddons', array(&$view));
		$view->display();
	}
	
	function save(){
        $this->checkToken();
        $addon = JSFactory::getTable('addon');
        $post = $this->input->post->getArray();
        // only the key is changed, the other fields of the addon are not taken from the form
        $id = (int)($post['f-id'] ?? 0);
        if ($id){
            $addon->load($id);
        }else{
            $addon->loadAlias((string)($post['alias'] ?? ''));
        }
        if (!$addon->id && $addon->alias == ''){
            JSError::raiseWarning("",Text::_('JSHOP_ERROR_SAVE_DATABASE'));
            $this->setRedirect("index.php?option=com_jshopping");
            return 0;
        }
        $addon->key = (string)($post['key'] ?? '');
		if (!$addon->store()) {
			JSError::raiseWarning("",Text::_('JSHOP_ERROR_SAVE_DATABASE'));
			$this->setRedirect("index.php?option=com_jshopping");
			return 0;
		}
        // keys of addons bundled in this addon (package / template)
        $addon->load($addon->id);
        $addon->syncBundledKeys();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onAfterSaveLicenseKeyAddons', array(&$addon));
        $this->setRedirect($this->getBackUrl($post['back'] ?? ''));
	}

    function cancel(){
        $post = $this->input->post->getArray();
        $this->setRedirect($this->getBackUrl($post['back'] ?? ''));
    }

    protected function getBackUrl($back){
        $url = base64_decode((string)$back);
        if (!$url || !Uri::isInternal($url)){
            $url = "index.php?option=com_jshopping&controller=addons";
        }
        return $url;
    }
}