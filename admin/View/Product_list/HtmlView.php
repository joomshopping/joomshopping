<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Product_list;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function display($tpl=null){
        \JToolBarHelper::title( \JText::_('JSHOP_LIST_PRODUCT'), 'generic.png' );
        \JToolBarHelper::addNew();
        \JToolBarHelper::custom('copy', 'copy', 'copy_f2.png', \JText::_('JLIB_HTML_BATCH_COPY'));
        \JToolBarHelper::editList('editlist');
        \JToolBarHelper::publishList();
        \JToolBarHelper::unpublishList();
        \JToolBarHelper::deleteList(\JText::_('JSHOP_DELETE')."?");
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    function displaySelectable($tpl=null){
        parent::display($tpl);
    }
}