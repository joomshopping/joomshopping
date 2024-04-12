<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
use Joomla\Component\Jshopping\Site\Lib\ShopItemMenu;
defined('_JEXEC') or die();

class Metadata{
	
	public static function metaData($alias, $loadParams = 1, $default_title = '', $path_way = '', $external_params = null){
		if ($path_way!=''){
			\JSHelper::appendPathWay($path_way);
		}
		if ($loadParams && is_null($external_params)){
			$params = \JFactory::getApplication()->getParams();
		}else{
			$params = null;
		}
		if ($external_params){
			$params = $external_params;
		}
		$seo = \JSFactory::getTable("seo");
        $seodata = $seo->loadData($alias);
		if ($seodata->title==""){
            $seodata->title = $default_title;
        }
        \JSHelper::setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
		return $seodata;
	}
	
	public static function metaDataDependenMainPageShop($alias, $loadParams = 1, $default_title = '', $path_way = ''){
		if (\JSHelper::getThisURLMainPageShop()){
			$params = 0;
			$title = $default_title;
			$path = $path_way ;
        }else{
			$params = $loadParams;
			$title = '';
			$path = '';
        }
		return self::metaData($alias, $params, $title, $path);
	}
	
	public static function mainCategory($category, $params){
		\JSHelper::setMetaData($category->meta_title, $category->meta_keyword, $category->meta_description, $params);
	}
	
	public static function category($category){
		if (\JSHelper::getShopMainPageItemid()==\JFactory::getApplication()->input->getInt('Itemid')){
            \JSHelper::appendExtendPathWay($category->getTreeChild(), 'category');
        }
        if ($category->meta_title=="") $category->meta_title = $category->name;
        \JSHelper::setMetaData($category->meta_title, $category->meta_keyword, $category->meta_description);
	}
	
	public static function cart(){
		self::metaDataDependenMainPageShop('cart', 1, \JText::_('JSHOP_CART'), \JText::_('JSHOP_CART'));
	}
	
	public static function checkoutAddress(){
		self::metaData("checkout-address", 0, \JText::_('JSHOP_CHECKOUT_ADDRESS'), \JText::_('JSHOP_CHECKOUT_ADDRESS'));
	}
	
	public static function checkoutPayment(){		
		self::metaData("checkout-payment", 0, \JText::_('JSHOP_CHECKOUT_PAYMENT'), \JText::_('JSHOP_CHECKOUT_PAYMENT'));
	}
	
	public static function checkoutShipping(){		
		self::metaData("checkout-shipping", 0, \JText::_('JSHOP_CHECKOUT_SHIPPING'), \JText::_('JSHOP_CHECKOUT_SHIPPING'));
	}
	
	public static function checkoutPreview(){		
		self::metaData("checkout-preview", 0, \JText::_('JSHOP_CHECKOUT_PREVIEW'), \JText::_('JSHOP_CHECKOUT_PREVIEW'));
	}
	
	public static function checkoutFinish(){
		$document = \JFactory::getDocument();
        $document->setTitle(\JText::_('JSHOP_CHECKOUT_FINISH'));
        \JSHelper::appendPathWay(\JText::_('JSHOP_CHECKOUT_FINISH'));
	}
	
	public static function content($page){		
		switch($page){
            case 'agb':
                $title = \JText::_('JSHOP_AGB');
            break;
            case 'return_policy':
                $title = \JText::_('JSHOP_RETURN_POLICY');
            break;
            case 'shipping':
                $title = \JText::_('JSHOP_SHIPPING');
            break;
            case 'privacy_statement':
                $title = \JText::_('JSHOP_PRIVACY_STATEMENT');
            break;
        }		
		if (\JSHelper::getThisURLMainPageShop()){
			$pathway = $title;
			$loadParams = 0;
		}else{
			$pathway = '';
			$loadParams = 1;
		}
		return self::metaData("content-".$page, $loadParams, $title, $pathway);
	}
	
	public static function listManufacturers($params){
		self::metaData("manufacturers", 0, '', '', $params);
	}
	
	public static function manufacturer($manufacturer){
		if (\JSHelper::getShopManufacturerPageItemid()==\JFactory::getApplication()->input->getInt('Itemid')){
            \JSHelper::appendPathWay($manufacturer->name);
        }
        if ($manufacturer->meta_title=="") $manufacturer->meta_title = $manufacturer->name;
        \JSHelper::setMetaData($manufacturer->meta_title, $manufacturer->meta_keyword, $manufacturer->meta_description);
	}
	
	public static function search(){		
		self::metaDataDependenMainPageShop('search', 1, \JText::_('JSHOP_SEARCH'), \JText::_('JSHOP_SEARCH'));
	}
	
	public static function searchResult(){
		self::metaDataDependenMainPageShop('search-result', 1, \JText::_('JSHOP_SEARCH'), \JText::_('JSHOP_SEARCH'));
	}
	
	public static function userLogin(){
		self::metaDataDependenMainPageShop('login', 1, \JText::_('JSHOP_LOGIN'), \JText::_('JSHOP_LOGIN'));
	}
	
	public static function userRegister(){
		self::metaDataDependenMainPageShop('register', 1, \JText::_('JSHOP_REGISTRATION'), \JText::_('JSHOP_REGISTRATION'));
	}
	
	public static function userEditaccount(){
		if (ShopItemMenu::getInstance()->getEditaccount() != \JFactory::getApplication()->input->getInt('Itemid')){
			$pathway = \JText::_('JSHOP_EDIT_DATA');
		}else{
			$pathway = '';
		}
		self::metaData("editaccount", 0, \JText::_('JSHOP_EDIT_DATA'), $pathway);
	}
	
	public static function userOrders(){
		if (ShopItemMenu::getInstance()->getOrders() != \JFactory::getApplication()->input->getInt('Itemid')){
			$path_way = \JText::_('JSHOP_MY_ORDERS');
		}else{
			$path_way = '';
		}
		self::metaData("myorders", 0, \JText::_('JSHOP_MY_ORDERS'), $path_way);
	}
	
	public static function userOrder($order){
		$jshopConfig = \JSFactory::getConfig();        
		self::metaData("myorder-detail", 0, \JText::_('JSHOP_MY_ORDERS'));
		$shim = ShopItemMenu::getInstance();
		if ($shim->getOrders()!=\JFactory::getApplication()->input->getInt('Itemid')){
			\JSHelper::appendPathWay(\JText::_('JSHOP_MY_ORDERS'), \JSHelper::SEFLink('index.php?option=com_jshopping&controller=user&task=orders', 0, 0, $jshopConfig->use_ssl));
		}
        \JSHelper::appendPathWay(\JText::_('JSHOP_ORDER_NUMBER').": ".$order->order_number);
	}
	
	public static function userMyaccount(){
		if (ShopItemMenu::getInstance()->getUser() != \JFactory::getApplication()->input->getInt('Itemid')){
			$pathway = \JText::_('JSHOP_MY_ACCOUNT');
		}else{
			$pathway = '';
		}
		self::metaData("myaccount", 0, \JText::_('JSHOP_MY_ACCOUNT'), $pathway);
	}
	
	public static function userGroupsinfo(){
		\JSHelper::setMetaData(\JText::_('JSHOP_USER_GROUPS_INFO'), "", "");
	}
	
	public static function listVendors(){
		self::metaData("vendors");
	}
	
	public static function vendorInfo($vendor){
		$title =  $vendor->shop_name;
		return self::metaData("vendor-info-".$vendor->id, 0, $title, $title);
	}
	
	public static function vendorProducts($vendor){
		$title =  $vendor->shop_name;
		return self::metaData("vendor-product-".$vendor->id, 0, $title, $title);
	}
	
	public static function wishlist(){
		self::metaDataDependenMainPageShop('wishlist', 1, \JText::_('JSHOP_WISHLIST'), \JText::_('JSHOP_WISHLIST'));
	}
	
	public static function product($category, $product){
		$app = \JFactory::getApplication();
		$Itemid = $app->input->getInt('Itemid');
		if (\JSHelper::getShopMainPageItemid()==$Itemid){
            \JSHelper::appendExtendPathWay($category->getTreeChild(), 'product');
        }
		$menu = $app->getMenu();
		$menuItem = $menu->getItem($Itemid);        
		if (isset($menuItem->query['view']) && $menuItem->query['view']!='product'){
			\JSHelper::appendPathWay($product->name);
		}
        if ($product->meta_title=="") $product->meta_title = $product->name;
        \JSHelper::setMetaData($product->meta_title, $product->meta_keyword, $product->meta_description);
	}
	
	public static function allProducts(){
		self::metaData("all-products");
	}
	
	public static function productsTophits(){		
		self::metaData("tophitsproducts");
	}
	
	public static function productsToprating(){		
		self::metaData("topratingproducts");
	}
	
	public static function productsLabel(){		
		self::metaData("labelproducts");
	}
	
	public static function productsBestseller(){		
		self::metaData("bestsellerproducts");
	}
	
	public static function productsRandom(){		
		self::metaData("randomproducts");
	}
	
	public static function productsLast(){		
		self::metaData("lastproducts");
	}

}