<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
defined('_JEXEC') or die();

class ContentController extends BaseController{
    
    public function init(){
        PluginHelper::importPlugin('content');
        $obj = $this;
        Factory::getApplication()->triggerEvent('onConstructJshoppingControllerContent', array(&$obj));
    }
    
    function display($cachable = false, $urlparams = false){
        throw new \Exception(Text::_('JSHOP_PAGE_NOT_FOUND'), 404);
    }

    function view(){        
		$model = JSFactory::getModel('contentPage', 'Site');

        $page = $this->input->getVar('page');
		$order_id = $this->input->getInt('order_id');
        $cartp = $this->input->getInt('cart');
        
		$seodata = Metadata::content($page);
		$model->setSeodata($seodata);
        
        $text = $model->load($page, $order_id, $cartp);
		if ($text===false){
			JSError::raiseError(404, $model->getError());
			return 0;
		}		

        $view = $this->getView("content");
        $view->setLayout("content");
        $view->set('text', $text);
        Factory::getApplication()->triggerEvent('onBeforeDisplayContentView', array(&$view));
        $view->display();
    }
}