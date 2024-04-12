<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
defined('_JEXEC') or die();

class ContentController extends BaseController{
    
    public function init(){
        \JPluginHelper::importPlugin('content');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshoppingControllerContent', array(&$obj));
    }
    
    function display($cachable = false, $urlparams = false){
        \JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
    }

    function view(){        
		$model = \JSFactory::getModel('contentPage', 'Site');

        $page = $this->input->getVar('page');
		$order_id = $this->input->getInt('order_id');
        $cartp = $this->input->getInt('cart');
        
		$seodata = Metadata::content($page);
		$model->setSeodata($seodata);
        
        $text = $model->load($page, $order_id, $cartp);
		if ($text===false){
			\JSError::raiseError(404, $model->getError());
			return 0;
		}		

        $view = $this->getView("content");
        $view->setLayout("content");
        $view->set('text', $text);
        \JFactory::getApplication()->triggerEvent('onBeforeDisplayContentView', array(&$view));
        $view->display();
    }
}