<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Users;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
	
    function displayList($tpl=null){        
        ToolbarHelper::title( Text::_('JSHOP_USER_LIST'), 'generic.png' );
        ToolbarHelper::addNew(); 
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE')."?");
        HelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        $title = Text::_('JSHOP_USERS')." / ";
        if ($this->user->user_id){
            $title.=$this->user->u_name;
        }else{
            $title.=Text::_('JSHOP_NEW');
        }
        ToolbarHelper::title($title, 'generic.png');
        ToolbarHelper::save();
        ToolbarHelper::apply();
        ToolbarHelper::save2new();
        ToolbarHelper::cancel();
        parent::display($tpl);
    }
}