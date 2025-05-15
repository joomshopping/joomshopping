<?php
/**
* @version      5.7.0 10.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;

use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Helper\Helper;

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
        $view = $this->getView("addonscatalog", 'html');
        $view->setLayout("apikey");
        $view->key = JSFactory::getConfig()->addonshop_api_key;
        $view->displayApikey();
    }

    public function apikeysave() {
        $jshopConfig = JSFactory::getConfig();
        $config = JSFactory::getTable('Config');
		$config->id = $jshopConfig->load_id;
        $config->addonshop_api_key = $this->input->getString('key');
        $config->store();
        $this->setRedirect('index.php?option=com_jshopping&controller=addonscatalog');
    }

    public function downloadaddon() {
        $app = Factory::getApplication();
        $id = $this->input->getInt('wid');
        $domain = Helper::getClearHost();
        $model = JSFactory::getModel('addonscatalog');
        $data = $model->getAddonData($id);
        $url = $model->getDownloadUrlAddon($data, $domain);        
        if ($url) {
            $app->redirect($url);
        } else {
            $app->enqueueMessage('Error download Url', 'error');
            $this->setRedirect("index.php?option=com_jshopping&controller=addonscatalog");
        }
    }

    public function installaddon() {
        $app = Factory::getApplication();
        $id = $this->input->getInt('wid');
        $back = $this->input->getVar('back');
        $domain = Helper::getClearHost();
        $model = JSFactory::getModel('addonscatalog');
        $data = $model->getAddonData($id);
        $url = $model->getInstallUrlAddon($data, $domain, $back);
        if ($url) {
            $this->setRedirect($url);
        } else {
            $app->enqueueMessage('Error install Url', 'error');
            $this->setRedirect($back);
        }
    }

    public function getaddonkey(){
        $alias = $this->input->getString('alias');
        $domain = Helper::getClearHost();
        $model = JSFactory::getModel('addonscatalog');
        echo $model->getAddonKey($alias, $domain);
        die();
    }

}