<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Logs;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl = null){        
        ToolbarHelper::title( Text::_('JSHOP_LOGS'), 'generic.png');
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        HelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl = null){
        ToolbarHelper::title(Text::_('JSHOP_LOGS')." / ".$this->filename, 'generic.png');
        ToolbarHelper::back();
        parent::display($tpl);
    }
}
