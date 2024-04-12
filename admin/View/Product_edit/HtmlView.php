<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Product_edit;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function display($tpl=null){
        $title = \JText::_('JSHOP_NEW_PRODUCT');
        if (isset($this->edit) && $this->edit){
            $title = \JText::_('JSHOP_EDIT_PRODUCT');
            if (!$this->product_attr_id) $title .= ' "'.$this->product->name.'"';
        }
        \JToolBarHelper::title($title, 'generic.png' );
        \JToolBarHelper::save();
        if (!isset($this->product_attr_id) || !$this->product_attr_id){
            \JToolBarHelper::spacer();
            \JToolBarHelper::apply();
            \JToolBarHelper::spacer();
            \JToolBarHelper::save2new();
            \JToolBarHelper::spacer();
            \JToolBarHelper::cancel();
        }
        parent::display($tpl);
	}

    function editGroup($tpl=null){
        \JToolBarHelper::title(\JText::_('JSHOP_EDIT_PRODUCT'), 'generic.png');
        \JToolBarHelper::save("savegroup");
        \JToolBarHelper::cancel();
        parent::display($tpl);
    }
}
?>