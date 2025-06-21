<?php
/**
* @version      5.8.0 22.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Site\Helper\Legacywebassetmanager;

defined('_JEXEC') or die();

class JSFactory{

    public static $load_user_id = null;

    public static function getConfig(){
    static $config;
        if (!isset($config)){
            PluginHelper::importPlugin('jshopping');
            $dispatcher = Factory::getApplication();
            $config = new \Joomla\Component\Jshopping\Site\Table\ConfigTable();
            include(JPATH_JOOMSHOPPING.'/config/default_config.php');
            if (file_exists(JPATH_JOOMSHOPPING.'/config/user_config.php')){
				include(JPATH_JOOMSHOPPING.'/config/user_config.php');
			}
            $dispatcher->triggerEvent('onBeforeLoadJshopConfig', array(&$config));
            $config->load($config->load_id);
            $config->loadCurrencyValue();
            $config->loadFrontLand();
            $config->loadLang();
			$config->parseConfigVars();
            $dispatcher->triggerEvent('onLoadJshopConfig', array(&$config));
        }
    return $config;
    }

    public static function getUserShop(){
    static $usershop;
        if (!isset($usershop)){
            $user = Factory::getUser(self::$load_user_id);
            $db = Factory::getDBO();
            $usershop = new \Joomla\Component\Jshopping\Site\Table\UsershopTable($db);
            if ($user->id){
                if (!$usershop->isUserInShop($user->id)){
                    $usershop->addUserToTableShop($user);
                }
                $usershop->load($user->id);
                $usershop->percent_discount = $usershop->getDiscount();
            }else{
                $usershop->percent_discount = 0;
            }
            Factory::getApplication()->triggerEvent('onAfterGetUserShopJSFactory', array(&$usershop));
        }
    return $usershop;
    }

    public static function getUserShopGuest(){
    static $userguest;
        if (!isset($userguest)){
            $userguest = new \Joomla\Component\Jshopping\Site\Model\UserguestModel();
            $userguest->load();
            $userguest->percent_discount = 0;
            Factory::getApplication()->triggerEvent('onAfterGetUserShopGuestJSFactory', array(&$userguest));
        }
    return $userguest;
    }

    public static function getUser(){
        $user = Factory::getUser();
        if ($user->id){
            $adv_user = JSFactory::getUserShop();
        }else{
            $adv_user = JSFactory::getUserShopGuest();
        }
    return $adv_user;
    }

    public static function loadCssFiles(){
    static $load;
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->load_css) return 0;
        if (!$load){
            $wa = JSFactory::getWebAssetManager();
			if (file_exists($jshopConfig->css_path.$jshopConfig->template.'.css')){
                $wap = $jshopConfig->getWebAssetParams('style', 'com.jshopping');
                $wa->registerAndUseStyle('com.jshopping', $jshopConfig->css_live_path.$jshopConfig->template.'.css', $wap['options'], $wap['attributes'], $wap['dependencies']);
			}
            if (file_exists($jshopConfig->css_path.$jshopConfig->template.'.custom.css')){
                $wap = $jshopConfig->getWebAssetParams('style', 'com.jshopping.custom');
                $wa->registerAndUseStyle('com.jshopping.custom', $jshopConfig->css_live_path.$jshopConfig->template.'.custom.css', $wap['options'], $wap['attributes'], $wap['dependencies']);
            }
            $load = 1;
        }
    }

    public static function loadJsFiles(){
    static $load;
        if (!$load){
            $jshopConfig = JSFactory::getConfig();
            $wa = JSFactory::getWebAssetManager();
            if ($jshopConfig->load_javascript) {
				if ($jshopConfig->load_javascript_bootstrap) {
					HTMLHelper::_('bootstrap.framework');
				}
				if ($jshopConfig->load_javascript_jquery) {
					HTMLHelper::_('jquery.framework');
				}
                $wap = $jshopConfig->getWebAssetParams('script', 'com.jshopping.function');
                $wa->registerAndUseScript('com.jshopping.function', $jshopConfig->file_functions_js, $wap['options'], $wap['attributes'], $wap['dependencies']);
                $wa->addInlineScript($jshopConfig->script_js_init);
            }
            $load = 1;
        }
    }

    public static function loadJsFilesRating(){
    static $load;
        if (!$load){
            $jshopConfig = JSFactory::getConfig();
            if ($jshopConfig->load_javascript){
                $wa = JSFactory::getWebAssetManager();
                $wap = $jshopConfig->getWebAssetParams('script', 'com.jshopping.metadata');
                $wa->registerAndUseScript('com.jshopping.metadata', $jshopConfig->file_metadata_js, $wap['options'], $wap['attributes'], $wap['dependencies']);
                $wap = $jshopConfig->getWebAssetParams('script', 'com.jshopping.rating');
                $wa->registerAndUseScript('com.jshopping.rating', $jshopConfig->file_rating_js, $wap['options'], $wap['attributes'], $wap['dependencies']);
                $wap = $jshopConfig->getWebAssetParams('style', 'com.jshopping.rating');
                $wa->registerAndUseStyle('com.jshopping.rating', $jshopConfig->file_rating_css, $wap['options'], $wap['attributes'], $wap['dependencies']);
            }
            $load = 1;
        }
    }

    public static function loadJsFilesLightBox(){
    static $load;
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->load_jquery_lightbox) return 0;
        if (!$load){
            HTMLHelper::_('bootstrap.framework');
            $wa = JSFactory::getWebAssetManager();
            $wap = $jshopConfig->getWebAssetParams('script', 'com.jshopping.lightbox');
            $wa->registerAndUseScript('com.jshopping.lightbox', $jshopConfig->file_lightbox_js, $wap['options'], $wap['attributes'], $wap['dependencies']);
            $wap = $jshopConfig->getWebAssetParams('style', 'com.jshopping.lightbox');
            $wa->registerAndUseStyle('com.jshopping.lightbox', $jshopConfig->file_lightbox_css, $wap['options'], $wap['attributes'], $wap['dependencies']);
            $wa->addInlineScript($jshopConfig->script_lightbox_init);
            $load = 1;
        }
    }

    public static function loadLanguageFile($langtag = null, $reload = false){
		return Factory::getLanguage()->load('com_jshopping', JPATH_ROOT.'/components/com_jshopping', $langtag, $reload);
    }

    public static function loadExtLanguageFile($extname, $langtag = "", $reload = true){
        $lang = Factory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        $langpatch = JPATH_ROOT.'/components/com_jshopping/lang/';
        if (file_exists($langpatch.'override/'.$extname.'/'.$langtag.'.php')) {
            include_once($langpatch.'override/'.$extname.'/'.$langtag.'.php');
        }elseif (file_exists($langpatch.$extname.'/'.$langtag.'.php')){
            include_once($langpatch.$extname.'/'.$langtag.'.php');
        }elseif (file_exists($langpatch.$extname.'/en-GB.php')){
            include_once($langpatch.$extname.'/en-GB.php');
        }
		$lang->load($extname, JPATH_ROOT.'/components/com_jshopping', $langtag, $reload);
    }

    public static function loadAdminLanguageFile($langtag = null, $reload = false){
        return Factory::getLanguage()->load('com_jshopping', JPATH_ROOT.'/administrator/components/com_jshopping', $langtag, $reload);
    }

    public static function loadExtAdminLanguageFile($extname, $langtag = "", $reload = true){
        $lang = Factory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        $langpatch = JPATH_ROOT.'/administrator/components/com_jshopping/lang/';
        if (file_exists($langpatch.'override/'.$extname.'/'.$langtag.'.php')){
            include_once($langpatch.'override/'.$extname.'/'.$langtag.'.php');
        }elseif (file_exists($langpatch.$extname.'/'.$langtag.'.php')){
            include_once($langpatch.$extname.'/'.$langtag.'.php');
        }elseif (file_exists($langpatch.$extname.'/en-GB.php')){
            include_once($langpatch.$extname.'/en-GB.php');
        }
		$lang->load($extname, JPATH_ROOT.'/administrator/components/com_jshopping', $langtag, $reload);
    }

    public static function getLang($langtag = ""){
    static $ml;
        if (!isset($ml) || $langtag!=""){
            $jshopConfig = JSFactory::getConfig();
            $ml = new Multilangfield();
            if ($langtag==""){
                $langtag = $jshopConfig->getLang();
            }
            $ml->setLang($langtag);
            Factory::getApplication()->triggerEvent('onAfterGetLangJSFactory', array(&$ml, &$langtag));
        }
    return $ml;
    }

    public static function getReservedFirstAlias(){
    static $alias;
        if (!isset($alias)){
            $files = Folder::files(JPATH_ROOT."/components/com_jshopping/Controller");
            $alias = array();
            foreach($files as $file){
                $alias[] = strtolower(str_replace(array(".php", 'Controller'),"", $file));
            }
        }        
    return $alias;
    }

    public static function getAliasCategory($langtag = ""){
    static $alias;
		if ($langtag==""){
			$langtag = JSFactory::getConfig()->getLang();
		}
		if (!isset($alias)){
			$alias = array();
		}
        if (!isset($alias[$langtag])){
            $db = Factory::getDBO();
            $lang = new Multilangfield();
            $lang->setLang($langtag);
            $dbquery = "select category_id as id, `".$lang->get('alias')."` as alias from #__jshopping_categories where `".$lang->get('alias')."`!=''";
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
			$alias[$langtag] = array();
            foreach($rows as $row){
                $alias[$langtag][$row->id] = $row->alias;
            }
            unset($rows);
        }
    return $alias[$langtag];
    }

    public static function getAliasManufacturer($langtag = ""){
    static $alias;
		if ($langtag==""){
			$langtag = JSFactory::getConfig()->getLang();
		}
		if (!isset($alias)){
			$alias = array();
		}
        if (!isset($alias[$langtag])){
            $db = Factory::getDBO();
            $lang = new Multilangfield();
            $lang->setLang($langtag);
            $dbquery = "select manufacturer_id as id, `".$lang->get('alias')."` as alias from #__jshopping_manufacturers where `".$lang->get('alias')."`!=''";
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
            $alias[$langtag] = array();
            foreach($rows as $row){
                $alias[$langtag][$row->id] = $row->alias;
            }
            unset($rows);
        }
    return $alias[$langtag];
    }

    public static function getAliasProduct($langtag = ""){
    static $alias;
        if ($langtag==""){
			$langtag = JSFactory::getConfig()->getLang();
		}
		if (!isset($alias)){
			$alias = array();
		}
        if (!isset($alias[$langtag])){
            $db = Factory::getDBO();
            $lang = new Multilangfield();
            $lang->setLang($langtag);
            $dbquery = "select product_id as id, `".$lang->get('alias')."` as alias from #__jshopping_products where `".$lang->get('alias')."`!=''";
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
            $alias[$langtag] = array();
            foreach($rows as $k=>$row){
				$alias[$langtag][$row->id] = $row->alias;
                unset($rows[$k]);
            }
            unset($rows);
        }
    return $alias[$langtag];
    }

    public static function getAllAttributes($resformat = 0, $publish = null){
        static $list;
        $list = $list ?? [];
        if (!isset($list[$publish])){
            $_attrib = JSFactory::getTable("attribut");
            $filter = [];
            if (isset($publish)) {
                $filter['publish'] = $publish;
            }
            $list[$publish] = $_attrib->getAllAttributes(1, $filter);
        }
        $attributes = $list[$publish];
        if ($resformat==0){
            return $attributes;
        }
        if ($resformat==1){
            $attributes_format1 = array();
            foreach($attributes as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($resformat==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($attributes as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    }

    public static function getAllUnits(){
    static $rows;
        if (!isset($rows)){
            $_unit = JSFactory::getTable("unit");
            $rows = $_unit->getAllUnits();
        }
    return $rows;
    }

    public static function getAllTaxesOriginal(){
    static $rows;
        if (!isset($rows)){
            $_tax = JSFactory::getTable('tax');
            $_rows = $_tax->getAllTaxes();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->tax_id] = $row->tax_value;
            }
        }
    return $rows;
    }

    public static function getAllTaxes(){
    static $rows;
        if (!isset($rows)){
            $jshopConfig = JSFactory::getConfig();
            $dispatcher = Factory::getApplication();
            $_tax = JSFactory::getTable('tax');
            $rows = JSFactory::getAllTaxesOriginal();
            if ($jshopConfig->use_extend_tax_rule){
                $country_id = 0;
                $adv_user = JSFactory::getUser();
                $country_id = $adv_user->country;
                if ($jshopConfig->tax_on_delivery_address && $adv_user->delivery_adress && $adv_user->d_country){
                    $country_id = $adv_user->d_country;
                }
                $client_type = $adv_user->client_type;
                $enter_tax_id = $adv_user->tax_number!="";
                if (!$country_id){
                    $country_id = $jshopConfig->default_country;
                }
                if ($country_id){
                    $_rowsext = $_tax->getExtTaxes();
                    $dispatcher->triggerEvent('onBeforeGetAllTaxesRowsext', array(&$_rowsext, &$country_id, &$adv_user, &$rows) );
                    foreach($_rowsext as $v){
                        if (in_array($country_id, $v->countries)){
                            if ($jshopConfig->ext_tax_rule_for==1){
                                if ($enter_tax_id){
                                    $rows[$v->tax_id] = $v->firma_tax;
                                }else{
                                    $rows[$v->tax_id] = $v->tax;
                                }
                            }else{
                                if ($client_type==2){
                                    $rows[$v->tax_id] = $v->firma_tax;
                                }else{
                                    $rows[$v->tax_id] = $v->tax;
                                }
                            }
                        }
                    }
                    $dispatcher->triggerEvent('onAfterGetAllTaxesRowsext', array(&$_rowsext, &$country_id, &$adv_user, &$rows) );
                    unset($_rowsext);
                }
            }
        $dispatcher->triggerEvent('onAfterGetAllTaxes', array(&$rows) );
        }
    return $rows;
    }

    public static function getAllManufacturer(){
    static $rows;
        if (!isset($rows)){
            $db = Factory::getDBO();
            $lang = JSFactory::getLang();
            $dispatcher = Factory::getApplication();
            $adv_result = "manufacturer_id as id, `".$lang->get('name')."` as name, manufacturer_logo, manufacturer_url";
            $dispatcher->triggerEvent('onBeforeQueryGetAllManufacturer', array(&$adv_result));
            $query = "select ".$adv_result." from #__jshopping_manufacturers where manufacturer_publish='1'";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getMainVendor(){
    static $row;
        if (!isset($row)){
            $row = JSFactory::getTable('vendor');
            $row->loadMain();
        }
    return $row;
    }

    public static function getAllVendor(){
    static $rows;
        if (!isset($rows)){
            $db = Factory::getDBO();
            $query = "select id, shop_name, l_name, f_name from #__jshopping_vendors";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            $mainvendor = JSFactory::getMainVendor();
            $rows[0] = $mainvendor;
            foreach($_rows as $row){
                $rows[$row->id] = $row;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllDeliveryTime(){
    static $rows;
        if (!isset($rows)){
            $db = Factory::getDBO();
            $lang = JSFactory::getLang();
            $query = "select id, `".$lang->get('name')."` as name from #__jshopping_delivery_times";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->name;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllDeliveryTimeDays(){
    static $rows;
        if (!isset($rows)){
            $db = Factory::getDBO();
            $lang = JSFactory::getLang();
            $query = "select id, days from #__jshopping_delivery_times";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->days;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllProductExtraField($publish = null){
        static $list;
        $list = $list ?? [];
        if (!isset($list[$publish])){
            $productfield = JSFactory::getTable('productfield');
            $filter = [];
            if (isset($publish)) {
                $filter['publish'] = $publish;
            }
            $list[$publish] = $productfield->getList(1, $filter);
        }
    return $list[$publish];
    }

    public static function getAllProductExtraFieldValue($publish = null){
        static $list;
        $list = $list ?? [];
        if (!isset($list[$publish])){
            $productfieldvalue = JSFactory::getTable('productfieldvalue');
            $filter = [];
            if (isset($publish)) {
                $filter['publish'] = $publish;
            }
            $list[$publish] = $productfieldvalue->getAllList(1, $filter);
        }
    return $list[$publish];
    }

    public static function getAllProductExtraFieldValueDetail($publish = null){
        static $list;
        $list = $list ?? [];
        if (!isset($list[$publish])){
            $productfieldvalue = JSFactory::getTable('productfieldvalue');
            $filter = [];
            if (isset($publish)) {
                $filter['publish'] = $publish;
            }
            $list[$publish] = $productfieldvalue->getAllList(2, $filter);
        }
    return $list[$publish];
    }

    public static function getDisplayListProductExtraFieldForCategory($cat_id, $publish = null){
    static $listforcat;
        if (!isset($listforcat[$cat_id][$publish])){
            $fields = [];
            $list = JSFactory::getAllProductExtraField($publish);
            foreach($list as $val){
                if ($val->allcats){
                    $fields[] = $val->id;
                }else{
                    if (in_array($cat_id, $val->cats)) $fields[] = $val->id;
                }
            }

            $jshopConfig = JSFactory::getConfig();
            $config_list = $jshopConfig->getProductListDisplayExtraFields();
            foreach($fields as $k=>$val){
                if (!in_array($val, $config_list)) unset($fields[$k]);
            }
            $listforcat[$cat_id][$publish] = $fields;
        }
    return $listforcat[$cat_id][$publish];
    }

    public static function getDisplayFilterExtraFieldForCategory($cat_id, $publish = null){
    static $listforcat;
        if (!isset($listforcat[$cat_id][$publish])){
            $fields = [];
            $list = JSFactory::getAllProductExtraField($publish);
            foreach($list as $val){
                if ($val->allcats){
                    $fields[] = $val->id;
                }else{
                    if (in_array($cat_id, $val->cats)) $fields[] = $val->id;
                }
            }

            $jshopConfig = JSFactory::getConfig();
            $config_list = $jshopConfig->getFilterDisplayExtraFields();
            foreach($fields as $k=>$val){
                if (!in_array($val, $config_list)) unset($fields[$k]);
            }
            $listforcat[$cat_id][$publish] = $fields;
        }
    return $listforcat[$cat_id][$publish];
    }

    public static function getAllCurrency(){
    static $list;
        if (!isset($list)){
            $currency = JSFactory::getTable('Currency');
            $_list = $currency->getAllCurrencies();
            $list = array();
            foreach($_list as $row){
                $list[$row->currency_id] = $row;
            }
			if (empty($list)) {
				$row = (object)[
					'currency_id' => 1555, 
					'currency_name' => 'EUR', 
					'currency_code' => 'EUR', 
					'currency_code_iso' => 'EUR', 
					'currency_code_num' => 978, 
					'currency_ordering' => 1,
					'currency_value' => 1, 
					'currency_publish' => 1
				];
				$list[1] = $row;
			}
        }
    return $list;
    }

    public static function getShippingExtList($for_shipping = 0){
    static $list;
        if (!isset($list)){
            $jshopConfig = JSFactory::getConfig();
            $path = $jshopConfig->path."shippings";
            $shippingext = JSFactory::getTable('shippingext');
            $_list = $shippingext->getList(1);
            $list = array();
            foreach($_list as $row){
                $extname = $row->alias;
                $filepatch = $path."/".$extname."/".$extname.".php";
                if (file_exists($filepatch)){
                    include_once($filepatch);
                    $row->exec = new $extname();
                    $list[$row->id] = $row;
                }else{
                    JSError::raiseWarning("",'Load ShippingExt "'.$extname.'" error.');
                }
            }
        }
        if ($for_shipping==0){
            return $list;
        }
        $returnlist = array();
        foreach($list as $row){
            if ($row->shipping_method!=""){
                $sm = unserialize($row->shipping_method);
            }else{
                $sm = array();
            }
            if(!isset($sm[$for_shipping])){
                $sm[$for_shipping]=1;
            }
            if ($sm[$for_shipping]!=="0"){
                $returnlist[] = $row;
            }
        }
    return $returnlist;
    }
    
    public static function getWebAssetManager() {
        if (JSFactory::getConfig()->use_web_asset_manager) {
			$doc = Factory::getApplication()->getDocument() ?? Factory::getDocument();
			$wa = $doc->getWebAssetManager();
        } else {
            $wa = new Legacywebassetmanager();
        }
        return $wa;
    }

    public static function getTable($type, $prefix = 'Joomla\\Component\\Jshopping\\Site\\Table\\', $config = array()){
        if (strtolower($prefix)=='jshop'){
            $prefix = 'Joomla\\Component\\Jshopping\\Site\\Table\\';
        }
        if (!substr_count($type, 'Table') && $prefix=='Joomla\\Component\\Jshopping\\Site\\Table\\'){
            $type = strtolower($type).'Table';
        }
        Factory::getApplication()->triggerEvent('onJSFactoryGetTable', array(&$type, &$prefix, &$config));
        $table = Table::getInstance($type, $prefix, $config);        
        Factory::getApplication()->triggerEvent('onAfterJSFactoryGetTable', array(&$table, &$type, &$prefix, &$config));
        return $table;
    }

    public static function getModel($type, $prefix = 'Joomla\\Component\\Jshopping\\Administrator\\Model\\', $config = array()){        
        if ($prefix=='jshop' || $prefix=='Site'){
            $prefix = 'Joomla\\Component\\Jshopping\\Site\\Model\\';
        }
        if (preg_match('/^Site(.*)/', $prefix, $masches)){
            $prefix = 'Joomla\\Component\\Jshopping\\Site\\Model'.$masches[1].'\\';
        }
        if ($prefix=='Administrator' || strtolower($prefix)=='jshoppingmodel'){
            $prefix = 'Joomla\\Component\\Jshopping\\Administrator\\Model\\';
        }        
        if (!substr_count($type, 'Model') && substr_count($prefix, 'Jshopping')){
            $type = strtolower($type).'Model';
        }
        Factory::getApplication()->triggerEvent('onJSFactoryGetModel', array(&$type, &$prefix, &$config));
        $model = BaseDatabaseModel::getInstance($type, $prefix, $config);
        Factory::getApplication()->triggerEvent('onAfterJSFactoryGetModel', array(&$model, &$type, &$prefix, &$config));
        return $model;
    }

    public static function setLoadUserId($id){
        self::$load_user_id = $id;
    }

    public static function getLoadUserId(){
        return self::$load_user_id;
    }

}