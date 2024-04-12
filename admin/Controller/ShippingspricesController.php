<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die;

class ShippingsPricesController extends BaseadminController{

    function init(){
        \JSHelperAdmin::checkAccessController("shippingsprices");
        \JSHelperAdmin::addSubmenu("other");
    }
    
    public function getUrlListItems(){
        $shipping_id_back = $this->input->getInt("shipping_id_back");
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&shipping_id_back=".$shipping_id_back;
    }
    
    public function getUrlEditItem($id = 0){
        return $this->getUrlListItems()."&task=edit&sh_pr_method_id=".$id;
    }
    
    function display($cachable = false, $urlparams = false){
		$db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $jshopConfig = \JSFactory::getConfig();
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.shippingsprices";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "shipping_price.sh_pr_method_id", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');

        $shipping_id_back = $this->input->getInt("shipping_id_back");
        $shippings = \JSFactory::getModel("shippings");
        $rows = $shippings->getAllShippingPrices(0, $shipping_id_back, $filter_order, $filter_order_Dir, 1);
        $currency = \JSFactory::getTable('currency');
        $currency->load($jshopConfig->mainCurrency);

		$view = $this->getView("shippingsprices", 'html');
        $view->setLayout("list");
		$view->set('rows', $rows);
        $view->set('currency', $currency);
        $view->set('shipping_id_back', $shipping_id_back);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayShippngsPrices', array(&$view));
		$view->displayList();
	}

    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $jshopConfig = \JSFactory::getConfig();
        $sh_pr_method_id = $this->input->getInt('sh_pr_method_id');
        $shipping_id_back = $this->input->getInt("shipping_id_back");
        $sh_method_price = \JSFactory::getTable('shippingmethodprice');
        $sh_method_price->load($sh_pr_method_id);
        $sh_method_price->prices = $sh_method_price->getPrices();
        if ($jshopConfig->tax){
            $list_tax = SelectOptions::getTaxs(0, 0, array('product_tax_rate'=>1));
            $lists['taxes'] = \JHTML::_('select.genericlist', $list_tax, 'shipping_tax_id','class="inputbox form-control"','tax_id','tax_name',$sh_method_price->shipping_tax_id);
            $lists['package_taxes'] = \JHTML::_('select.genericlist', $list_tax, 'package_tax_id','class="inputbox form-control"','tax_id','tax_name',$sh_method_price->package_tax_id);
        }
        $actived = $sh_method_price->shipping_method_id;
        if (!$actived) $actived = $shipping_id_back;
		$lists['shipping_methods'] = \JHTML::_('select.genericlist', SelectOptions::getShippings(0),'shipping_method_id','class = "inputbox form-control"','shipping_id','name', $actived);
		$lists['countries'] = \JHTML::_('select.genericlist', SelectOptions::getCountrys(0), 'shipping_countries_id[]','class = "inputbox form-control" size = "10", multiple = "multiple"','country_id','name', $sh_method_price->getCountries());
        if ($jshopConfig->admin_show_delivery_time){
            $lists['deliverytimes'] = \JHTML::_('select.genericlist', SelectOptions::getDeliveryTimes(), 'delivery_times_id','class = "inputbox form-control"','id','name', $sh_method_price->delivery_times_id);
        }

        $currency = \JSFactory::getTable('currency');
        $currency->load($jshopConfig->mainCurrency);

        $extensions = \JSFactory::getShippingExtList($actived);

		$view = $this->getView("shippingsprices", 'html');
        $view->setLayout("edit");
		$view->set('sh_method_price', $sh_method_price);
		$view->set('lists', $lists);
        $view->set('shipping_id_back', $shipping_id_back);
        $view->set('currency', $currency);
        $view->set('extensions', $extensions);
        $view->set('config', $jshopConfig);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditShippingsPrices', array(&$view));
        $view->displayEdit();
    }

    function back(){
        $this->setRedirect('index.php?option=com_jshopping&controller=shippings');
    }

}