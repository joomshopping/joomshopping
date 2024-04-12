<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Panel;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    function displayHome($tpl=null){        
        \JToolBarHelper::title(\JText::_("JoomShopping"), 'generic.png' );
        parent::display($tpl);
	}
    function displayInfo($tpl=null){
        \JToolBarHelper::title( \JText::_('JSHOP_ABOUT_AS'), 'generic.png' );
        parent::display($tpl);
    }
    function displayConfig($tpl=null){
        \JToolBarHelper::title( \JText::_('JSHOP_CONFIG'), 'generic.png' );
        \JSHelperAdmin::btnHome();
        if (\JFactory::getUser()->authorise('core.admin')){
            \JToolBarHelper::preferences('com_jshopping');
        }
        parent::display($tpl);
    }
    function displayOptions($tpl=null){
        \JSHelperAdmin::btnHome();
        \JToolBarHelper::title( \JText::_('JSHOP_OTHER_ELEMENTS'), 'generic.png' );
        parent::display($tpl);
    }
}