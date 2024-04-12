<?php
/**
* @version      5.3.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Product_field_values;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_PRODUCT_EXTRA_FIELD_VALUES'), 'generic.png' );        
        \JToolBarHelper::custom( "back", 'arrow-left', 'arrow-left', \JText::_('JSHOP_BACK_TO_PRODUCT_EXTRA_FIELDS'), false);                 
        if ($this->productfield->product_uniq_val == 0) {
            \JToolBarHelper::addNew();
        }
        \JToolBarHelper::deleteList(\JText::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        \JToolBarHelper::title( $temp = ($this->row->id) ? (\JText::_('JSHOP_EDIT').' / '.$this->row->{\JSFactory::getLang()->get('name')}) : (\JText::_('JSHOP_NEW')), 'generic.png' );
        \JToolBarHelper::save();
        \JToolBarHelper::apply();
        \JToolBarHelper::save2new();
        \JToolBarHelper::cancel();        
        parent::display($tpl);
    }
}