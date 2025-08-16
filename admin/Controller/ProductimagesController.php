<?php
/**
* @version      5.8.0 09.06.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\ReadDir;

defined( '_JEXEC' ) or die();

class ProductimagesController extends BaseadminController{
    
    public function display($cachable = false, $urlparams = false){
		$jshopConfig = JSFactory::getConfig();
		$filter = $this->input->getVar('filter', '');
        $action = "index.php?option=com_jshopping&controller=productimages&task=display&tmpl=component";
        $img_live_path = $jshopConfig->image_product_live_path . '/thumb_';

        $dir = new ReadDir($jshopConfig->image_product_path);
        $files = $dir->getFiles($filter, 'thumb_');		
        $view = $this->getView("productimages", 'html');
        $view->setLayout("list");
        $view->set('list', $files);
        $view->set('config', $jshopConfig);
        $view->set('filter', $filter);
        $view->set('action', $action);
        $view->set('img_live_path', $img_live_path);
        Factory::getApplication()->triggerEvent('onBeforeDisplayProductsImages', array(&$view));
		$view->displayList();
	}

    public function videos(){
        $jshopConfig = JSFactory::getConfig();
		$filter = $this->input->getVar('filter', '');

        $dir = new ReadDir($jshopConfig->video_product_path);
        $files = $dir->getFiles($filter);
        $view = $this->getView("productimages", 'html');
        $view->setLayout("videos");
        $view->set('list', $files);
        $view->set('config', $jshopConfig);
        $view->set('filter', $filter);
        Factory::getApplication()->triggerEvent('onBeforeDisplayProductimagesDirVideo', array(&$view));
		$view->displayList();
    }

    public function demofiles(){
        $jshopConfig = JSFactory::getConfig();
		$filter = $this->input->getVar('filter', '');

        $dir = new ReadDir($jshopConfig->demo_product_path);
        $files = $dir->getFiles($filter);
        $view = $this->getView("productimages", 'html');
        $view->setLayout("demofiles");
        $view->set('list', $files);
        $view->set('config', $jshopConfig);
        $view->set('filter', $filter);
        Factory::getApplication()->triggerEvent('onBeforeDisplayProductimagesDirDemo', array(&$view));
		$view->displayList();
    }

    public function salefiles(){
        $jshopConfig = JSFactory::getConfig();
		$filter = $this->input->getVar('filter', '');

        $dir = new ReadDir($jshopConfig->files_product_path);
        $dir->setSkipFiles(['.htaccess']);
        $files = $dir->getFiles($filter);
        $view = $this->getView("productimages", 'html');
        $view->setLayout("salefiles");
        $view->set('list', $files);
        $view->set('config', $jshopConfig);
        $view->set('filter', $filter);
        Factory::getApplication()->triggerEvent('onBeforeDisplayProductimagesDirFile', array(&$view));
		$view->displayList();
    }

    public function attributes(){
		$jshopConfig = JSFactory::getConfig();
		$filter = $this->input->getVar('filter', '');
        $action = "index.php?option=com_jshopping&controller=productimages&task=attributes&tmpl=component";
        $img_live_path = $jshopConfig->image_attributes_live_path .'/';

        $dir = new ReadDir($jshopConfig->image_attributes_path);
        $files = $dir->getFiles($filter);		
        $view = $this->getView("productimages", 'html');
        $view->setLayout("list");
        $view->set('list', $files);
        $view->set('config', $jshopConfig);
        $view->set('filter', $filter);
        $view->set('action', $action);
        $view->set('img_live_path', $img_live_path);
        Factory::getApplication()->triggerEvent('onBeforeDisplayProductsImages', array(&$view));
		$view->displayList();
	}


}