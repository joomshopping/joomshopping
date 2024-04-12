<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\taxes_ext;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_LIST_TAXES_EXT'), 'generic.png' );
        \JToolBarHelper::custom( "back", 'folder', 'folder', \JText::_('JSHOP_LIST_TAXES'), false);
        \JToolBarHelper::addNew();
        \JToolBarHelper::deleteList(\JText::_('JSHOP_DELETE')."?");
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        \JToolBarHelper::title( $temp=($this->tax->id) ? (\JText::_('JSHOP_EDIT_TAX_EXT')) : (\JText::_('JSHOP_NEW_TAX_EXT')), 'generic.png' ); 
        \JToolBarHelper::save();
        \JToolBarHelper::apply();
        \JToolBarHelper::cancel();
        parent::display($tpl);
    }
}