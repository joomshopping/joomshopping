<?php
/**
* @version      5.8.0 15.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Attributesvalues;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){        
        ToolbarHelper::title($this->attr_name." / ".Text::_('JSHOP_LIST_ATTRIBUT_VALUES'), 'generic.png' );
        ToolbarHelper::custom("back", 'arrow-left', 'arrow-left', Text::_('JSHOP_RETURN_TO_ATTRIBUTES'), false);
        ToolbarHelper::addNew();
        ToolbarHelper::publishList();
        ToolbarHelper::unpublishList();
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        HelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        ToolbarHelper::title( $temp = ($this->attributValue->value_id) ? (Text::_('JSHOP_EDIT_ATTRIBUT_VALUE').' / '.$this->attributValue->{\JSFactory::getLang()->get('name')}) : (Text::_('JSHOP_NEW_ATTRIBUT_VALUE')), 'generic.png' ); 
        ToolbarHelper::save();
        ToolbarHelper::apply();
        ToolbarHelper::save2new();
        ToolbarHelper::cancel();
        parent::display($tpl);
    }
}