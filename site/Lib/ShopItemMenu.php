<?php
/**
* @version      5.0.8 03.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;
defined('_JEXEC') or die();

class ShopItemMenu{
    static private $instance = [];
    public $list = null;
    public $list_product = null;
    public $list_category = null;
    public $list_manufacturer = null;
    public $list_content = null;
    public $cart = null;
    public $wishlist = null;
    public $search = null;
    public $user = null;
    public $vendor = null;
    public $shop = null;
    public $manufacturer = null;
    public $products = null;
    public $checkout = null;
    public $login = null;
    public $logout = null;
    public $editaccount = null;
    public $orders = null;
    public $register = null;
	public $lang = null;

    static function getInstance($lang = ''){
		if (!$lang){
			$lang = \JSFactory::getConfig()->getLang();
		}
        if (!isset(self::$instance[$lang])){
            self::$instance[$lang] = new ShopItemMenu();
            self::$instance[$lang]->init($lang);
        }
        return self::$instance[$lang];
    }
    
    function init($lang = ''){
		$this->lang = $lang;
        $list = $this->getList();
        $this->list_product = [];
        $this->list_category = [];
        $this->list_manufacturer = [];
        $this->list_content = [];
        $this->cart = 0;
        $this->wishlist = 0;
        $this->search = 0;
        $this->user = 0;
        $this->vendor = 0;
        $this->shop = 0;
        $this->manufacturer = 0;
        $this->products = 0;
        $this->checkout = 0;
        $this->login = 0;
        $this->logout = 0;
        $this->editaccount = 0;
        $this->orders = 0;
        $this->register = 0;

        foreach($list as $k=>$v){
            $data = $v->data;
            if (!isset($data['controller']) && isset($data['view'])){
                $data['controller'] = $data['view'];
                unset($data['view']);
                unset($data['layout']);
            }
            if (count($data)==4 && $data['controller']=="product" && isset($data['task']) && $data['task']=="view" && isset($data['category_id']) && isset($data['product_id'])){
                $this->list_product[$data['product_id']] = $v->id;
            }
            if (count($data)==3 && $data['controller']=="category" && $data['task']=="view" && $data['category_id']){
                $this->list_category[$data['category_id']] = $v->id;
            }
            if (count($data)==3 && $data['controller']=="manufacturer" && $data['task']=="view" && $data['manufacturer_id']){
                $this->list_manufacturer[$data['manufacturer_id']] = $v->id;
            }
            if (count($data)==3 && $data['controller']=="content" && $data['task']=="view" && $data['page']){
                $this->list_content[$data['page']] = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="login"){
                $this->login = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="logout"){
                $this->logout = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="editaccount"){
                $this->editaccount = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="orders"){
                $this->orders = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="register"){
                $this->register = $v->id;
            }
            if ($data['controller']=="cart"){
                $this->cart = $v->id;
            }
            if ($data['controller']=="wishlist"){
                $this->wishlist = $v->id;
            }
            if ($data['controller']=="search"){
                $this->search = $v->id;
            }
            if ($data['controller']=="category" && count($data)==1){
                $this->shop = $v->id;
            }
            if ($data['controller']=="manufacturer" && count($data)==1){
                $this->manufacturer = $v->id;
            }
            if ($data['controller']=="products" && count($data)==1){
                $this->products = $v->id;
            }
            if ($data['controller']=="user" && count($data)==1){
                $this->user = $v->id;
            }
            if ($data['controller']=="vendor" && count($data)==1){
                $this->vendor = $v->id;
            }
            if ($data['controller']=="checkout"){
                $this->checkout = $v->id;
            }            
        }
    }
    
    function getList(){
        if (!is_array($this->list)){
            $jshopConfig = \JSFactory::getConfig();
            $user = \JFactory::getUser();
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $db = \JFactory::getDBO();
            $query = "select id,link from #__menu 
			          where `type`='component' and published=1 and link like '%option=com_jshopping%' and client_id=0 
					  and (language='*' or language='".$db->escape($this->lang)."') and access IN (".$groups.")";
            $db->setQuery($query);
            $this->list = $db->loadObjectList();
            foreach($this->list as $k=>$v){
                $data = [];
                $v->link = str_replace("index.php?option=com_jshopping&","",$v->link);
                $tmp = explode('&', $v->link);
                foreach($tmp as $k2=>$v2){
                    $tmp2 = explode("=", $v2);
                    if ($tmp2[1]!=""){
                        $data[$tmp2[0]] = $tmp2[1];
                    }
                }
                $this->list[$k]->data = $data;
            }
        }
    return $this->list;
    }

    function getListProduct(){
    return $this->list_product;
    }
    
    function getListCategory(){
    return $this->list_category;
    }
    
    function getListManufacturer(){
    return $this->list_manufacturer;
    }
    
    function getListContent(){
    return $this->list_content;
    }
    
    function getCart(){
    return $this->cart;
    }
    
    function getWishlist(){
    return $this->wishlist;
    }
    
    function getSearch(){
    return $this->search;
    }
    
    function getUser(){
    return $this->user;
    }
    
    function getLogin(){
    return $this->login;
    }
    
    function getLogout(){
    return $this->logout;
    }
    
    function getEditaccount(){
    return $this->editaccount;
    }
    
    function getOrders(){
    return $this->orders;
    }
    
    function getRegister(){
    return $this->register;
    }

    function getVendor(){
    return $this->vendor;
    }
    
    function getShop(){
    return $this->shop;
    }
    
    function getManufacturer(){
    return $this->manufacturer;
    }
    
    function getProducts(){
    return $this->products;
    }
    
    function getCheckout(){
    return $this->checkout;
    }

    function getItemIdFromQuery($query){
        $Itemid = 0;
		if (!isset($query['controller'])) {
			$query['controller'] = null;
		}
		if (!isset($query['task'])) {
			$query['task'] = null;
		}
        if ($query['controller']=="category" && $query['task']=="view" && $query['category_id']){
            $categoryitemidlist = $this->getListCategory();
			if (isset($categoryitemidlist[$query['category_id']])) {
				$Itemid = $categoryitemidlist[$query['category_id']];
			}
        }
        if ($query['controller']=="product" && $query['task']=="view" && $query['category_id'] && $query['product_id']){
            $productitemidlist = $this->getListProduct();
            if (isset($productitemidlist[$query['product_id']])) {
                $Itemid = $productitemidlist[$query['product_id']];
            }
            if (!$Itemid) {
                $categoryitemidlist = $this->getListCategory();
                $prodalias = \JSFactory::getAliasProduct($this->lang);
                if (isset($categoryitemidlist[$query['category_id']]) && isset($prodalias[$query['product_id']])){
                    $Itemid = $categoryitemidlist[$query['category_id']];
                }
            }
        }
        if ($query['controller']=="manufacturer" && $this->getManufacturer()){
            $Itemid = $this->getManufacturer();
        }
        if ($query['controller']=="manufacturer" && $query['task']=="view" && $query['manufacturer_id']){         
            $manufactureritemidlist = $this->getListManufacturer();
			if (isset($manufactureritemidlist[$query['manufacturer_id']])){
				$Itemid = $manufactureritemidlist[$query['manufacturer_id']];
            }
        }
        if ($query['controller']=="content" && $query['task']=="view" && $query['page']){
            $contentitemidlist = $this->getListContent();
			if (isset($contentitemidlist[$query['page']])){
				$Itemid = $contentitemidlist[$query['page']];
			}
		}
		if ($query['controller']=="cart" && $this->getCart()){
			$Itemid = $this->getCart();
		}
		if ($query['controller']=="wishlist" && $this->getWishlist()){
			$Itemid = $this->getWishlist();
		}
		if ($query['controller']=="search" && $this->getSearch()){
			$Itemid = $this->getSearch();
		}
        if ($query['controller']=="user" && $this->getUser()){
			$Itemid = $this->getUser();
		}
		if ($query['controller']=="user" && $query['task']=="login" && $this->getLogin()){
			$Itemid = $this->getLogin();
		}
		if ($query['controller']=="user" && $query['task']=="logout" && $this->getLogout()){
			$Itemid = $this->getLogout();
		}
		if ($query['controller']=="user" && $query['task']=="editaccount" && $this->getEditaccount()){
			$Itemid = $this->getEditaccount();
		}
		if ($query['controller']=="user" && $query['task']=="orders" && $this->getOrders()){
			$Itemid = $this->getOrders();
		}
		if ($query['controller']=="user" && $query['task']=="register" && $this->getRegister()){
			$Itemid = $this->getRegister();
		}		
		if ($query['controller']=="vendor" && $this->getVendor()){
			$Itemid = $this->getVendor();
		}
		if ($query['controller']=="checkout" && $this->getCheckout()){
			$Itemid = $this->getCheckout();
		}
        return $Itemid;
    }
}