<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
defined('_JEXEC') or die();

class ShippingExtPriceController extends BaseadminController{

    function init(){
        \JSHelperAdmin::checkAccessController("shippingextprice");
        \JSHelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
		$shippings = \JSFactory::getModel("shippingextprice");
		$rows = $shippings->getList();

		$view = $this->getView("shippingext", 'html');
        $view->setLayout("list");
		$view->set('rows', $rows);
        $view->sidebar = \JHTMLSidebar::render();
		$view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayShippingExtPrices', array(&$view));
		$view->displayList();
	}

	function edit() {
        \JFactory::getApplication()->input->set('hidemainmenu', true);
		$id = $this->input->getInt("id");
        $row = \JSFactory::getTable('shippingext');
        $row->load($id);

        if (!$row->exec) {
            \JSError::raiseError( 404, "Error load ShippingExt");
        }

        $shippings_conects = $row->getShippingMethod();

        $shippings = \JSFactory::getModel("shippings");
        $list_shippings = $shippings->getAllShippings(0);

        $nofilter = array("params", "shipping_method");
        \JFilterOutput::objectHTMLSafe($row, ENT_QUOTES, $nofilter);

        $view = $this->getView("shippingext", 'html');
        $view->setLayout("edit");
        $view->set('row', $row);
        $view->set('list_shippings', $list_shippings);
        $view->set('shippings_conects', $shippings_conects);
		$view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->etemplatevar = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditShippingExtPrice', array(&$view));
        $view->displayEdit();
	}

    function remove(){
        $id = $this->input->getInt("id");
        \JSFactory::getModel("shippingextprice")->delete($id);
        $this->setRedirect("index.php?option=com_jshopping&controller=shippingextprice", \JText::_('JSHOP_ITEM_DELETED'));
    }

    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=shippings");
    }

}