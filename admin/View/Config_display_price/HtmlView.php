<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Config_display_price;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_CONFIG_DISPLAY_PRICE_LIST'), 'generic.png' );
        \JToolBarHelper::custom( "back", 'arrow-left', 'arrow-left', \JText::_('JSHOP_CONFIG'), false);
        \JToolBarHelper::addNew();
        \JToolBarHelper::deleteList();        
        parent::display($tpl);
	}
    function displayEdit($tpl=null){        
        \JToolBarHelper::title( $temp=($this->row->id) ? (\JText::_('JSHOP_EDIT')) : (\JText::_('JSHOP_NEW')), 'generic.png' );
        \JToolBarHelper::save();
        \JToolBarHelper::spacer();
        \JToolBarHelper::apply();
        \JToolBarHelper::spacer();
        \JToolBarHelper::cancel();        
        parent::display($tpl);
    }
}