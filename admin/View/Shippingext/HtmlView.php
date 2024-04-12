<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Shippingext;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_SHIPPING_EXT_PRICE_CALC'), 'generic.png' );        
        \JToolBarHelper::custom( "back", 'folder', 'folder', \JText::_('JSHOP_LIST_SHIPPINGS'), false);
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
    }
    
    function displayEdit($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_SHIPPING_EXT_PRICE_CALC'), 'generic.png' );        
        \JToolBarHelper::save();
        \JToolBarHelper::spacer();
        \JToolBarHelper::cancel();        
        parent::display($tpl);
    }
}