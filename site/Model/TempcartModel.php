<?php
/**
* @version      5.3.2 10.02.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Uri\Uri;
defined('_JEXEC') or die();

class TempCartModel{
    
    public $savedays = 365;
	public $load_product_temp_cart_type = array('wishlist');
    
    function __construct(){
        PluginHelper::importPlugin('jshoppingcheckout');
		$obj = $this;
        Factory::getApplication()->triggerEvent('onConstructJshopTempCart', array(&$obj));
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
		$id_cookie = $this->getIdTempCart($cart->type_cart);
		if (!count($cart->products)) {
			$this->delete($id_cookie, $cart->type_cart);
			return 1;
		}
		$this->save($id_cookie, $cart->type_cart, $cart->products);
        return 1;
    }
	
	function save($id_cookie, $type, $products) {
		$user_id = (int)Factory::getUser()->id;
		$id = $this->getIdByCookieId($id_cookie, $type);
		if (!$user_id) {
			$user_id = $this->getUserIdByCookieId($id_cookie, $type);
		}
		$table = JSFactory::getTable('tempcart');
		$table->id = $id;
		if ($user_id) {
			$table->user_id = $user_id;
		}
		$table->id_cookie = $id_cookie;
		$table->cart = serialize($products);
		$table->type_cart = $type;
		$table->store();
		$id = $table->id;
		Factory::getApplication()->triggerEvent('onAfterSaveTempCart', array(&$table));
	}
	
	function delete($id_cookie, $type){
		$user_id = (int)Factory::getUser()->id;
		if (!$user_id) {
			$user_id = $this->getUserIdByCookieId($id_cookie, $type);
		}
		$this->deleteRow($id_cookie, $type);
		if ($user_id) {
			$this->deleteRowByUserId($user_id, $type);
		}
	}

	function deleteRow($id_cookie, $type) {
		$db = Factory::getDBO();
		$query = "DELETE FROM `#__jshopping_cart_temp` WHERE `id_cookie`=".$db->q($id_cookie)." AND `type_cart`=".$db->q($type);
        $db->setQuery($query);
        return $db->execute();
	}

	function deleteRowByUserId($user_id, $type) {
		$db = Factory::getDBO();
		$query = "DELETE FROM `#__jshopping_cart_temp` WHERE `user_id`=".$db->q($user_id)." AND `type_cart`=".$db->q($type);
		$db->setQuery($query);
		$db->execute();
	}
	
	function getProducts($type){
		return $this->getTempCart($this->getIdTempCart($type), $type);
	}
	
	function setIdTempCart($id_cookie, $type){
		$patch = "/";
        if (Uri::base(true) != ""){
			$patch = Uri::base(true);
		}
		$time = time() + 3600 * 24 * $this->savedays;
		$keyname = $this->getCookieKeyName($type);
		setcookie($keyname, $id_cookie, $time, $patch);
		$_COOKIE[$keyname] = $id_cookie;
	}

	function getCookieKeyName($type) {
		if ($type == 'wishlist') {
			return 'jshopping_temp_cart';
		} else {
			return 'jshopping_temp_cart_'.$type;
		}
	}
	
	function getIdTempCart($type){
		$keyname = $this->getCookieKeyName($type);
		$id_cookie = isset($_COOKIE[$keyname]) ? (string)$_COOKIE[$keyname] : '';
		if (!$id_cookie) {
			$id_cookie = $this->getUniqId();
			$this->setIdTempCart($id_cookie, $type);
		}
		return $id_cookie;
	}

    function getTempCart($id_cookie, $type_cart = "wishlist"){
        $db = Factory::getDBO();
        $query = "SELECT `cart`, `user_id` FROM `#__jshopping_cart_temp`
                  WHERE `id_cookie` = ".$db->q($id_cookie)." AND `type_cart`=".$db->q($type_cart)." LIMIT 0,1";
        $db->setQuery($query);
        $row = $db->loadObject();
		if (isset($row->cart)) {
			$products = unserialize($row->cart);
		} else {
			$products = [];
		}
		$user_id = (int)Factory::getUser()->id;
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
					$new_id_cookie = $v->id_cookie;
				}
			}
			if (isset($new_id_cookie)) {
				$this->setIdTempCart($new_id_cookie, $type_cart);
				$this->save($new_id_cookie, $type_cart, $products);
				$this->deleteRow($id_cookie, $type_cart);
			}
		}
		return $products;
    }

	function getListRowsByUserId($user_id, $type_cart = "wishlist") {
		$db = Factory::getDBO();
        $query = "SELECT * FROM `#__jshopping_cart_temp`
                  WHERE `user_id`=".$db->q($user_id)." AND `type_cart`=".$db->q($type_cart);
        $db->setQuery($query);
        return $db->loadObjectList();
	}

	function getIdByCookieId($id_cookie, $type_cart = 'wishlist'){
		$db = Factory::getDBO();
        $query = "SELECT id FROM `#__jshopping_cart_temp`
                  WHERE `id_cookie`=".$db->q($id_cookie)." AND `type_cart`=".$db->q($type_cart)." LIMIT 0,1";
        $db->setQuery($query);
        return (int)$db->loadResult();
	}

	function getUserIdByCookieId($id_cookie, $type_cart = 'wishlist'){
		$db = Factory::getDBO();
        $query = "SELECT user_id FROM `#__jshopping_cart_temp`
                  WHERE `id_cookie`=".$db->q($id_cookie)." AND `type_cart`=".$db->q($type_cart)." LIMIT 0,1";
        $db->setQuery($query);
        return (int)$db->loadResult();
	}
}