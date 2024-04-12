<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Orders;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function displayList($tpl=null){        
        \JToolBarHelper::title(\JText::_('JSHOP_ORDER_LIST'), 'generic.png');
        \JToolBarHelper::addNew();
        \JToolBarHelper::deleteList(\JText::_('JSHOP_DELETE')."?");
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    function displayEdit($tpl=null){
        \JToolBarHelper::title($this->order->order_number, 'generic.png');
        \JToolBarHelper::save();
        \JToolBarHelper::apply();
        \JToolBarHelper::cancel();
        parent::display($tpl);
    }
    function displayShow($tpl=null){
        \JToolBarHelper::title($this->order->order_number, 'generic.png');
        \JToolBarHelper::back();
		\JToolBarHelper::custom('send', 'mail', 'mail', \JText::_('JSHOP_SEND_MAIL'), false);
		\JToolBarHelper::custom('edit', 'edit', 'edit', \JText::_('JSHOP_EDIT'), false);
        parent::display($tpl);
    }
    function displayTrx($tpl = null){
        \JToolBarHelper::title($this->order->order_number."/ ".\JText::_('JSHOP_TRANSACTION'), 'generic.png');
        \JToolBarHelper::back();
        parent::display($tpl);
    }
}