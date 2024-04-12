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
defined('_JEXEC') or die();

class ConfigDisplayPriceController extends BaseadminController{
    
    function init(){
        \JSHelperAdmin::checkAccessController("configdisplayprice");
        \JSHelperAdmin::addSubmenu("config");
    }
    
    function display($cachable = false, $urlparams = false){		
        $model = \JSFactory::getModel("configdisplayprice");
        $rows = $model->getList(1);
        $typedisplay = $model->getPriceType();		
        
        $view = $this->getView("config_display_price", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows);
        $view->set('typedisplay', $typedisplay);
        $view->sidebar = \JHTMLSidebar::render();
        $view->tmp_html_start = '';
        $view->tmp_html_end = '';
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayConfigDisplayPrice', array(&$view)); 
        $view->displayList();
    }
    
    function edit() {        
        $id = $this->input->getInt("id");
        
        $configdisplayprice = \JSFactory::getTable('configdisplayprice');
        $configdisplayprice->load($id);
        
        $list_c = $configdisplayprice->getZones();
        $zone_countries = array();        
        foreach($list_c as $v){
            $obj = new \stdClass();
            $obj->country_id = $v;
            $zone_countries[] = $obj;
        }
        
        $display_price_list = SelectOptions::getPriceType();        
        $lists['display_price'] = \JHTML::_('select.genericlist', $display_price_list, 'display_price', '', 'id', 'name', $configdisplayprice->display_price);
        $lists['display_price_firma'] = \JHTML::_('select.genericlist', $display_price_list, 'display_price_firma', '', 'id', 'name', $configdisplayprice->display_price_firma);
        $lists['countries'] = \JHTML::_('select.genericlist', SelectOptions::getCountrys(0), 'countries_id[]', 'size = "10", multiple = "multiple"', 'country_id', 'name', $zone_countries);
        
        \JFilterOutput::objectHTMLSafe($configdisplayprice, ENT_QUOTES);

        $view = $this->getView("config_display_price", 'html');
        $view->setLayout("edit");
        $view->set('row', $configdisplayprice);
        $view->set('lists', $lists);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = '';
        $view->tmp_html_end = '';
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigDisplayPrice', array(&$view));
        $view->displayEdit();
    }
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=config&task=general");
    }
    
}