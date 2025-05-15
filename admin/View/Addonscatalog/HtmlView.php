<?php
/**
* @version      5.7.0 08.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Addonscatalog;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function displayList($tpl=null){
        ToolbarHelper::title(Text::_('JSHOP_ADDONS_CATALOG'), 'generic.png');
        ToolbarHelper::custom("addons.display", 'arrow-left', 'arrow-left', Text::_('JSHOP_ADDONS'), false);
        HelperAdmin::btnHome();
        ToolbarHelper::custom("refresh", 'icon-refresh', 'icon-refresh', Text::_('JSHOP_REFRESH'), false);
	    ToolBarHelper::divider();
	    if (Factory::getUser()->authorise('core.admin')){
		    ToolbarHelper::custom("apikey", 'options', 'options', Text::_('JSHOP_API_KEY'), false);
	    }
        parent::display($tpl);
	}

    function displayApikey($tpl=null){
        ToolbarHelper::title(Text::_('JSHOP_API_KEY'), 'generic.png');
        ToolbarHelper::custom("back", 'arrow-left', 'arrow-left', Text::_('JSHOP_ADDONS_CATALOG'), false);
        ToolbarHelper::save('apikeysave');
        HelperAdmin::btnHome();
        parent::display($tpl);
	}

}