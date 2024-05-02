<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Shippingext;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        ToolbarHelper::title( Text::_('JSHOP_SHIPPING_EXT_PRICE_CALC'), 'generic.png' );        
        ToolbarHelper::custom( "back", 'folder', 'folder', Text::_('JSHOP_LIST_SHIPPINGS'), false);
        HelperAdmin::btnHome();
        parent::display($tpl);
    }
    
    function displayEdit($tpl=null){        
        ToolbarHelper::title( Text::_('JSHOP_SHIPPING_EXT_PRICE_CALC'), 'generic.png' );        
        ToolbarHelper::save();
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();        
        parent::display($tpl);
    }
}