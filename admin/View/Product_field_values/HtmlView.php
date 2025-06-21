<?php
/**
* @version      5.8.0 29.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Product_field_values;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        ToolbarHelper::title($this->productfield->getName() . ' / ' . Text::_('JSHOP_PRODUCT_EXTRA_FIELD_VALUES'), 'generic.png' );
        ToolbarHelper::custom( "back", 'arrow-left', 'arrow-left', Text::_('JSHOP_BACK_TO_PRODUCT_EXTRA_FIELDS'), false);
        if ($this->productfield->type != 2) {
            ToolbarHelper::addNew();
        }
        ToolbarHelper::publishList();
        ToolbarHelper::unpublishList();
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        HelperAdmin::btnHome();
        if ($this->productfield->type != 2 && $this->productfield->multilist == 0) {
            ToolbarHelper::custom("clear_double", 'folder', 'folder', Text::_('JSHOP_CLEAR_DUPLICATE_VALUE'), false);
        }
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        ToolbarHelper::title( $temp = ($this->row->id) ? (Text::_('JSHOP_EDIT').' / '.$this->row->{\JSFactory::getLang()->get('name')}) : (Text::_('JSHOP_NEW')), 'generic.png' );
        ToolbarHelper::save();
        ToolbarHelper::apply();
        ToolbarHelper::save2new();
        ToolbarHelper::cancel();        
        parent::display($tpl);
    }
}