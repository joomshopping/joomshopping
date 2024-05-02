<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Deliverytimes;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        ToolbarHelper::title( Text::_('JSHOP_LIST_DELIVERY_TIMES'), 'generic.png' ); 
        ToolbarHelper::addNew();
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        HelperAdmin::btnHome();
        parent::display($tpl);
	}
    function displayEdit($tpl=null){
        ToolbarHelper::title( $temp = ($this->edit) ? (Text::_('JSHOP_DELIVERY_TIME_EDIT').' / '.$this->deliveryTimes->{\JSFactory::getLang()->get('name')}) : (Text::_('JSHOP_DELIVERY_TIME_NEW')), 'generic.png' ); 
        ToolbarHelper::save();
        ToolbarHelper::spacer();
        ToolbarHelper::apply();
        ToolbarHelper::spacer();
        ToolbarHelper::save2new();
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();        
        parent::display($tpl);
    }
}