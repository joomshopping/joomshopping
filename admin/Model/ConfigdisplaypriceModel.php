<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class ConfigDisplayPriceModel extends BaseadminModel{

    public function getList($loadCountiesInfo = 0){
        $db = \JFactory::getDBO(); 
        $query = "SELECT * FROM `#__jshopping_config_display_prices`";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($loadCountiesInfo){
            $rows = $this->addCountryToListConfigPrices($rows);
        }
        return $rows;
    }
    
    protected function addCountryToListConfigPrices($rows){
        $countries = \JSFactory::getModel("countries");
        $list = $countries->getAllCountries(0);    
        $countries_name = array();
        foreach($list as $v){
            $countries_name[$v->country_id] = $v->name;
        }
        foreach($rows as $k=>$v){
            $list = unserialize($v->zones);
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
        return $rows;
    }
    
    public function getPriceType(){
        return array(
            0 => \JText::_('JSHOP_PRODUCT_BRUTTO_PRICE'), 
            1 => \JText::_('JSHOP_PRODUCT_NETTO_PRICE')
        );
    }
    
    public function save(array $post){
        $configdisplayprice = \JSFactory::getTable('configDisplayPrice');        
        $dispatcher = \JFactory::getApplication();        
        $dispatcher->triggerEvent('onBeforeSaveConfigDisplayPrice', array(&$post));                
        if (!$post['countries_id']){
            $this->setError(\JText::_('JSHOP_ERROR_BIND'));
            return 0;
        }
        $configdisplayprice->bind($post);
        $configdisplayprice->setZones($post['countries_id']);
        if (!$configdisplayprice->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0; 
        }
        \JSHelperAdmin::updateCountConfigDisplayPrice();
        $dispatcher->triggerEvent('onAftetSaveConfigDisplayPrice', array(&$configdisplayprice));
        return $configdisplayprice;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = \JFactory::getDBO();
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDeleteConfigDisplayPrice', array(&$cid));
        $res = array();
        foreach($cid as $id){
            $query = "DELETE FROM `#__jshopping_config_display_prices` WHERE `id`=".(int)$id;
            $db->setQuery($query);
            $db->execute();
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
            }
            $res[$id] = true;
        }
        \JSHelperAdmin::updateCountConfigDisplayPrice();
        $dispatcher->triggerEvent('onAfterDeleteConfigDisplayPrice', array(&$cid));
        return $res;
    }
}
