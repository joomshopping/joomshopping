<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Shippingsprices;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_SHIPPING_PRICES_LIST'), 'generic.png' ); 
        \JToolBarHelper::custom("back", 'arrow-left', 'arrow-left', \JText::_('JSHOP_LIST_SHIPPINGS'), false);
        \JToolBarHelper::addNew();
        \JToolBarHelper::deleteList(\JText::_('JSHOP_DELETE')."?");
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        \JToolBarHelper::title($this->sh_method_price->sh_pr_method_id ? (\JText::_('JSHOP_EDIT_SHIPPING_PRICES')) : (\JText::_('JSHOP_NEW_SHIPPING_PRICES')), 'generic.png' ); 
        \JToolBarHelper::save();
        \JToolBarHelper::spacer();
        \JToolBarHelper::apply();
        \JToolBarHelper::spacer();
        \JToolBarHelper::save2new();
        \JToolBarHelper::spacer();
        \JToolBarHelper::cancel();        
        parent::display($tpl);
    }
}