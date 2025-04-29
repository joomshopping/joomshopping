<?php
/**
* @version      5.6.0 13.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die();

class CountriesModel extends BaseadminModel{
    
    protected $nameTable = 'country';
    protected $tableFieldPublish = 'country_publish';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAllCountries($filters, $limit['limitstart'] ?? null, $limit['limit'] ?? null, $params['orderConfig'] ?? 1, $orderBy['order'] ?? null, $orderBy['dir'] ?? null);
	}

	public function getCountItems(array $filters = [], array $params = []) {
		return $this->getCountPublishCountries($filters);
	}
    
    /**
    * get list country
    * 
    * @param mixed $filter array or (0-all, 1-publish, 2-unpublish)
    * @param int $limitstart
    * @param int $limit
    * @param int $orderConfig use order config
    * @return array
    */
    function getAllCountries($filter = 1, $limitstart = null, $limit = null, $orderConfig = 1, $order = null, $orderDir = null){
        $db = Factory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
                
        $where = '';
        if (is_array($filter)) {
            if (isset($filter['publish'])) {
                $where .= " AND `country_publish` = ".$db->q($filter['publish']);
            }
            if (isset($filter['text_search'])) {
                $where .= " AND (`".$lang->get("name")."` LIKE ".$db->q('%'.$filter['text_search'].'%')." OR country_code=".$db->q($filter['text_search'])." OR country_code_2=".$db->q($filter['text_search']).")";
            }
        } else {
            if ($filter == 1) {
                $where = " AND country_publish = 1 ";
            }
            if ($filter == 2) {
                $where = " AND country_publish = 0 ";
            }
        }
        
        $ordering = "ordering";
        if ($orderConfig && $jshopConfig->sorting_country_in_alphabet) $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT country_id, country_publish, ordering, country_code, country_code_2, `".$lang->get("name")."` as name 
            FROM `#__jshopping_countries` WHERE 1 ".$where." ORDER BY ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    /**
    * get count country
    * @return int
    */
    function getCountAllCountries() {
        $db = Factory::getDBO(); 
        $query = "SELECT COUNT(country_id) FROM `#__jshopping_countries`";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    /**
    * get count county
    * @param mixed $filter array or int => publish
    * @return int
    */
    function getCountPublishCountries($filter = 1) {
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $where = '';
        if (is_array($filter)) {
            if (isset($filter['publish'])) {
                $where .= " AND `country_publish` = ".$db->q($filter['publish']);
            }
            if (isset($filter['text_search'])) {
                $where .= " AND (`".$lang->get("name")."` LIKE ".$db->q('%'.$filter['text_search'].'%')." OR country_code=".$db->q($filter['text_search'])." OR country_code_2=".$db->q($filter['text_search']).")";
            }
        } else {
            if ($filter == 1) {
                $where = " AND country_publish = 1";
            }
            if ($filter == 0) {
                $where = " AND country_publish = 0";
            }
        }
        $query = "SELECT COUNT(country_id) FROM `#__jshopping_countries` WHERE 1 ".$where;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    public function save(array $post){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveCountry', array(&$post));
		$country = JSFactory::getTable('country');
		$country->bind($post);	
		if (!$country->country_publish){
			$country->country_publish = 0;
	    }    
		$this->_reorderCountry($country);
		if (!$country->store()) {
			$this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE').' '.$country->getError());
			return 0;
		}
        $dispatcher->triggerEvent('onAfterSaveCountry', array(&$country));
        return $country;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = Factory::getDBO();
		$app = Factory::getApplication();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveCountry', array(&$cid));
        $res = array();
		foreach($cid as $id){
			$query = "DELETE FROM `#__jshopping_countries` WHERE `country_id`=".(int)$id;
			$db->setQuery($query);
			$db->execute();
            if ($msg){
                $app->enqueueMessage(Text::_('JSHOP_COUNTRY_DELETED'), 'message');
            }
            $res[$id] = true;
		}
        $dispatcher->triggerEvent('onAfterRemoveCountry', array(&$cid));
        return $res;
    }
    
    protected function _reorderCountry(&$country) {
		$db = Factory::getDBO();
		$query = "UPDATE `#__jshopping_countries` SET `ordering` = ordering + 1 WHERE `ordering` > ".$db->q($country->ordering);
		$db->setQuery($query);
		$db->execute();
		$country->ordering++;
	}
    
    public function publish(array $cid, $flag){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishCountry', array(&$cid, &$flag));
        parent::publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishCountry', array(&$cid, &$flag));
	}
      
}