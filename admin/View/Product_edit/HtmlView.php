<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Product_edit;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function display($tpl=null){
        $title = Text::_('JSHOP_NEW_PRODUCT');
        if (isset($this->edit) && $this->edit){
            $title = Text::_('JSHOP_EDIT_PRODUCT');
            if (!$this->product_attr_id) $title .= ' "'.$this->product->name.'"';
        }
        ToolbarHelper::title($title, 'generic.png' );
        ToolbarHelper::save();
        if (!isset($this->product_attr_id) || !$this->product_attr_id){
            ToolbarHelper::spacer();
            ToolbarHelper::apply();
            ToolbarHelper::spacer();
            ToolbarHelper::save2new();
            ToolbarHelper::spacer();
            ToolbarHelper::cancel();
        }
        parent::display($tpl);
	}

    function editGroup($tpl=null){
        ToolbarHelper::title(Text::_('JSHOP_EDIT_PRODUCT'), 'generic.png');
        ToolbarHelper::save("savegroup");
        ToolbarHelper::cancel();
        parent::display($tpl);
    }
}
?>