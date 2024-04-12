<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;
defined('_JEXEC') or die();

class CountriesModel extends BaseadminModel{
    
    protected $nameTable = 'countryTable';
    protected $tableFieldPublish = 'country_publish';
    
    /**
    * get list country
    * 
    * @param int $publish (0-all, 1-publish, 2-unpublish)
    * @param int $limitstart
    * @param int $limit
    * @param int $orderConfig use order config
    * @return array
    */
    function getAllCountries($publish = 1, $limitstart = null, $limit = null, $orderConfig = 1, $order = null, $orderDir = null){
        $db = \JFactory::getDBO();
        $jshopConfig = \JSFactory::getConfig();
                
        if ($publish == 0) {
            $where = " ";
        } else {
            if ($publish == 1) {
                $where = (" WHERE country_publish = '1' ");
            } else {
                if ($publish == 2) {
                    $where = (" WHERE country_publish = '0' ");
                }
            }
        }
        $ordering = "ordering";
        if ($orderConfig && $jshopConfig->sorting_country_in_alphabet) $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $lang = \JSFactory::getLang();
        $query = "SELECT country_id, country_publish, ordering, country_code, country_code_2, `".$lang->get("name")."` as name FROM `#__jshopping_countries` ".$where." ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    /**
    * get count country
    * @return int
    */
    function getCountAllCountries() {
        $db = \JFactory::getDBO(); 
        $query = "SELECT COUNT(country_id) FROM `#__jshopping_countries`";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    /**
    * get count county
    * @param int $publish
    * @return int
    */
    function getCountPublishCountries($publish = 1) {
        $db = \JFactory::getDBO(); 
        $query = "SELECT COUNT(country_id) FROM `#__jshopping_countries` WHERE country_publish = '".intval($publish)."'";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    public function save(array $post){
        $dispatcher = \JFactory::getApplication();		
        $dispatcher->triggerEvent('onBeforeSaveCountry', array(&$post));
		$country = \JSFactory::getTable('country');
		$country->bind($post);	
		if (!$country->country_publish){
			$country->country_publish = 0;
	    }    
		$this->_reorderCountry($country);
		if (!$country->store()) {
			$this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE').' '.$country->getError());
			return 0;
		}
        $dispatcher->triggerEvent('onAfterSaveCountry', array(&$country));
        return $country;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = \JFactory::getDBO();
		$app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveCountry', array(&$cid));
        $res = array();
		foreach($cid as $id){
			$query = "DELETE FROM `#__jshopping_countries` WHERE `country_id`=".(int)$id;
			$db->setQuery($query);
			$db->execute();
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_COUNTRY_DELETED'), 'message');
            }
            $res[$id] = true;
		}
        $dispatcher->triggerEvent('onAfterRemoveCountry', array(&$cid));
        return $res;
    }
    
    protected function _reorderCountry(&$country) {
		$db = \JFactory::getDBO();
		$query = "UPDATE `#__jshopping_countries` SET `ordering` = ordering + 1 WHERE `ordering` > '".$country->ordering."'";		
		$db->setQuery($query);
		$db->execute();
		$country->ordering++;
	}
    
    public function publish(array $cid, $flag){
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishCountry', array(&$cid, &$flag));
        parent::publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishCountry', array(&$cid, &$flag));
	}
      
}