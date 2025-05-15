<?php
/**
* @version      5.6.3 10.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;

defined('_JEXEC') or die();

class UpdateController extends BaseadminController{
    
    function init(){
        HelperAdmin::checkAccessController("update");
        HelperAdmin::addSubmenu("update");
        $language = Factory::getLanguage(); 
        $language->load('com_installer');
    }

    function display($cachable = false, $urlparams = false){
		$view = $this->getView("update", 'html');
        $view->set('config', JSFactory::getConfig());
        $view->etemplatevar1 = '';
        $view->etemplatevar2 = '';
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->default_folder = JPATH_ROOT."/tmp/foldername";
		$view->display();
    }

	function update() {
        $installtype = $this->input->get('installtype');
        $back = $this->input->getVar('back');
        $backmain = "index.php?option=com_jshopping&controller=update";
        $ct = $this->input->getInt('ct');
        $model = JSFactory::getModel('Update');
        if ($installtype == 'package') {
            $install_path = $this->input->files->get('install_path', null, 'raw');
        } else {
            $install_path = $this->input->get('install_path', null, 'raw') ?? $this->input->get('install_url', null, 'raw');            
        }
        if (!($installtype == 'url' && preg_match('/^sm\d+:/', $install_path))) {
            $ct_type = $ct == 1 ? 'get' : 'post';
            $this->checkToken($ct_type);
        }
        try {
		    $model->install($install_path, $installtype);
        } catch (Exception $e) {
            JSError::raiseError(500, $e->getMessage());
            Helper::saveToLog("install.log", $e->getMessage());
            $this->setRedirect($backmain); 
            return 0;
        }
        if ($model->getWarnings()) {
            foreach($model->getWarnings() as $msg) {
                JSError::raiseWarning(400, $msg);
            }
        }
        if ($model->getBackUrl()) {
            $back = $model->getBackUrl();
        }
        $back = $back ?: $backmain;
        $this->setRedirect($back, Text::_('JSHOP_COMPLETED'));
    }

}