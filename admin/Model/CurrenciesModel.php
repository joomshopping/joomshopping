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

class CurrenciesModel extends BaseadminModel{
    
    protected $nameTable = 'currencyTable';
    protected $tableFieldOrdering = 'currency_ordering';
    protected $tableFieldPublish = 'currency_publish';

    function getAllCurrencies($publish = 1, $order = null, $orderDir = null) {
        $db = \JFactory::getDBO();
        $query_where = ($publish)?("WHERE currency_publish = '1'"):("");
        $ordering = 'currency_ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT * FROM `#__jshopping_currencies` $query_where ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveCurrencie', array(&$cid));
        $res = array();
        foreach($cid as $id){
            if ($this->delete($id)){
                if ($msg){
                    $app->enqueueMessage(\JText::_('JSHOP_CURRENCY_DELETED'), 'message');
                }
                $res[$id] = true;
            }else{
                if ($msg){
                    $app->enqueueMessage(\JText::_('JSHOP_CURRENCY_ERROR_DELETED'), 'error');
                }
                $res[$id] = false;
            }
        }        
        $dispatcher->triggerEvent('onAfterRemoveCurrencie', array(&$cid));
        return $res;
    }
    
    function getCountProduct($id){
        $db = \JFactory::getDBO();
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
        $row = \JSFactory::getTable('currency');
        return $row->delete($id);
    }
    
    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $post['currency_publish'] = (int)$post['currency_publish'];
        $post['currency_value'] = \JSHelper::saveAsPrice($post['currency_value']);
        return $post;
    }
    
    public function save(array $post){
        $dispatcher = \JFactory::getApplication();
        $currency = \JSFactory::getTable('currency');
        $dispatcher->triggerEvent('onBeforeSaveCurrencie', array(&$post));
        $currency->bind($post);
        if ($currency->currency_value==0){
            $currency->currency_value = 1;
        }
        $this->_reorderCurrency($currency);
        if (!$currency->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveCurrencie', array(&$currency));
        return $currency;
    }
    
    public function publish(array $cid, $flag){
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishCurrencie', array(&$cid, &$flag));
        parent::publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishCurrencie', array(&$cid, &$flag));
    }
    
    protected function _reorderCurrency(&$currency) {
        $db = \JFactory::getDBO();
        $query = "UPDATE `#__jshopping_currencies`
                    SET `currency_ordering` = currency_ordering + 1
                    WHERE `currency_ordering` > '" . $currency->currency_ordering . "'";
        $db->setQuery($query);
        $db->execute();
        $currency->currency_ordering++;
    }
}