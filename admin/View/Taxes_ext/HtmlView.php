<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\taxes_ext;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        ToolbarHelper::title( Text::_('JSHOP_LIST_TAXES_EXT'), 'generic.png' );
        ToolbarHelper::custom( "back", 'folder', 'folder', Text::_('JSHOP_LIST_TAXES'), false);
        ToolbarHelper::addNew();
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE')."?");
        HelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        ToolbarHelper::title( $temp=($this->tax->id) ? (Text::_('JSHOP_EDIT_TAX_EXT')) : (Text::_('JSHOP_NEW_TAX_EXT')), 'generic.png' ); 
        ToolbarHelper::save();
        ToolbarHelper::apply();
        ToolbarHelper::cancel();
        parent::display($tpl);
    }
}