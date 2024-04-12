<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Addons;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){
        \JToolBarHelper::title( \JText::_('JSHOP_ADDONS'), 'generic.png');
        \JToolBarHelper::addNew('help');
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl = null){        
        \JToolBarHelper::title(\JText::_('JSHOP_ADDONS')." / ".\JText::_('JSHOP_CONFIG').' / '.$this->row->name, 'generic.png' );
        \JToolBarHelper::save();
        \JToolBarHelper::spacer();
        \JToolBarHelper::apply();
        \JToolBarHelper::spacer();
        \JToolBarHelper::cancel();        
        parent::display($tpl);
    }
    
    function displayInfo($tpl = null){        
        \JToolBarHelper::title(\JText::_('JSHOP_ADDONS')." / ".\JText::_('JSHOP_DESCRIPTION').' / '.$this->row->name, 'generic.png' );
        \JToolBarHelper::cancel();        
        parent::display($tpl);
    }
    
    function displayVersion($tpl = null){        
        \JToolBarHelper::title(\JText::_('JSHOP_ADDONS')." / ".\JText::_('JSHOP_VERSION').' / '.$this->row->name, 'generic.png' );
        \JToolBarHelper::cancel();        
        parent::display($tpl);
    }

    function displayHelp($tpl = null){
        \JToolBarHelper::title(\JText::_('JSHOP_ADDONS'), 'generic.png');
        \JToolBarHelper::cancel();
        parent::display($tpl);
    }
    
}