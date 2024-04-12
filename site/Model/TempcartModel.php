<?php
/**
* @version      5.3.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class TempCartModel{
    
    public $savedays = 365;
	public $load_product_temp_cart_type = array('wishlist');
    
    function __construct(){
        \JPluginHelper::importPlugin('jshoppingcheckout');
		$obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshopTempCart', array(&$obj));
    }
	
	function checkAccessToTempCart($type_cart){	
		if (!in_array($type_cart, $this->load_product_temp_cart_type)){
            return 0;
        }else{
			return 1;
		}
	}
	
	function getUniqId(){
		return session_id();
	}
    
    function insertTempCart($cart){
        if (!$this->checkAccessToTempCart($cart->type_cart)){
            return 0;
        }
		$id_cookie = $this->getIdTempCart();
		if (!$id_cookie) {
			$id_cookie = $this->getUniqId();
			$this->setIdTempCart($id_cookie);
		}		
		if (!count($cart->products)) {
			$this->delete($id_cookie, $cart->type_cart);
			return 1;
		}
		$this->save($id_cookie, $cart->type_cart, $cart->products);
        return 1;
    }
	
	function save($id_cookie, $type, $products) {
		$user_id = (int)\JFactory::getUser()->id;
		$id = $this->getIdByCookieId($id_cookie, $type);
		if (!$user_id) {
			$user_id = $this->getUserIdByCookieId($id_cookie, $type);
		}
		$table = \JSFactory::getTable('tempcart');
		$table->id = $id;
		if ($user_id) {
			$table->user_id = $user_id;
		}
		$table->id_cookie = $id_cookie;
		$table->cart = serialize($products);
		$table->type_cart = $type;
		$table->store();
		$id = $table->id;
		if ($user_id) {
			$rows = $this->getListRowsByUserId($user_id, $type);
			foreach($rows as $v) {
				if ($v->id != $id) {
					$table = \JSFactory::getTable('tempcart');
					$table->id = $v->id;
					$table->cart = serialize($products);
					$table->store();
				}
			}
		}
	}
	
	function delete($id_cookie, $type){
		$user_id = (int)\JFactory::getUser()->id;
		if (!$user_id) {
			$user_id = $this->getUserIdByCookieId($id_cookie, $type);
		}

		$db = \JFactory::getDBO();
		$query = "DELETE FROM `#__jshopping_cart_temp` WHERE `id_cookie`=".$db->q($id_cookie)." AND `type_cart`=".$db->q($type);
        $db->setQuery($query);
        $db->execute();
		if ($user_id) {
			$query = "DELETE FROM `#__jshopping_cart_temp` WHERE `user_id`=".$db->q($user_id)." AND `type_cart`=".$db->q($type);
			$db->setQuery($query);
			$db->execute();
		}
	}
	
	function getProducts($type){
		return $this->getTempCart($this->getIdTempCart(), $type);
	}
	
	function setIdTempCart($id_cookie){
		$patch = "/";
        if (\JURI::base(true) != ""){
			$patch = \JURI::base(true);
		}
		$time = time() + 3600 * 24 * $this->savedays;
		setcookie('jshopping_temp_cart', $id_cookie, $time, $patch);
	}
	
	function getIdTempCart(){
		return isset($_COOKIE['jshopping_temp_cart']) ? (string)$_COOKIE['jshopping_temp_cart'] : '';
	}

    function getTempCart($id_cookie, $type_cart = "wishlist"){
        $db = \JFactory::getDBO();
        $query = "SELECT `cart`, `user_id` FROM `#__jshopping_cart_temp`
                  WHERE `id_cookie` = ".$db->q($id_cookie)." AND `type_cart`=".$db->q($type_cart)." LIMIT 0,1";
        $db->setQuery($query);
        $row = $db->loadObject();
		if (isset($row->cart)) {
			$products = unserialize($row->cart);
		} else {
			$products = [];
		}
		$user_id = (int)\JFactory::getUser()->id;
		if (!$user_id && isset($row->user_id)) {
			$user_id = $row->user_id;
		}
		if ($user_id) {
			$exist_pid = array_map(function($item){
				return $item['product_id'].'-'.$item['attributes'].'-'.$item['freeattributes'];
			}, $products);
			$rows = $this->getListRowsByUserId($user_id, $type_cart);
			foreach($rows as $v) {
				if ($v->id_cookie != $id_cookie && $v->cart) {
					$_products = unserialize($v->cart);
					foreach($_products as $_prod) {
						$check = $_prod['product_id'].'-'.$_prod['attributes'].'-'.$_prod['freeattributes'];
						if (!in_array($check, $exist_pid)) {
							$products[]	= $_prod;
							$exist_pid[] = $check;
						}
					}
				}
			}
		}
		return $products;
    }

	function getListRowsByUserId($user_id, $type_cart = "wishlist") {
		$db = \JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_cart_temp`
                  WHERE `user_id`=".$db->q($user_id)." AND `type_cart`=".$db->q($type_cart);
        $db->setQuery($query);
        return $db->loadObjectList();
	}

	function getIdByCookieId($id_cookie, $type_cart = 'wishlist'){
		$db = \JFactory::getDBO();
        $query = "SELECT id FROM `#__jshopping_cart_temp`
                  WHERE `id_cookie`=".$db->q($id_cookie)." AND `type_cart`=".$db->q($type_cart)." LIMIT 0,1";
        $db->setQuery($query);
        return (int)$db->loadResult();
	}

	function getUserIdByCookieId($id_cookie, $type_cart = 'wishlist'){
		$db = \JFactory::getDBO();
        $query = "SELECT user_id FROM `#__jshopping_cart_temp`
                  WHERE `id_cookie`=".$db->q($id_cookie)." AND `type_cart`=".$db->q($type_cart)." LIMIT 0,1";
        $db->setQuery($query);
        return (int)$db->loadResult();
	}
}