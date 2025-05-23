<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

class CurrenciesModel extends BaseadminModel{
    
    protected $nameTable = 'currency';
    protected $tableFieldOrdering = 'currency_ordering';
    protected $tableFieldPublish = 'currency_publish';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAllCurrencies($filters['publish'] ?? 0, $orderBy['order'] ?? null, $orderBy['dir'] ?? null);
	}

    function getAllCurrencies($publish = 1, $order = null, $orderDir = null) {
        $db = Factory::getDBO();
        $query_where = ($publish)?("WHERE currency_publish = '1'"):("");
        $ordering = 'currency_ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT * FROM `#__jshopping_currencies` $query_where ORDER BY ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveCurrencie', array(&$cid));
        $res = array();
        foreach($cid as $id){
            if ($this->delete($id)){
                if ($msg){
                    $app->enqueueMessage(Text::_('JSHOP_CURRENCY_DELETED'), 'message');
                }
                $res[$id] = true;
            }else{
                if ($msg){
                    $app->enqueueMessage(Text::_('JSHOP_CURRENCY_ERROR_DELETED'), 'error');
                }
                $res[$id] = false;
            }
        }        
        $dispatcher->triggerEvent('onAfterRemoveCurrencie', array(&$cid));
        return $res;
    }
    
    function getCountProduct($id){
        $db = Factory::getDBO();
        $query = "select count(*) from #__jshopping_products where currency_id=".intval($id);
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function delete($id, $check = 1){
        if ($check){
            if ($this->getCountProduct($id)){
                return 0;
            }
        }
        $row = JSFactory::getTable('currency');
        return $row->delete($id);
    }
    
    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $post['currency_publish'] = $post['currency_publish'] ?? 0;
        $post['currency_value'] = Helper::saveAsPrice($post['currency_value']);
        return $post;
    }
    
    public function save(array $post){
        $dispatcher = Factory::getApplication();
        $currency = JSFactory::getTable('currency');
        $dispatcher->triggerEvent('onBeforeSaveCurrencie', array(&$post));
        $currency->bind($post);
        if ($currency->currency_value==0){
            $currency->currency_value = 1;
        }
        $this->_reorderCurrency($currency);
        if (!$currency->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveCurrencie', array(&$currency));
        return $currency;
    }
    
    public function publish(array $cid, $flag){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishCurrencie', array(&$cid, &$flag));
        parent::publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishCurrencie', array(&$cid, &$flag));
    }
    
    protected function _reorderCurrency(&$currency) {
        $db = Factory::getDBO();
        $query = "UPDATE `#__jshopping_currencies`
                    SET `currency_ordering` = currency_ordering + 1
                    WHERE `currency_ordering` > '" . $currency->currency_ordering . "'";
        $db->setQuery($query);
        $db->execute();
        $currency->currency_ordering++;
    }
}