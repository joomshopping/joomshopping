<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
defined('_JEXEC') or die();

class AddonsController extends BaseadminController{
    
    function init(){
        \JSHelperAdmin::checkAccessController("addons");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $addons = \JSFactory::getModel("addons");
        $rows = $addons->getList(1);
        $back64 = base64_encode("index.php?option=com_jshopping&controller=addons");

        $view = $this->getView("addons", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows); 
        $view->set('back64', $back64);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayAddons', array(&$view));
        $view->displayList();
    }
    
    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getVar("id");
        $dispatcher = \JFactory::getApplication();
        $row = \JSFactory::getTable('addon');
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
    
    function save(){
        $this->saveConfig('save');
    }
    
    function apply(){
        $this->saveConfig();
    }
    
    private function saveConfig($task = 'apply'){
		$post = $this->input->post->getArray(array(), null, 'RAW');
	 	if (isset($post['f-id'])){
	    	$post['id'] = $post['f-id'];
        	unset($post['f-id']);
    	}
        \JSFactory::getModel("addons")->save($post);
        if ($task == 'save'){
            $this->setRedirect("index.php?option=com_jshopping&controller=addons");
        } else {
            $this->setRedirect("index.php?option=com_jshopping&controller=addons&task=edit&id=".$post['id']);
        }
    }

    function remove(){
        $id = $this->input->getVar("id");
        \JSFactory::getModel("addons")->delete($id);
        $this->setRedirect("index.php?option=com_jshopping&controller=addons");
    }
    
    function info(){
        $id = $this->input->getVar("id");
        
        $dispatcher = \JFactory::getApplication();
        $row = \JSFactory::getTable('addon');
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
    
    function version(){
        $id = $this->input->getVar("id");
        
        $dispatcher = \JFactory::getApplication();
        $row = \JSFactory::getTable('addon');
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

    function help(){
        $view = $this->getView("addons", 'html');
        $view->setLayout("help");
        $view->displayHelp();
    }

}