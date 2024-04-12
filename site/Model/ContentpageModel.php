<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class ContentPageModel  extends BaseModel{
	
	private $seodata;
	
	public function setSeodata($seodata){
		$this->seodata = $seodata;
	}
	
	public function load($page, $order_id = 0, $cartp = 0){
		$jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();		
		$statictext = \JSFactory::getTable("statictext");
        
        if ($jshopConfig->return_policy_for_product && $page=='return_policy' && ($cartp || $order_id)){
            if ($cartp){
                $cart = \JSFactory::getModel('cart', 'Site');
                $cart->load();
                $list = $cart->getReturnPolicy();
            }else{
                $order = \JSFactory::getTable('order');
                $order->load($order_id);
                $list = $order->getReturnPolicy();
            }
            $listtext = array();
            foreach($list as $v){
                $listtext[] = $v->text;
            }
            $row = new \stdClass();
            $row->id = -1;
            $row->text = implode('<div class="return_policy_space"></div>', $listtext);
        }else{
            $row = $statictext->loadData($page);
        }
                
        if (!$row->id){
			$this->setError(\JText::_('JSHOP_PAGE_NOT_FOUND'));           
            return false;
        }		
		if ($jshopConfig->use_plugin_content){
            $obj = new \stdClass();
            $params = \JFactory::getApplication()->getParams('com_content');
            $obj->text = $row->text;
            $obj->title = $this->seodata->title;
            $dispatcher->triggerEvent('onContentPrepare', array('com_content.article', &$obj, &$params, 0));
            $row->text = $obj->text;
        }
        $text = $row->text;
        $dispatcher->triggerEvent('onBeforeDisplayContent', array($page, &$text));
		return $text;
	}
	
}