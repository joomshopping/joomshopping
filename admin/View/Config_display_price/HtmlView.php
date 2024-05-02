<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Config_display_price;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        ToolbarHelper::title( Text::_('JSHOP_CONFIG_DISPLAY_PRICE_LIST'), 'generic.png' );
        ToolbarHelper::custom( "back", 'arrow-left', 'arrow-left', Text::_('JSHOP_CONFIG'), false);
        ToolbarHelper::addNew();
        ToolbarHelper::deleteList();        
        parent::display($tpl);
	}
    function displayEdit($tpl=null){        
        ToolbarHelper::title( $temp=($this->row->id) ? (Text::_('JSHOP_EDIT')) : (Text::_('JSHOP_NEW')), 'generic.png' );
        ToolbarHelper::save();
        ToolbarHelper::spacer();
        ToolbarHelper::apply();
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();        
        parent::display($tpl);
    }
}