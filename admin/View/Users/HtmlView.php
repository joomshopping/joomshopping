<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Users;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
	
    function displayList($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_USER_LIST'), 'generic.png' );
        \JToolBarHelper::addNew(); 
        \JToolBarHelper::deleteList(\JText::_('JSHOP_DELETE')."?");
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        $title = \JText::_('JSHOP_USERS')." / ";
        if ($this->user->user_id){
            $title.=$this->user->u_name;
        }else{
            $title.=\JText::_('JSHOP_NEW');
        }
        \JToolBarHelper::title($title, 'generic.png');
        \JToolBarHelper::save();
        \JToolBarHelper::apply();
        \JToolBarHelper::save2new();
        \JToolBarHelper::cancel();
        parent::display($tpl);
    }
}