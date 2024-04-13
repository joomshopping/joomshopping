<?php
/**
* @version      5.4.0 07.04.2024
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

defined('_JEXEC') or die();

class AddonsController extends BaseadminController{
    
    public function init(){
        HelperAdmin::checkAccessController("addons");
        HelperAdmin::addSubmenu("other");
    }

    public function display($cachable = false, $urlparams = false){
        $addons = JSFactory::getModel("addons");
        $rows = $addons->getList(1);
        $back64 = base64_encode("index.php?option=com_jshopping&controller=addons");

        $view = $this->getView("addons", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows); 
        $view->set('back64', $back64);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayAddons', array(&$view));
        $view->displayList();
    }
    
    public function edit(){
        $this->input->set('hidemainmenu', true);
        $id = $this->input->getVar("id");
        $dispatcher = Factory::getApplication();
        $row = JSFactory::getTable('addon');
        $row->load($id);
        $config_file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/config.tmpl.php";
        $config_file_exist = file_exists($config_file_patch);

        $view = $this->getView("addons", 'html');
        $view->setLayout("edit");
        $view->set('row', $row);
        $view->set('params', $row->getParams());
        $view->set('config_file_patch', $config_file_patch);
        $view->set('config_file_exist', $config_file_exist);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher->triggerEvent('onBeforeEditAddons', array(&$view));
        $view->displayEdit();
    }
    
    public function save(){
		$post = $this->input->post->getArray(array(), null, 'RAW');
	 	if (isset($post['f-id'])){
	    	$post['id'] = $post['f-id'];
        	unset($post['f-id']);
    	}
        JSFactory::getModel("addons")->save($post);
        if ($this->getTask()=='apply') {
            $this->setRedirect("index.php?option=com_jshopping&controller=addons&task=edit&id=".$post['id']);            
        } else {
            $this->setRedirect("index.php?option=com_jshopping&controller=addons");
        }
    }

    public function remove(){
        $id = $this->input->getVar("id");
        JSFactory::getModel("addons")->delete($id);
        $this->setRedirect("index.php?option=com_jshopping&controller=addons");
    }
    
    public function info(){
        $id = $this->input->getVar("id");
        
        $dispatcher = Factory::getApplication();
        $row = JSFactory::getTable('addon');
        $row->load($id);
        $file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/info.tmpl.php";
        $file_exist = file_exists($file_patch);

        $view = $this->getView("addons", 'html');
        $view->setLayout("info");
        $view->set('row', $row);
        $view->set('params', $row->getParams());
        $view->set('file_patch', $file_patch);
        $view->set('file_exist', $file_exist);
        $dispatcher->triggerEvent('onBeforeInfoAddons', array(&$view));
        $view->displayInfo();
    }
    
    public function version(){
        $id = $this->input->getVar("id");
        
        $dispatcher = Factory::getApplication();
        $row = JSFactory::getTable('addon');
        $row->load($id);
        $file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/version.tmpl.php";
        $file_exist = file_exists($file_patch);

        $view = $this->getView("addons", 'html');
        $view->setLayout("info");
        $view->set('row', $row);
        $view->set('params', $row->getParams());
        $view->set('file_patch', $file_patch);
        $view->set('file_exist', $file_exist);
        $dispatcher->triggerEvent('onBeforeVersionAddons', array(&$view));
        $view->displayVersion();
    }

    public function help(){
        $view = $this->getView("addons", 'html');
        $view->setLayout("help");
        $view->displayHelp();
    }

    public function listweb(){
        $app = Factory::getApplication();
        $context = "jshopping.list.admin.addons";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		$category_id = $app->getUserStateFromRequest($context.'category_id', 'category_id', 0, 'int');
        $type = $app->getUserStateFromRequest($context.'type', 'type', 0, 'int');
		$text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
		
        $model = JSFactory::getModel('addons');
		
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
		$total = $model->getListWebCount($filter);
		$pageNav = new Pagination($total, $limitstart, $limit);
        $rows = $model->getListWeb($filter, $filter_order, $filter_order_Dir, $pageNav->limitstart, $pageNav->limit);        
        $cats = $model->getListWebCategory();
        $types = $model->getListWebTypes();

        $view = $this->getView("addons", 'html');
        $view->setLayout("listweb");
        $view->rows = $rows;
        $view->cats = $cats;
        $view->types = $types;
        $view->filter_order = $filter_order;
        $view->filter_order_Dir = $filter_order_Dir;
        $view->filter = $filter;
		$view->pageNav = $pageNav;
        $view->displayListWeb();
    }

    public function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=addons");
    }

    public function listwebrefresh(){
        $model = JSFactory::getModel('addons');
        $model->listWebRefresh();
        $this->setRedirect("index.php?option=com_jshopping&controller=addons&task=listweb");
    }

}