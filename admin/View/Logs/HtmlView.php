<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Logs;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl = null){        
        \JToolBarHelper::title( \JText::_('JSHOP_LOGS'), 'generic.png');
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl = null){
        \JToolBarHelper::title(\JText::_('JSHOP_LOGS')." / ".$this->filename, 'generic.png');
        \JToolBarHelper::back();
        parent::display($tpl);
    }
}
