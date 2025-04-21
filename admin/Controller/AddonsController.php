<?php
/**
* @version      5.6.1 07.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;

use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;

defined('_JEXEC') or die();

class AddonsController extends BaseadminController{
    
    public function init(){
        HelperAdmin::checkAccessController("addons");
        HelperAdmin::addSubmenu("other");
    }

    public function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.addons";
        $filter = array_filter($app->getUserStateFromRequest($context.'filter', 'filter', [], 'array'));

        $addons = JSFactory::getModel("addons");
        $rows = $addons->getList(1, $filter);
        $back64 = base64_encode("index.php?option=com_jshopping&controller=addons");

        $view = $this->getView("addons", 'html');
        $view->setLayout("list");
        $view->rows = $rows;
        $view->back64 = $back64;
        $view->config = JSFactory::getConfig();
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->filter = $filter;

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

    public function config(){
        $app = Factory::getApplication();
        $app->input->set('hidemainmenu', true);
		$id = $this->input->getInt("id");
		$row = JSFactory::getTable('addon');
		$row->load($id);
        $config = $row->getConfig();
        $def_folder_view =  'components/com_jshopping/templates/addons/'.$row->alias;
        $def_folder_js =  'components/com_jshopping/js/addons';
        $def_folder_css =  'components/com_jshopping/css/addons';

        $def_overrides_view =  'templates/{YOUR_JOOMLA_TEMPLATE}/html/com_jshopping/addons/'.$row->alias;
        $def_overrides_js =  'templates/{YOUR_JOOMLA_TEMPLATE}/js/addons';
        $def_overrides_css =  'templates/{YOUR_JOOMLA_TEMPLATE}/css/addons';

        $debug_options = [0 => Text::_('JNo'), 1 => Text::_('JYES')." L1", 2 => Text::_('JYES')." L2", 3 => Text::_('JYES')." L3"];
        $debug_select = HTMLHelper::_('select.genericlist', $debug_options, 'config[debug]','class="inputbox form-select"','id','name', $config['debug'] ?? 0);

		$view = $this->getView("addons", 'html');
        $view->setLayout("config");
        $view->row = $row;
        $view->config = $config;
        $view->def_folder_view = $def_folder_view;
        $view->def_folder_js = $def_folder_js;
        $view->def_folder_css = $def_folder_css;
        $view->def_overrides_view = $def_overrides_view;
        $view->def_overrides_js = $def_overrides_js;
        $view->def_overrides_css = $def_overrides_css;
        $view->debug_select = $debug_select;
        $app->triggerEvent('onBeforeConfigAddons', array(&$view));
		$view->displayConfig();
	}

    public function saveconfig() {
        $post = $this->input->post->getArray(array(), null, 'RAW');
	 	if (isset($post['f-id'])){
	    	$post['id'] = $post['f-id'];
        	unset($post['f-id']);
    	}
        JSFactory::getModel("addons")->saveconfig($post);
        $this->setRedirect("index.php?option=com_jshopping&controller=addons");
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