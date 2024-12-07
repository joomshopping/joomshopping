<?php
/**
* @version      5.0.8 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Orders;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function displayList($tpl=null){        
        ToolbarHelper::title(Text::_('JSHOP_ORDER_LIST'), 'generic.png');
        ToolbarHelper::addNew();
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE')."?");
        HelperAdmin::btnHome();
        parent::display($tpl);
	}
    function displayEdit($tpl=null){
        ToolbarHelper::title($this->order->order_id ? Text::_('JSHOP_EDIT').' '.$this->order->order_number : Text::_('JSHOP_NEW'), 'generic.png');
        ToolbarHelper::save();
        ToolbarHelper::apply();
        ToolbarHelper::cancel();
        parent::display($tpl);
    }
    function displayShow($tpl=null){
        ToolbarHelper::title($this->order->order_number, 'generic.png');
        ToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_jshopping&controller=orders');
		ToolbarHelper::custom('send', 'mail', 'mail', Text::_('JSHOP_SEND_MAIL'), false);
		ToolbarHelper::custom('edit', 'edit', 'edit', Text::_('JSHOP_EDIT'), false);
        parent::display($tpl);
    }
    function displayTrx($tpl = null){
        ToolbarHelper::title($this->order->order_number."/ ".Text::_('JSHOP_TRANSACTION'), 'generic.png');
        ToolbarHelper::back();
        parent::display($tpl);
    }
}