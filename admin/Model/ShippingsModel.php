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

class ShippingsModel extends BaseadminModel{
    
    protected $nameTable = 'shippingMethod';

    public function getAllShippings($publish = 1, $order = null, $orderDir = null) {
        $db = \JFactory::getDBO();
        $query_where = ($publish)?("WHERE published = '1'"):("");
        $lang = \JSFactory::getLang();
        $ordering = 'ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT shipping_id, `".$lang->get('name')."` as name, `".$lang->get("description")."` as description, published, ordering
                  FROM `#__jshopping_shipping_method`
                  $query_where
                  ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getAllShippingPrices($publish = 1, $shipping_method_id = 0, $order = null, $orderDir = null, $loadCountriesInfo = 0){
        $db = \JFactory::getDBO();
        $query_where = "";
        $query_where .= ($publish)?(" and shipping.published = '1'"):("");
        $query_where .= ($shipping_method_id)?(" and shipping_price.shipping_method_id= '".$shipping_method_id."'"):("");

        $ordering = "shipping_price.sh_pr_method_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }

        $lang = \JSFactory::getLang();
        $query = "SELECT shipping_price.*, shipping.`".$lang->get('name')."` as name
                  FROM `#__jshopping_shipping_method_price` AS shipping_price
                  INNER JOIN `#__jshopping_shipping_method` AS shipping ON shipping.shipping_id = shipping_price.shipping_method_id
                  where (1=1) $query_where
                  ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($loadCountriesInfo){
            $rows = $this->addCountriesToShippingPricesList($rows);
        }
        return $rows;
    }
    
    protected function getListCountryFromShPrMethodId(){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "select MPC.sh_pr_method_id, C.`".$lang->get("name")."` as name from #__jshopping_shipping_method_price_countries as MPC
                  left join #__jshopping_countries as C on C.country_id=MPC.country_id order by MPC.sh_pr_method_id, C.ordering";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    protected function addCountriesToShippingPricesList($rows){
        $list = $this->getListCountryFromShPrMethodId();
        $shipping_countries = array();
        foreach($list as $smp){
            $shipping_countries[$smp->sh_pr_method_id][] = $smp->name;
        }
        unset($list);
        foreach($rows as $k=>$row){
            $rows[$k]->countries = "";
            if (is_array($shipping_countries[$row->sh_pr_method_id])){
                if (count($shipping_countries[$row->sh_pr_method_id])>10){
                    $tmp =  array_slice($shipping_countries[$row->sh_pr_method_id],0,10);
                    $rows[$k]->countries = implode(", ",$tmp)."...";
                }else{
                    $rows[$k]->countries = implode(", ",$shipping_countries[$row->sh_pr_method_id]);
                }
            }
        }
        return $rows;
    }

    function getMaxOrdering(){
        $db = \JFactory::getDBO();
        $query = "select max(ordering) from `#__jshopping_shipping_method`";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function saveCountries($sh_pr_method_id, $countries){
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_shipping_method_price_countries` WHERE `sh_pr_method_id` = '" . $db->escape($sh_pr_method_id) . "'";
        $db->setQuery($query);
        $db->execute();
        if (!is_array($countries)) return 0;
        foreach($countries as $key => $value){
            $query = "INSERT INTO `#__jshopping_shipping_method_price_countries`
                      SET `country_id` = '" . $db->escape($value) . "', `sh_pr_method_id` = '" . $db->escape($sh_pr_method_id) . "'";
            $db->setQuery($query);
            $db->execute();
        }
    }

    function savePrices($sh_pr_method_id, $array_post) {
        $db = \JFactory::getDBO();

        $query = "DELETE FROM `#__jshopping_shipping_method_price_weight` WHERE `sh_pr_method_id` = '".$db->escape($sh_pr_method_id)."'";
        $db->setQuery($query);
        $db->execute();

        if (!isset($array_post['shipping_price']) || !is_array($array_post['shipping_price'])) return 0;

        foreach($array_post['shipping_price'] as $key => $value){
            if(!$array_post['shipping_weight_from'][$key] && !$array_post['shipping_weight_to'][$key]){
                continue;
            }
            $sh_method = \JSFactory::getTable('shippingMethodPriceWeight');
            $sh_method->sh_pr_method_id = $sh_pr_method_id;
            $sh_method->shipping_price = \JSHelper::saveAsPrice($array_post['shipping_price'][$key]);
            $sh_method->shipping_package_price = \JSHelper::saveAsPrice($array_post['shipping_package_price'][$key]);
            $sh_method->shipping_weight_from = \JSHelper::saveAsPrice($array_post['shipping_weight_from'][$key]);
            $sh_method->shipping_weight_to = \JSHelper::saveAsPrice($array_post['shipping_weight_to'][$key]);
            if (!$sh_method->store()) {
                \JSError::raiseWarning("", "Error saving to database" . $sh_method->_db->stderr());
            }
        }
    }

    function deletePriceWeight($sh_pr_weight_id) {
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_shipping_method_price_weight` WHERE `sh_pr_weight_id` = '".$db->escape($sh_pr_weight_id)."'";
        $db->setQuery($query);
        $db->execute();
    }

	function getListNameShippings($publish = 1){
        $_list = $this->getAllShippings($publish);
        $list = array();
        foreach($_list as $v){
            $list[$v->shipping_id] = $v->name;
        }
        return $list;
    }

    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        if (!isset($post['published'])){
            $post['published'] = 0;
        }
        if (!$post['listpayments']){
            $post['listpayments'] = array();
        }
        $languages = \JSFactory::getModel("languages")->getAllLanguages(1);
        foreach($languages as $lang){
            $post['description_'.$lang->language] = $input->get('description'.$lang->id, '', 'RAW');
        }
        return $post;
    }

    public function save(array $post){
        $shipping = \JSFactory::getTable('shippingMethod');
        $shipping->setPayments($post['listpayments']);
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveShipping', array(&$post));
		$shipping->bind($post);
        if (!$shipping->shipping_id){
            $shipping->ordering = $this->getMaxOrdering() + 1;
        }
		$shipping->setParams($post['s_params']);
		if (!$shipping->store()){
			$this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE')." ".$shipping->getError());
			return 0;
		}
        $dispatcher->triggerEvent('onAfterSaveShipping', array(&$shipping));
        return $shipping;
    }

    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveShipping', array(&$cid));
		foreach($cid as $id){
			$this->delete($id);
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_SHIPPING_DELETED'), 'message');
            }
		}
        $dispatcher->triggerEvent('onAfterRemoveShipping', array(&$cid));
    }

    public function delete(&$id) {
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_shipping_method` WHERE `shipping_id`=".(int)$id;
        $db->setQuery($query);
        if ($db->execute()) {
            $query = "SELECT `sh_pr_method_id` FROM `#__jshopping_shipping_method_price` WHERE `shipping_method_id`=".(int)$id;
            $db->setQuery($query);
            $sh_pr_ids = $db->loadObjectList();

            foreach($sh_pr_ids as $value2){
                $query = "DELETE FROM `#__jshopping_shipping_method_price_weight` WHERE `sh_pr_method_id` = '".$db->escape($value2->sh_pr_method_id)."'";
                $db->setQuery($query);
                $db->execute();

                $query = "DELETE FROM `#__jshopping_shipping_method_price_countries` WHERE `sh_pr_method_id` = '".$db->escape($value2->sh_pr_method_id)."'";
                $db->setQuery($query);
                $db->execute();
            }

            $query = "DELETE FROM `#__jshopping_shipping_method_price` WHERE `shipping_method_id`=".(int)$id;
            $db->setQuery($query);
            $db->execute();
        }
    }
    
    public function publish(array $cid, $flag){
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishShipping', array(&$cid, &$flag));
		$obj = \JSFactory::getTable('shippingMethod');
        $obj->publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishShipping', array(&$cid, &$flag));
    }

}