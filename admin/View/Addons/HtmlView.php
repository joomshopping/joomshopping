<?php
/**
* @version      5.7.0 08.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Addons;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{
    
    function displayList($tpl=null){
        ToolbarHelper::title(Text::_('JSHOP_ADDONS'), 'generic.png');
        ToolbarHelper::publishList();
        ToolbarHelper::unpublishList();
        ToolbarHelper::deleteList(Text::_('JSHOP_DELETE_ITEM_CAN_BE_USED'));
        if ($this->config->disable_admin['addons_catalog'] == 0) {
            ToolbarHelper::custom("addonscatalog.display", 'folder', 'folder', Text::_('JSHOP_ADDONS_CATALOG'), false);
        }
        HelperAdmin::btnHome();
        parent::display($tpl);
	}
    
    function displayEdit($tpl = null){
        ToolbarHelper::title(Text::_('JSHOP_ADDONS')." / ".Text::_('JSHOP_CONFIG').' / '.$this->row->name, 'generic.png' );
        ToolbarHelper::save();
        ToolbarHelper::spacer();
        ToolbarHelper::apply();
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();
        parent::display($tpl);
    }

    function displayConfig($tpl = null){
        ToolbarHelper::title(Text::_('JSHOP_ADDONS')." / ".Text::_('JSHOP_MAINTENANCE').' / '.$this->row->name, 'generic.png' );
        ToolbarHelper::save('saveconfig');
        ToolbarHelper::cancel();
        parent::display($tpl);
    }
    
    function displayInfo($tpl = null){
        ToolbarHelper::title(Text::_('JSHOP_ADDONS')." / ".Text::_('JSHOP_DESCRIPTION').' / '.$this->row->name, 'generic.png' );
        ToolbarHelper::cancel();
        parent::display($tpl);
    }
    
    function displayVersion($tpl = null){
        ToolbarHelper::title(Text::_('JSHOP_ADDONS')." / ".Text::_('JSHOP_VERSION').' / '.$this->row->name, 'generic.png' );
        ToolbarHelper::cancel();
        parent::display($tpl);
    }

    function displayHelp($tpl = null){
        ToolbarHelper::title(Text::_('JSHOP_ADDONS'), 'generic.png');
        ToolbarHelper::cancel();
        parent::display($tpl);
    }

}