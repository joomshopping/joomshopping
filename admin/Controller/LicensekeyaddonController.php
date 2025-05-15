<?php
/**
* @version      5.7.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

defined('_JEXEC') or die();

class LicenseKeyAddonController extends BaseadminController{
    
    function init(){
        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        HelperAdmin::checkAccessController("licensekeyaddon");
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
        $addon = JSFactory::getTable('addon');
        $post = $this->input->post->getArray();
		if (isset($post['f-id'])){
            $post['id'] = $post['f-id'];
            unset($post['f-id']);
        }
		$addon->bind($post);
		if (!$addon->store()) {
			JSError::raiseWarning("",Text::_('JSHOP_ERROR_SAVE_DATABASE'));
			$this->setRedirect("index.php?option=com_jshopping");
			return 0;
		}
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onAfterSaveLicenseKeyAddons', array(&$addon));
        $this->setRedirect(base64_decode($post['back']));
	}
    
    function cancel(){
        $post = $this->input->post->getArray();
        $this->setRedirect(base64_decode($post['back']));
    }
}