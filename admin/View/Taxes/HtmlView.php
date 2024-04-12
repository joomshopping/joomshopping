<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Taxes;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){
        \JToolBarHelper::title( \JText::_('JSHOP_LIST_TAXES'), 'generic.png' ); 
        \JToolBarHelper::addNew();
        \JToolBarHelper::deleteList(\JText::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    function displayEdit($tpl=null){
        \JToolBarHelper::title( $temp = ($this->edit) ? (\JText::_('JSHOP_EDIT_TAX')." / ".$this->tax->tax_name) : (\JText::_('JSHOP_NEW_TAX')), 'generic.png' ); 
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