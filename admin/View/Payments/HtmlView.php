<?php
/**
* @version      5.6.2 15.04.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Payments;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){
        ToolbarHelper::title( Text::_('JSHOP_LIST_PAYMENTS'), 'generic.png' ); 
        ToolbarHelper::addNew();
        ToolbarHelper::custom('copy', 'copy', 'copy_f2.png', Text::_('JLIB_HTML_BATCH_COPY'));
        ToolbarHelper::publishList();
        ToolbarHelper::unpublishList();
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        HelperAdmin::btnHome();
        parent::display($tpl);
    }
    
    function displayEdit($tpl=null){
        ToolbarHelper::title( $this->payment->payment_id ? (Text::_('JSHOP_EDIT_PAYMENT').' / '.$this->payment->{\JSFactory::getLang()->get('name')}) : (Text::_('JSHOP_NEW_PAYMENT')), 'generic.png' );
        ToolbarHelper::save();
        ToolbarHelper::spacer();
        ToolbarHelper::apply();
        ToolbarHelper::spacer();
        ToolbarHelper::save2new();
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();
        parent::display($tpl);
    }
}