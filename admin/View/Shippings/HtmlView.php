<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Shippings;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        ToolbarHelper::title( Text::_('JSHOP_LIST_SHIPPINGS'), 'generic.png' ); 
        ToolbarHelper::addNew();
        ToolbarHelper::publishList();
        ToolbarHelper::unpublishList();
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        ToolbarHelper::custom("ext_price_calc", "cog", "cog" ,Text::_('JSHOP_SHIPPING_EXT_PRICE_CALC'), false);
        HelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        ToolbarHelper::title( $temp = ($this->edit) ? (Text::_('JSHOP_EDIT_SHIPPING').' / '.$this->shipping->{\JSFactory::getLang()->get('name')}) : (Text::_('JSHOP_NEW_SHIPPING')), 'generic.png' ); 
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