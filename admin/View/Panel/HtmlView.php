<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Panel;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    function displayHome($tpl=null){        
        ToolbarHelper::title(Text::_("JoomShopping"), 'generic.png' );
        parent::display($tpl);
	}
    function displayInfo($tpl=null){
        ToolbarHelper::title( Text::_('JSHOP_ABOUT_AS'), 'generic.png' );
        parent::display($tpl);
    }
    function displayConfig($tpl=null){
        ToolbarHelper::title( Text::_('JSHOP_CONFIG'), 'generic.png' );
        HelperAdmin::btnHome();
        if (Factory::getUser()->authorise('core.admin')){
            ToolbarHelper::preferences('com_jshopping');
        }
        parent::display($tpl);
    }
    function displayOptions($tpl=null){
        HelperAdmin::btnHome();
        ToolbarHelper::title( Text::_('JSHOP_OTHER_ELEMENTS'), 'generic.png' );
        parent::display($tpl);
    }
}