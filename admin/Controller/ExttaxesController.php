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

defined( '_JEXEC' ) or die();

class ExtTaxesController extends BaseadminController{
    
    function init(){
        \JSHelperAdmin::checkAccessController("exttaxes");
        \JSHelperAdmin::addSubmenu("other");
    }
    
    public function getUrlListItems(){
        $back_tax_id = $this->input->getInt("back_tax_id");
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&back_tax_id=".$back_tax_id;
    }
    
    public function getUrlEditItem($id = 0){
        $back_tax_id = $this->input->getInt("back_tax_id");
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&task=edit&id=".$id."&back_tax_id=".$back_tax_id;
    }

    function display($cachable = false, $urlparams = false){
        $jshopConfig = \JSFactory::getConfig();
        $back_tax_id = $this->input->getInt("back_tax_id");
        $app = \JFactory::getApplication();
        $context = "jshoping.list.admin.exttaxes";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ET.id", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $taxes = \JSFactory::getModel("taxes");
        $rows = $taxes->getExtTaxes($back_tax_id, $filter_order, $filter_order_Dir);
        
        $countries = \JSFactory::getModel("countries");
        $list = $countries->getAllCountries(0);
        $countries_name = array();
        foreach($list as $v){
            $countries_name[$v->country_id] = $v->name;
        }

        foreach($rows as $k=>$v){
            $list = (array)unserialize($v->zones);

            foreach($list as $k2=>$v2){
                $list[$k2] = $countries_name[$v2];
            }
            if (count($list) > 10){
                $tmp = array_slice($list, 0, 10);
                $rows[$k]->countries = implode(", ", $tmp)."...";
            }else{
                $rows[$k]->countries = implode(", ", $list);
            }
        }

        $view = $this->getView("taxes_ext", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows); 
        $view->set('back_tax_id', $back_tax_id);
        $view->set('config', $jshopConfig);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforedisplayExtTax', array(&$view)); 
        $view->displayList();
    }

    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $jshopConfig = \JSFactory::getConfig();
        $back_tax_id = $this->input->getInt("back_tax_id");
        $id = $this->input->getInt("id");
        
        $tax = \JSFactory::getTable('taxext');
        $tax->load($id);
        
        if (!$tax->tax_id && $back_tax_id){
            $tax->tax_id = $back_tax_id;
        }

        $list_c = $tax->getZones();
        $zone_countries = array();
        foreach($list_c as $v){
            $obj = new \stdClass();
            $obj->country_id = $v;
            $zone_countries[] = $obj;
        }

        $lists['taxes'] = \JHTML::_('select.genericlist', SelectOptions::getTaxs(), 'tax_id', 'class="custom-select"', 'tax_id', 'tax_name', $tax->tax_id);
        $lists['countries'] = \JHTML::_('select.genericlist', SelectOptions::getCountrys(0), 'countries_id[]', 'size = "10", class="custom-select", multiple = "multiple"', 'country_id', 'name', $zone_countries);        

        $view = $this->getView("taxes_ext", 'html');
        $view->setLayout("edit");
        \JFilterOutput::objectHTMLSafe($tax, ENT_QUOTES);
        $view->set('tax', $tax);
        $view->set('back_tax_id', $back_tax_id);
        $view->set('lists', $lists);
        $view->set('config', $jshopConfig);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditExtTax', array(&$view));
        $view->displayEdit();
    }

    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=taxes");
    }

}