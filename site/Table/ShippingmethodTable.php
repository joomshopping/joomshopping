<?php
/**
* @version      5.8.0 09.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

class ShippingMethodTable extends MultilangTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_shipping_method', 'shipping_id', $_db);
    }
    
    function loadFromAlias($alias){
        $db = Factory::getDBO();
        $query = "SELECT shipping_id FROM `#__jshopping_shipping_method` WHERE `alias`='".$db->escape($alias)."'";
        extract(Helper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $id = $db->loadResult();
        return $this->load($id);
    }
    
    function getAllShippingMethods($publish = 1) {
        $db = Factory::getDBO();
        $user = Factory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $query_where = 'WHERE `access` IN ('.$groups.')';
        if ($publish) {
            $query_where .= " AND published=1 ";
        }
        $lang = JSFactory::getLang();
        $query = "SELECT shipping_id, `".$lang->get('name')."` as name, `".$lang->get("description")."` as description, published, ordering
                  FROM `#__jshopping_shipping_method` 
                  $query_where 
                  ORDER BY ordering";
		extract(Helper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    function getAllShippingMethodsCountry($country_id, $payment_id, $publish = 1){
        $db = Factory::getDBO(); 
        $lang = JSFactory::getLang();
		$JshopConfig = JSFactory::getConfig();        
        $user = Factory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $query_where = 'AND sh_method.`access` IN ('.$groups.')';
        if ($publish) {
            $query_where .= " AND sh_method.published=1 ";
        }
		if ($payment_id && $JshopConfig->step_4_3 == 0) {
			$query_where.= " AND (sh_method.payments='' OR FIND_IN_SET(".$payment_id.", sh_method.payments) ) ";
		}
        $query = "SELECT *, sh_method.`".$lang->get("name")."` as name, `".$lang->get("description")."` as description FROM `#__jshopping_shipping_method` AS sh_method
                  INNER JOIN `#__jshopping_shipping_method_price` AS sh_pr_method ON sh_method.shipping_id = sh_pr_method.shipping_method_id
                  INNER JOIN `#__jshopping_shipping_method_price_countries` AS sh_pr_method_country ON sh_pr_method_country.sh_pr_method_id = sh_pr_method.sh_pr_method_id
                  INNER JOIN `#__jshopping_countries` AS countries  ON sh_pr_method_country.country_id = countries.country_id
                  WHERE countries.country_id = '".$db->escape($country_id)."' $query_where
                  ORDER BY sh_method.ordering";
		extract(Helper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        return $db->loadObJectList();
    }
    
    function getShippingPriceId($shipping_id, $country_id, $publish = 1){
        $db = Factory::getDBO(); 
        $query_where = ($publish) ? ("AND sh_method.published = '1'") : ("");
        $query = "SELECT sh_pr_method.sh_pr_method_id FROM `#__jshopping_shipping_method` AS sh_method
                  INNER JOIN `#__jshopping_shipping_method_price` AS sh_pr_method ON sh_method.shipping_id = sh_pr_method.shipping_method_id
                  INNER JOIN `#__jshopping_shipping_method_price_countries` AS sh_pr_method_country ON sh_pr_method_country.sh_pr_method_id = sh_pr_method.sh_pr_method_id
                  INNER JOIN `#__jshopping_countries` AS countries  ON sh_pr_method_country.country_id = countries.country_id
                  WHERE countries.country_id = '".$db->escape($country_id)."' and sh_method.shipping_id=".intval($shipping_id)."  $query_where";
        extract(Helper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        return (int)$db->loadResult();
    }
    
    function getPayments(){
		extract(Helper::Js_add_trigger(get_defined_vars()));
        if ($this->payments==""){
            return array();
        }else{
            return explode(",", $this->payments);
        }
    }
    
    function setPayments($payments){
        $payments = (array)$payments;
        foreach($payments as $v){
            if ($v==0){
                $payments = array();
                break;
            }
        }
		extract(Helper::Js_add_trigger(get_defined_vars()));
        $this->payments = implode(",", $payments);
    }
	
	function setParams($params){
        $this->params = serialize($params);
    }
    
    function getParams(){        
        if ($this->params==""){
            return array();
        }else{
            return unserialize($this->params);
        }
    }
    
    function getShippingForm($alias = null){
        if (is_null($alias)){
            $alias = $this->alias;
        }
        $JshopConfig = JSFactory::getConfig();
        $script = str_replace(array('.','/'),'', $alias ?? '');
        $patch = $JshopConfig->path.'shippingform/'.$script."/".$script.'.php';
        if ($script!='' && file_exists($patch)){
            include_once($patch);
            $data = new $script();
        }else{
            $data = null;
        }
        return $data;
    }
    
    function loadShippingForm($shipping_id, $shippinginfo, $params){
        $shippingForm = $this->getShippingForm($shippinginfo->alias);
        $html = "";
        if ($shippingForm){
            ob_start();
            $shippingForm->showForm($shipping_id, $shippinginfo, $params);
            $html = ob_get_contents();
            ob_get_clean();
        }
        return $html;
    }

    function loadShippingFormAdmin($order, $shipping_id, $shippinginfo, $params){
        $shippingForm = $this->getShippingForm($shippinginfo->alias);
        $html = "";
        if ($shippingForm){
            ob_start();
            $shippingForm->showFormAdmin($order, $shipping_id, $shippinginfo, $params);
            $html = ob_get_contents();
            ob_get_clean();
        }
        return $html;
    }

}