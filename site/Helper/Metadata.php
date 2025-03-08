<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\ShopItemMenu;
defined('_JEXEC') or die();

class Metadata{
	
	public static function metaData($alias, $loadParams = 1, $default_title = '', $path_way = '', $external_params = null){
		if ($path_way!=''){
			Helper::appendPathWay($path_way);
		}
		if ($loadParams && is_null($external_params)){
			$params = Factory::getApplication()->getParams();
		}else{
			$params = null;
		}
		if ($external_params){
			$params = $external_params;
		}
		$seo = JSFactory::getTable("seo");
        $seodata = $seo->loadData($alias);
		if ($seodata->title==""){
            $seodata->title = $default_title;
        }
        Helper::setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
		return $seodata;
	}
	
	public static function metaDataDependenMainPageShop($alias, $loadParams = 1, $default_title = '', $path_way = ''){
		if (Helper::getThisURLMainPageShop()){
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
		Helper::setMetaData($category->meta_title, $category->meta_keyword, $category->meta_description, $params);
	}
	
	public static function category($category){
		if (Helper::getShopMainPageItemid()==Factory::getApplication()->input->getInt('Itemid')){
            Helper::appendExtendPathWay($category->getTreeChild(), 'category');
        }
        if ($category->meta_title=="") $category->meta_title = $category->name;
        Helper::setMetaData($category->meta_title, $category->meta_keyword, $category->meta_description);
	}
	
	public static function cart(){
		self::metaDataDependenMainPageShop('cart', 1, Text::_('JSHOP_CART'), Text::_('JSHOP_CART'));
	}
	
	public static function checkoutAddress(){
		self::metaData("checkout-address", 0, Text::_('JSHOP_CHECKOUT_ADDRESS'), Text::_('JSHOP_CHECKOUT_ADDRESS'));
	}
	
	public static function checkoutPayment(){		
		self::metaData("checkout-payment", 0, Text::_('JSHOP_CHECKOUT_PAYMENT'), Text::_('JSHOP_CHECKOUT_PAYMENT'));
	}
	
	public static function checkoutShipping(){		
		self::metaData("checkout-shipping", 0, Text::_('JSHOP_CHECKOUT_SHIPPING'), Text::_('JSHOP_CHECKOUT_SHIPPING'));
	}
	
	public static function checkoutPreview(){		
		self::metaData("checkout-preview", 0, Text::_('JSHOP_CHECKOUT_PREVIEW'), Text::_('JSHOP_CHECKOUT_PREVIEW'));
	}
	
	public static function checkoutFinish(){
		$document = Factory::getDocument();
        $document->setTitle(Text::_('JSHOP_CHECKOUT_FINISH'));
        Helper::appendPathWay(Text::_('JSHOP_CHECKOUT_FINISH'));
	}
	
	public static function content($page){		
		switch($page){
            case 'agb':
                $title = Text::_('JSHOP_AGB');
            break;
            case 'return_policy':
                $title = Text::_('JSHOP_RETURN_POLICY');
            break;
            case 'shipping':
                $title = Text::_('JSHOP_SHIPPING');
            break;
            case 'privacy_statement':
                $title = Text::_('JSHOP_PRIVACY_STATEMENT');
            break;
        }		
		if (Helper::getThisURLMainPageShop()){
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
		if (Helper::getShopManufacturerPageItemid()==Factory::getApplication()->input->getInt('Itemid')){
            Helper::appendPathWay($manufacturer->name);
        }
        if ($manufacturer->meta_title=="") $manufacturer->meta_title = $manufacturer->name;
        Helper::setMetaData($manufacturer->meta_title, $manufacturer->meta_keyword, $manufacturer->meta_description);
	}
	
	public static function search(){		
		self::metaDataDependenMainPageShop('search', 1, Text::_('JSHOP_SEARCH'), Text::_('JSHOP_SEARCH'));
	}
	
	public static function searchResult(){
		self::metaDataDependenMainPageShop('search-result', 1, Text::_('JSHOP_SEARCH'), Text::_('JSHOP_SEARCH'));
	}
	
	public static function userLogin(){
		self::metaDataDependenMainPageShop('login', 1, Text::_('JSHOP_LOGIN'), Text::_('JSHOP_LOGIN'));
	}
	
	public static function userRegister(){
		self::metaDataDependenMainPageShop('register', 1, Text::_('JSHOP_REGISTRATION'), Text::_('JSHOP_REGISTRATION'));
	}
	
	public static function userEditaccount(){
		if (ShopItemMenu::getInstance()->getEditaccount() != Factory::getApplication()->input->getInt('Itemid')){
			$pathway = Text::_('JSHOP_EDIT_DATA');
		}else{
			$pathway = '';
		}
		self::metaData("editaccount", 0, Text::_('JSHOP_EDIT_DATA'), $pathway);
	}
	
	public static function userOrders(){
		if (ShopItemMenu::getInstance()->getOrders() != Factory::getApplication()->input->getInt('Itemid')){
			$path_way = Text::_('JSHOP_MY_ORDERS');
		}else{
			$path_way = '';
		}
		self::metaData("myorders", 0, Text::_('JSHOP_MY_ORDERS'), $path_way);
	}
	
	public static function userOrder($order){
		$jshopConfig = JSFactory::getConfig();        
		self::metaData("myorder-detail", 0, Text::_('JSHOP_MY_ORDERS'));
		$shim = ShopItemMenu::getInstance();
		if ($shim->getOrders()!=Factory::getApplication()->input->getInt('Itemid')){
			Helper::appendPathWay(Text::_('JSHOP_MY_ORDERS'), Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=orders', 1, 0, $jshopConfig->use_ssl));
		}
        Helper::appendPathWay(Text::_('JSHOP_ORDER_NUMBER').": ".$order->order_number);
	}
	
	public static function userMyaccount(){
		if (ShopItemMenu::getInstance()->getUser() != Factory::getApplication()->input->getInt('Itemid')){
			$pathway = Text::_('JSHOP_MY_ACCOUNT');
		}else{
			$pathway = '';
		}
		self::metaData("myaccount", 0, Text::_('JSHOP_MY_ACCOUNT'), $pathway);
	}
	
	public static function userGroupsinfo(){
		Helper::setMetaData(Text::_('JSHOP_USER_GROUPS_INFO'), "", "");
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
		self::metaDataDependenMainPageShop('wishlist', 1, Text::_('JSHOP_WISHLIST'), Text::_('JSHOP_WISHLIST'));
	}
	
	public static function product($category, $product){
		$app = Factory::getApplication();
		$Itemid = $app->input->getInt('Itemid');
		if (Helper::getShopMainPageItemid()==$Itemid){
            Helper::appendExtendPathWay($category->getTreeChild(), 'product');
        }
		$menu = $app->getMenu();
		$menuItem = $menu->getItem($Itemid);        
		if (isset($menuItem->query['view']) && $menuItem->query['view']!='product'){
			Helper::appendPathWay($product->name);
		}
        if ($product->meta_title=="") $product->meta_title = $product->name;
        Helper::setMetaData($product->meta_title, $product->meta_keyword, $product->meta_description);
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