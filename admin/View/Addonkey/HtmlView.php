<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Addonkey;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function display($tpl=null){
        
        ToolbarHelper::title( Text::_('JSHOP_ENTER_LICENSE_KEY'), 'generic.png' ); 
        ToolbarHelper::save();
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();
        
        parent::display($tpl);
	}
}
?>