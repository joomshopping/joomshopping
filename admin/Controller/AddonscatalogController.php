<?php
/**
* @version      5.10.0 17.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;

use Exception;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;

defined('_JEXEC') or die();

class AddonscatalogController extends BaseadminController{
    
    public function init(){
        HelperAdmin::checkAccessController("addonscatalog");
        HelperAdmin::addSubmenu("other");
    }

    public function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshopping.list.admin.addonscatalog";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		$category_id = $app->getUserStateFromRequest($context.'category_id', 'category_id', 0, 'int');
        $type = $app->getUserStateFromRequest($context.'type', 'type', 0, 'int');
		$text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');

        $model = JSFactory::getModel('addonscatalog');
		
		$filter = [];
		if ($category_id > 0) {
		    $filter['category_id'] = $category_id;
        }
        if ($type > 0) {
		    $filter['type'] = $type;
        }
        if (trim($text_search)) {
		    $filter['text_search'] = $text_search;
        }
        $domain = Helper::getClearHost();
        $back_url = 'index.php?option=com_jshopping&controller=addonscatalog';

		$total = $model->getListCount($filter, $domain);
		$pageNav = new Pagination($total, $limitstart, $limit);
        $rows = $model->getList($filter, $filter_order, $filter_order_Dir, $pageNav->limitstart, $pageNav->limit, $domain, $back_url);
        $cats = $model->getListCategory();
        $types = $model->getListTypes();

        $view = $this->getView("addonscatalog", 'html');
        $view->setLayout("list");
        $view->rows = $rows;
        $view->cats = $cats;
        $view->types = $types;
        $view->filter_order = $filter_order;
        $view->filter_order_Dir = $filter_order_Dir;
        $view->filter = $filter;
		$view->pageNav = $pageNav;
        $view->config = JSFactory::getConfig();
        $view->displayList();
    }

    public function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=addonscatalog");
    }

    public function refresh(){
        $model = JSFactory::getModel('addonscatalog');
        $model->refresh();
        $this->setRedirect("index.php?option=com_jshopping&controller=addonscatalog");
    }

    public function apikey() {
        HelperAdmin::checkConfigDisable("apikey_edit_disable", "index.php?option=com_jshopping&controller=addonscatalog");
        $view = $this->getView("addonscatalog", 'html');
        $view->setLayout("apikey");
        $view->key = JSFactory::getConfig()->addonshop_api_key;
        $view->displayApikey();
    }

    public function apikeysave() {
        HelperAdmin::checkConfigDisable("apikey_edit_disable", "index.php?option=com_jshopping&controller=addonscatalog");
        $jshopConfig = JSFactory::getConfig();
        $config = JSFactory::getTable('Config');
		$config->id = $jshopConfig->load_id;
        $config->addonshop_api_key = $this->input->getString('key');
        $config->store();
        $this->setRedirect('index.php?option=com_jshopping&controller=addonscatalog');
    }

    public function downloadaddon() {
        $this->checkToken('get');
        HelperAdmin::checkConfigDisable("downloadaddon_disable", "index.php?option=com_jshopping&controller=addonscatalog");
        $app = Factory::getApplication();
        $id = $this->input->getInt('wid');
        $model = JSFactory::getModel('addonscatalog');
        try {
            $file = $model->downloadAddon($id);
        } catch (Exception $e) {
            $app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect("index.php?option=com_jshopping&controller=addonscatalog");
            return 0;
        }
        $name = $model->getDownloadFileName($id);
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/zip');
        header('Content-Length: '.filesize($file));
        header('Content-Disposition: attachment; filename="'.$name.'"');
        header('Cache-Control: no-store');
        readfile($file);
        @unlink($file);
        $app->close();
    }

    public function installaddon() {
        $this->checkToken('get');
        HelperAdmin::checkAccessController("update");
        HelperAdmin::checkConfigDisable("installaddon_disable", "index.php?option=com_jshopping&controller=addonscatalog");
        $app = Factory::getApplication();
        $id = $this->input->getInt('wid');
        $back = $this->input->getVar('back');
        if (!$back || !Uri::isInternal($back)) {
            $back = "index.php?option=com_jshopping&controller=addonscatalog";
        }
        $model = JSFactory::getModel('addonscatalog');
        try {
            $update = $model->installAddon($id);
        } catch (Exception $e) {
            JSError::raiseError(500, $e->getMessage());
            Helper::saveToLog("install.log", $e->getMessage());
            $this->setRedirect($back);
            return 0;
        }
        foreach($update->getWarnings() as $msg) {
            JSError::raiseWarning(400, $msg);
        }
        if ($update->getBackUrl()) {
            $back = $update->getBackUrl();
        }
        $this->setRedirect($back, Text::_('JSHOP_COMPLETED'));
    }

    public function getaddonkey(){
        HelperAdmin::checkConfigDisable("addonkey_edit_disable", "index.php?option=com_jshopping&controller=addonscatalog");
        $alias = $this->input->getString('alias');
        $domain = Helper::getClearHost();
        $model = JSFactory::getModel('addonscatalog');
        echo $model->getAddonKey($alias, $domain);
        die();
    }

}