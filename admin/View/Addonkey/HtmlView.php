<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Addonkey;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function display($tpl=null){
        
        \JToolBarHelper::title( \JText::_('JSHOP_ENTER_LICENSE_KEY'), 'generic.png' ); 
        \JToolBarHelper::save();
        \JToolBarHelper::spacer();
        \JToolBarHelper::cancel();
        
        parent::display($tpl);
	}
}
?>