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

        $dir = new ReadDir($jshopConfig->image_product_path);
        $files = $dir->getFiles($filter, 'thumb_');		
        $view = $this->getView("productimages", 'html');
        $view->setLayout("list");
        $view->set('list', $files);
        $view->set('config', $jshopConfig);
        $view->set('filter', $filter);
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


}