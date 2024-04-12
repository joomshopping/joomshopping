<?php
/**
* @version      5.1.3 19.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
use Joomla\Component\Jshopping\Site\Lib\TreeObjectList;
use Joomla\Component\Jshopping\Site\Lib\ShopItemMenu;

defined('_JEXEC') or die();

class Helper{
    
    public static function getJsFrontRequestController(){
        $input = \JFactory::getApplication()->input;
        $controller = $input->getCmd('controller');
        if (!$controller) $controller = $input->getCmd('view');
        if (!$controller) $controller = "category";
        return $controller;
    }
    
    public static function js_add_trigger($vars = [], $name = ''){
        list(,$caller) = debug_backtrace();
        $caller['class'] = isset($caller['class']) ? $caller['class'] : "";
        $trigger_name = 'on'.ucfirst(str_replace('\\', '', $caller['class'])).ucfirst($caller['function']).ucfirst($name);
        \JFactory::getApplication()->triggerEvent($trigger_name, array(&$caller['object'], &$vars));
        return $vars;
    }

    public static function setMetaData($title, $keyword, $description, $params=null){
        $config = \JFactory::getConfig();
        $document =\JFactory::getDocument();
        if ($title=='' && $params && $params->get('page_title')!=''){
            $title = $params->get('page_title');
        }
        if ($keyword=='' && $params && $params->get('menu-meta_keywords')!=''){
            $keyword = $params->get('menu-meta_keywords');
        }
        if ($description=='' && $params && $params->get('menu-meta_description')!=''){
            $description = $params->get('menu-meta_description');
        }
        if ($config->get('sitename_pagetitles')==1){
            $title = $config->get('sitename')." - ".$title;
        }
        if ($config->get('sitename_pagetitles')==2){
            $title = $title." - ".$config->get('sitename');
        }
        $document->setTitle($title);
        $document->setMetadata('keywords',$keyword);
        $document->setMetadata('description',$description);
        if (!$params){
            $params = \JFactory::getApplication()->getParams();
        }
        if ($params->get('robots')){
            $document->setMetadata('robots', $params->get('robots'));
        }
    }

    public static function parseArrayToParams($array) {
        $str = '';
        foreach ($array as $key => $value) {
            $str .= $key."=".$value."\n";
        }
        return $str;
    }

    public static function parseParamsToArray($string) {
        $temp = explode("\n",$string);
		$array = [];
        foreach ($temp as $key => $value) {
            if(!$value) continue;
            $temp2 = explode("=",$value);
            $array[$temp2[0]] = $temp2[1];
        }
        return $array;
    }

    public static function getParseParamsSerialize($data){
        if ($data!=""){
            return unserialize($data);
        }else{
            return [];
        }
    }

    public static function outputDigit($digit, $count_null) {
        $length = strlen(strval($digit));
        for ($i = 0; $i < $count_null - $length; $i++) {
            $digit = '0'.$digit;
        }
        return $digit;
    }

    public static function splitValuesArrayObject($array_object,$property_name) {
        $return = '';
        if (is_array($array_object)){
            foreach($array_object as $key=>$value){
                $return .= $array_object[$key]->$property_name.', ';
            }
            $return = "( ".substr($return,0,strlen($return) - 2)." )";
        }
        return $return;
    }

    public static function getTextNameArrayValue($names, $values){
        $return = '';
        foreach ($names as $key=>$value){
            $return .= $names[$key].": ".$values[$key]."\n";
        }
        return $return;
    }

    public static function strToHex($string){
        $hex='';
        for ($i=0;$i<strlen($string);$i++){
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    public static function hexToStr($hex){
        $string='';
        for ($i=0;$i<strlen($hex)-1;$i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

    public static function insertValueInArray($value, &$array) {
        if ($key = array_search($value, $array)) return $key;
        $array[$value] = $value;
        ksort($array);
        return $key-1;
    }

    public static function appendExtendPathWay($array, $page) {
        $app =\JFactory::getApplication();
        $pathway = $app->getPathway();
        \JFactory::getApplication()->triggerEvent('onBeforeAppendExtendPathWay', array(&$array, &$page, &$pathway));
        foreach($array as $cat){
            $pathway->addItem($cat->name, self::SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$cat->category_id, 1));
        }
        \JFactory::getApplication()->triggerEvent('onAfterAppendExtendPathWay', array(&$array, &$page, &$pathway));
    }

    public static function appendPathWay($page, $url = ""){
        $app =\JFactory::getApplication();
        $pathway = $app->getPathway();
        \JFactory::getApplication()->triggerEvent('onBeforeAppendPathWay', array(&$page, &$url, &$pathway));
        if ($url!=""){
            $pathway->addItem($page, $url);
        }else{
            $pathway->addItem($page);
        }
        \JFactory::getApplication()->triggerEvent('onAfterAppendPathWay', array(&$page, &$url, &$pathway));
    }

    public static function getMainCurrencyCode(){
        $jshopConfig = \JSFactory::getConfig();
        $currency = \JSFactory::getTable('currency');
        $currency->load($jshopConfig->mainCurrency);
    return $currency->currency_code;
    }

    public static function formatprice($price, $currency_code = null, $currency_exchange = 0, $style_currency = 0) {
        $jshopConfig = \JSFactory::getConfig();
		$price = floatval($price);

        if ($currency_exchange){
            $price = $price * $jshopConfig->currency_value;
        }
        if ($jshopConfig->formatprice_style_currency_span && $style_currency!=-1){
            $style_currency = 1;
        }
        if (!$currency_code){
            $currency_code = $jshopConfig->currency_code;
        }
        if ($jshopConfig->decimal_count<0){
            $price = round($price, intval($jshopConfig->decimal_count));
        }
        $price = number_format($price, intval($jshopConfig->decimal_count), $jshopConfig->decimal_symbol, $jshopConfig->thousand_separator);
        if ($style_currency==1) $currency_code = '<span class="currencycode">'.$currency_code.'</span>';
        $return = str_replace("Symb", $currency_code, str_replace("00", $price, $jshopConfig->format_currency[$jshopConfig->currency_format]));
        extract(self::js_add_trigger(get_defined_vars(), "after"));
        return $return;
    }

    public static function formatEPrice($price){
        $jshopConfig = \JSFactory::getConfig();
        return number_format($price, intval($jshopConfig->product_price_precision), '.', '');
    }

    public static function formatdate($date, $showtime = 0){
        $jshopConfig = \JSFactory::getConfig();
        $format = $jshopConfig->store_date_format;
        if ($showtime) $format = $format." %H:%M:%S";
        return @strftime($format, strtotime($date));
    }

    public static function formattax($val){
        $jshopConfig = \JSFactory::getConfig();
        $val = floatval($val);
        return str_replace(".", $jshopConfig->decimal_symbol, $val);
    }

    public static function formatweight($val, $unitid = 0, $show_unit = 1){
        $jshopConfig = \JSFactory::getConfig();
        if (!$unitid){
            $unitid = $jshopConfig->main_unit_weight;
        }
        $units = \JSFactory::getAllUnits();
        $unit = $units[$unitid];
        if ($show_unit){
            $sufix = " ".$unit->name;
        }else{
            $sufix = "";
        }
        $val = floatval($val);
        return str_replace(".", $jshopConfig->decimal_symbol, $val).$sufix;
    }

    public static function formatqty($val){
        return floatval($val);
    }

    public static function getRoundPriceProduct($price){
        $jshopConfig = \JSFactory::getConfig();
        if ($jshopConfig->price_product_round){
            $price = round($price, intval($jshopConfig->decimal_count));
        }
        return $price;
    }

    public static function sprintCurrency($id, $field = 'currency_code'){
        $all_currency = \JSFactory::getAllCurrency();
	return $all_currency[$id]->$field ?? null;
    }

    public static function sprintUnitWeight(){
        $jshopConfig = \JSFactory::getConfig();
        $units = \JSFactory::getAllUnits();
        $unit = $units[$jshopConfig->main_unit_weight];
    return $unit->name;
    }

    /**
    * get system language
    *
    * @param int $client (0 - site, 1 - admin)
    */
    public static function getAllLanguages($client=0){
        jimport('joomla.filesystem.folder');
        $pattern = '#(.*?)\(#is';
        $rows = [];
        $path = JPATH_ROOT.'/language';
        $dirs = \JFolder::folders($path);
        foreach($dirs as $dir){
            $files = \JFolder::files( $path.'/'.$dir, '^([-_A-Za-z]*)\.xml$' );
            foreach($files as $file){
                $data = \JInstaller::parseXMLInstallFile($path.'/'.$dir.'/'.$file);
				if (!is_array($data) || strtolower($file) != 'install.xml') {
                    continue;
                }
                $row = new \StdClass();
                $row->descr = $data['name'];
                $row->language = $dir;
                $row->lang = substr($row->language, 0, 2);
                $row->name = $data['name'];
                preg_match($pattern, $row->name, $matches);
                if (isset($matches[1])) $row->name = trim($matches[1]);
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public static function installNewLanguages($defaultLanguage = "", $show_message = 1){
        $db =\JFactory::getDBO();
        $jshopConfig = \JSFactory::getConfig();
        $session =\JFactory::getSession();
        $joomlaLangs = self::getAllLanguages();	
        $checkedlanguage = $session->get('jshop_checked_language');
        if (is_array($checkedlanguage)){
            $newlanguages = 0;
            foreach($joomlaLangs as $lang){
                if (!in_array($lang->language, $checkedlanguage)) $newlanguages++;
            }
            if ($newlanguages==0) return 0;
        }

        $query = "select * from #__jshopping_languages";
        $db->setQuery($query);
        $shopLangs = $db->loadObjectList();
        $shopLangsTag = [];
        foreach($shopLangs as $lang){
            $shopLangsTag[] = $lang->language;
        }

        if (!$defaultLanguage) $defaultLanguage = $jshopConfig->defaultLanguage;

        $checkedlanguage = [];
        $installed_new_lang = 0;

        foreach($joomlaLangs as $lang){
            $checkedlanguage[] = $lang->language;
            if (!in_array($lang->language, $shopLangsTag)){
                $ml = \JSFactory::getLang();
                if ($ml->addNewFieldLandInTables($lang->language, $defaultLanguage)){
                    $installed_new_lang = 1;
                    $query = "insert into #__jshopping_languages set `language`='".$db->escape($lang->language)."', `name`='".$db->escape($lang->name)."', `publish`='1', ordering=1";
                    $db->setQuery($query);
                    $db->execute();
                    if ($show_message){
                        \JFactory::getApplication()->enqueueMessage(\JText::_('JSHOP_INSTALLED_NEW_LANGUAGES').": ".$lang->name, 'notice');
                    }
                }
            }
        }
        $session->set("jshop_checked_language", $checkedlanguage);
        return 1;
    }

    public static function recurseTree($cat, $level, $all_cats, &$categories, $is_select){
        $probil = '';
        if ($is_select){
            for ($i = 0; $i < $level; $i++) {
                $probil .= '-- ';
            }
            $cat->name = ($probil . $cat->name);
            $categories[] = \JHTML::_('select.option', $cat->category_id, $cat->name, 'category_id', 'name');
        } else {
            $cat->level = $level;
            $categories[] = $cat;
        }
        foreach($all_cats as $categ) {
            if ($categ->category_parent_id == $cat->category_id){
                self::recurseTree($categ, ++$level, $all_cats, $categories, $is_select);
                $level--;
            }
        }
        return $categories;
    }

    public static function buildTreeCategory($publish = 1, $is_select = 1, $access = 1){
        $list = \JSFactory::getTable('category')->getAllCategories($publish, $access, 'name');
        $tree = new TreeObjectList($list, array(
            'parent' => 'category_parent_id',
            'id' => 'category_id',
            'is_select' => $is_select
        ));
        return $tree->getList();
    }

    public static function _getCategoryParent($cat, $parent){
        $res = [];
        foreach($cat as $obj){
            if ($obj->category_parent_id == $parent){
                $res[] = $obj;
            }
        }
    return $res;
    }

    public static function _getResortCategoryTree(&$cats, $allcats){
        foreach($cats as $k=>$v){
            $cats_sub = self::_getCategoryParent($allcats, $v->category_id);
            if (count($cats_sub)){
                self::_getResortCategoryTree($cats_sub, $allcats);
            }
            $cats[$k]->subcat = $cats_sub;
        }
    }

    public static function getTreeCategory($publish = 1, $access = 1){
        $allcats = \JSFactory::getTable('category')->getAllCategories($publish, $access, 'name');
        $cats = self::_getCategoryParent($allcats, 0);
        self::_getResortCategoryTree($cats, $allcats);
    return $cats;
    }

    /**
    * check date Format date yyyy-mm-dd
    */
    public static function checkMyDate($date) {
        if (trim($date)=="") return false;
        $arr = explode("-",$date);
    return checkdate($arr[1],$arr[2],$arr[0]);
    }

    public static function checkUserLogin(){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        header("Cache-Control: no-cache, must-revalidate");
        if(!$user->id) {
            $app =\JFactory::getApplication();
            $return = base64_encode($_SERVER['REQUEST_URI']);
            $session =\JFactory::getSession();
            $session->set("return", $return);
            $app->redirect(self::SEFLink('index.php?option=com_jshopping&controller=user&task=login', 1, 1, $jshopConfig->use_ssl));
            exit();
        }
    return 1;
    }

    public static function addLinkToProducts(&$products, $default_category_id = 0, $useDefaultItemId = 0){
        $jshopConfig = \JSFactory::getConfig();
        \JFactory::getApplication()->triggerEvent('onBeforeAddLinkToProducts', array(&$products, &$default_category_id, &$useDefaultItemId));
        foreach($products as $key=>$value){
            $category_id = (!$default_category_id)?($products[$key]->category_id):($default_category_id);
            if (!$category_id) $category_id = 0;
            $products[$key]->product_link = self::SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$category_id.'&product_id='.$products[$key]->product_id, $useDefaultItemId);
            $products[$key]->buy_link = '';
            if ($jshopConfig->show_buy_in_category && $products[$key]->_display_price){
                if (!($jshopConfig->hide_buy_not_avaible_stock && ($products[$key]->product_quantity <= 0))){
                    $products[$key]->buy_link = self::SEFLink('index.php?option=com_jshopping&controller=cart&task=add&category_id='.$category_id.'&product_id='.$products[$key]->product_id, 1);
                }
            }
        }
    }

    public static function getJHost(){
        return $_SERVER["HTTP_HOST"];
    }

    public static function searchChildCategories($category_id,$all_categories,&$cat_search) {
        foreach ($all_categories as $all_cat) {
            if($all_cat->category_parent_id == $category_id) {
                \JSHelper::searchChildCategories($all_cat->category_id, $all_categories, $cat_search);
                $cat_search[] = $all_cat->category_id;
            }
        }
    }

    public static function getThisURLMainPageShop(){
        $shopMainPageItemid = self::getShopMainPageItemid();
        $Itemid = \JFactory::getApplication()->input->getInt("Itemid");
    return ($shopMainPageItemid==$Itemid && $Itemid!=0);
    }

    public static function getShopMainPageItemid($lang=''){
    static $Itemid;
        if (!isset($Itemid[$lang])){
            $shim = ShopItemMenu::getInstance($lang);
            $Itemid[$lang] = $shim->getShop();
            if (!$Itemid[$lang]){
                $Itemid[$lang] = $shim->getProducts();
            }
        }
    return $Itemid[$lang];
    }

    public static function getShopManufacturerPageItemid($lang=''){
    static $Itemid;
        if (!isset($Itemid[$lang])){
            $shim = ShopItemMenu::getInstance($lang);
            $Itemid[$lang] = $shim->getManufacturer();
        }
    return $Itemid[$lang];
    }

    public static function getDefaultItemid($link = ''){
        $Itemid = 0;
        $lang = '';
        if ($link!=''){
            $url = parse_url($link);
            parse_str($url['query'], $query);
            if (isset($query['lang'])){
                $lang = $query['lang'];
            }
            $shim = ShopItemMenu::getInstance($lang);
            $Itemid = $shim->getItemIdFromQuery($query);
        }
        if ($Itemid){
            return $Itemid;
        }else{
            return self::getShopMainPageItemid($lang);
        }
    }

    /**
    * set Sef Link
    *
    * @param string $link
    * @param int $useDefaultItemId - (0 - current itemid, 1 - shop page itemid, 2 -manufacturer itemid)
    * @param int $redirect
    */
    public static function SEFLink($link, $useDefaultItemId = 1, $redirect = 0, $ssl=null){
        $app = \JFactory::getApplication();
        \JPluginHelper::importPlugin('jshoppingproducts');        
        \JFactory::getApplication()->triggerEvent('onLoadJshopSEFLink', array(&$link, &$useDefaultItemId, &$redirect, &$ssl));
        $defaultItemid = self::getDefaultItemid($link);
        if ($useDefaultItemId==2){
            $Itemid = self::getShopManufacturerPageItemid();
            if (!$Itemid) $Itemid = $defaultItemid;
        }elseif ($useDefaultItemId==1){
            $Itemid = $defaultItemid;
        }else{
            $Itemid = $app->input->getInt('Itemid');
            if (!$Itemid) $Itemid = $defaultItemid;
        }
        \JFactory::getApplication()->triggerEvent('onAfterLoadJshopSEFLinkItemid', array(&$Itemid, &$link, &$useDefaultItemId, &$redirect, &$ssl));
        if (!preg_match('/Itemid=/', $link)){
            if (!preg_match('/\?/', $link)) $sp = "?"; else $sp = "&";
            $link .= $sp.'Itemid='.$Itemid;
        }
        $link = \JRoute::_($link, (($redirect) ? (false) : (true)), $ssl);
        if ($app->isClient('administrator')){
            $link = str_replace('/administrator', '', $link);
        }
    return $link;
    }

    public static function getFullUrlSefLink($link, $useDefaultItemId = 0, $redirect = 0, $ssl=null){
        $app = \JFactory::getApplication();
        $liveurlhost = \JURI::getInstance()->toString(array("scheme",'host', 'port'));
        if ($app->isClient('administrator')) {
			$shop_item_id = self::getDefaultItemid($link);
            $app = \Joomla\CMS\Application\CMSApplication::getInstance('site');            
            $router = $app::getRouter();
            if (!preg_match('/Itemid=/', $link)){
                if (!preg_match('/\?/', $link)) $sp = "?"; else $sp = "&";
                $link .= $sp."Itemid=".$shop_item_id;
            }
            $uri = $router->build($link);
            $url = $uri->toString();
            $fullurl = $liveurlhost.str_replace('/administrator', '', $url);
        }else{
            $fullurl = $liveurlhost.self::SEFLink($link, $useDefaultItemId, $redirect, $ssl);
        }
        return $fullurl;
    }

    public static function compareX64($a,$b){
    return base64_encode($a)==$b;
    }

    public static function replaceNbsp($string) {
    return (str_replace(" ","_",$string));
    }

    public static function replaceToNbsp($string) {
    return (str_replace("_"," ",$string));
    }

    public static function replaceWWW($str){
    return str_replace("www.","",$str);
    }

    public static function sprintRadioList($list, $name, $params, $key, $val, $actived = null, $separator = ' '){
        $html = "";
        $id = str_replace("[","",$name);
        $id = str_replace("]","",$id);
        foreach($list as $obj){
            $id_text = $id.$obj->$key;
            if ($obj->$key == $actived) $sel = ' checked="checked"'; else $sel = '';
            $html.='<span class="input_type_radio"><input type="radio" name="'.$name.'" id="'.$id_text.'" value="'.$obj->$key.'"'.$sel.' '.$params.'> <label for="'.$id_text.'">'.$obj->$val."</label></span>".$separator;
        }
    return $html;
    }

    public static function saveToLog($file, $text){
        $jshopConfig = \JSFactory::getConfig();
        if (!$jshopConfig->savelog) return 0;
        if ($file=='paymentdata.log' && !$jshopConfig->savelogpaymentdata) return 0;
        $f = fopen($jshopConfig->log_path.$file, "a+");
        fwrite($f, self::getJsDate('now', 'Y-m-d H:i:s')." ".$text."\r\n");
        fclose($f);
    return 1;
    }

    public static function displayTextJSC(){
        $conf = \JSFactory::getConfig();
        if (self::getJsFrontRequestController()!='content' && !self::compareX64(self::replaceWWW(self::getJHost()),$conf->licensekod)){
            print $conf->copyrightText;
        }
    }

    public static function filterHTMLSafe(&$mixed, $quote_style=ENT_QUOTES, $exclude_keys='' ){
        if (is_object( $mixed )){
            foreach (get_object_vars( $mixed ) as $k => $v){
                if (is_array( $v ) || is_object( $v ) || $v == NULL) {
                    continue;
                }
                if (is_string( $exclude_keys ) && $k == $exclude_keys) {
                    continue;
                } else if (is_array( $exclude_keys ) && in_array( $k, $exclude_keys )) {
                    continue;
                }
                $mixed->$k = htmlspecialchars( $v, $quote_style, 'UTF-8' );
            }
        }
    }

    public static function saveAsPrice($val){
		if (!isset($val)) return 0;
        $val = str_replace(",", ".", $val);
        preg_match('/-?[0-9]+(\.[0-9]+)?/', $val, $matches);
        return isset($matches[0]) ? floatval($matches[0]) : 0;
    }

    public static function getPriceDiscount($price, $discount){
        return $price - ($price*$discount/100);
    }

    public static function getSeoSegment($str){
        return str_replace(":", "-", $str);
    }

    public static function setPrevSelLang($lang){
        $session =\JFactory::getSession();
        $session->set("js_history_sel_lang", $lang);
    }
    public static function getPrevSelLang(){
        $session =\JFactory::getSession();
        return $session->get("js_history_sel_lang");
    }

    public static function setFilterAlias($alias){
        $alias = str_replace(" ","-",$alias);
        $alias = (string) preg_replace('/[\x00-\x1F\x7F<>"\'$#%&\?\/\.\)\(\{\}\+\=\[\]\\\,:;]/', '', $alias);
        $alias = JString::strtolower($alias);
    return $alias;
    }

    public static function showMarkStar($rating){
        $jshopConfig = \JSFactory::getConfig();
        $count = floor($jshopConfig->max_mark / $jshopConfig->rating_starparts);
        $star_width = $jshopConfig->rating_star_width;
        $width = $count * $star_width;
        $rating = round($rating);
        $width_active = intval($rating * $star_width / $jshopConfig->rating_starparts);
        $html = "<div class='stars_no_active' style='width:".$width."px'>";
        $html .= "<div class='stars_active' style='width:".$width_active."px'>";
        $html .= "</div>";
        $html .= "</div>";
    return $html;
    }

    public static function getNameImageLabel($id, $type = 1){
    static $listLabels;
        $jshopConfig = \JSFactory::getConfig();
        if (!$jshopConfig->admin_show_product_labels) return "";
        if (!is_array($listLabels)){
            $productLabel = \JSFactory::getTable('productlabel');
            $listLabels = $productLabel->getListLabels();
        }
        $obj = $listLabels[$id];
        if ($type==1)
            return $obj->image;
        else
            return $obj->name;
    }

    public static function getPriceFromCurrency($price, $currency_id = 0, $current_currency_value = 0){
        $jshopConfig = \JSFactory::getConfig();
        if ($currency_id){
            $all_currency = \JSFactory::getAllCurrency();
            $value = $all_currency[$currency_id]->currency_value ?? 1;
            if ($value == 0){
                $value = 1;
            }
            $pricemaincurrency = $price / $value;
        }else{
            $pricemaincurrency = $price;
        }
        if (!$current_currency_value){
            $current_currency_value = $jshopConfig->currency_value;
        }
    return $pricemaincurrency * $current_currency_value;
    }

    public static function listProductUpdateData($products, $setUrl = 0){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $userShop = \JSFactory::getUserShop();
        $taxes = \JSFactory::getAllTaxes();
        if ($jshopConfig->product_list_show_manufacturer){
            $manufacturers = \JSFactory::getAllManufacturer();
        }
        if ($jshopConfig->product_list_show_vendor){
            $vendors = \JSFactory::getAllVendor();
        }
        if ($jshopConfig->show_delivery_time){
            $deliverytimes = \JSFactory::getAllDeliveryTime();
        }

        $image_path = $jshopConfig->image_product_live_path;
        $noimage = $jshopConfig->noimage;

        \JPluginHelper::importPlugin('jshoppingproducts');        

        foreach($products as $key=>$value){
            $products[$key]->_tmp_var_start = "";
            $products[$key]->_tmp_var_image_block = "";
            $products[$key]->_tmp_var_bottom_foto = "";
            $products[$key]->_tmp_var_old_price_ext = "";
            $products[$key]->_tmp_var_bottom_price = "";
            $products[$key]->_tmp_var_bottom_old_price = "";
            $products[$key]->_tmp_var_price_ext  = "";
            $products[$key]->_tmp_var_top_buttons = "";
            $products[$key]->_tmp_var_buttons = "";
            $products[$key]->_tmp_var_bottom_buttons = "";
            $products[$key]->_tmp_var_end = "";
            $use_userdiscount = 1;
            $products[$key]->user_discount = 0;
            if ($jshopConfig->user_discount_not_apply_prod_old_price && $products[$key]->product_old_price>0){
                $use_userdiscount = 0;
            }else{
                $products[$key]->user_discount = $userShop->percent_discount;
            }
            $app->triggerEvent('onListProductUpdateDataProduct', array(&$products, &$key, &$value, &$use_userdiscount));

            $products[$key]->_original_product_price = $products[$key]->product_price;
            $products[$key]->product_price_wp = $products[$key]->product_price;
            $products[$key]->product_price_default = 0;
            if ($jshopConfig->product_list_show_min_price){
                if ($products[$key]->min_price > 0) $products[$key]->product_price = $products[$key]->min_price;
            }
            $products[$key]->show_price_from = 0;
            if ($jshopConfig->product_list_show_min_price && $value->different_prices){
                $products[$key]->show_price_from = 1;
            }

            $products[$key]->product_price = self::getPriceFromCurrency($products[$key]->product_price, $products[$key]->currency_id);
            $products[$key]->product_old_price = self::getPriceFromCurrency($products[$key]->product_old_price, $products[$key]->currency_id);
            $products[$key]->product_price_wp = self::getPriceFromCurrency($products[$key]->product_price_wp, $products[$key]->currency_id);

            $products[$key]->product_price = self::getPriceCalcParamsTax($products[$key]->product_price, $products[$key]->tax_id);
            $products[$key]->product_old_price = self::getPriceCalcParamsTax($products[$key]->product_old_price, $products[$key]->tax_id);
            $products[$key]->product_price_wp = self::getPriceCalcParamsTax($products[$key]->product_price_wp, $products[$key]->tax_id);

            if ($products[$key]->user_discount && $use_userdiscount){
                $products[$key]->product_price_default = $products[$key]->_original_product_price;
                $products[$key]->product_price_default = self::getPriceFromCurrency($products[$key]->product_price_default, $products[$key]->currency_id);
                $products[$key]->product_price_default = self::getPriceCalcParamsTax($products[$key]->product_price_default, $products[$key]->tax_id);

                $products[$key]->product_price = self::getPriceDiscount($products[$key]->product_price, $products[$key]->user_discount);
                $products[$key]->product_old_price = self::getPriceDiscount($products[$key]->product_old_price, $products[$key]->user_discount);
                $products[$key]->product_price_wp = self::getPriceDiscount($products[$key]->product_price_wp, $products[$key]->user_discount);
            }

            if ($jshopConfig->list_products_calc_basic_price_from_product_price){
                $products[$key]->basic_price_info = self::getProductBasicPriceInfo($value, $products[$key]->product_price_wp);
            }else{
                $products[$key]->basic_price_info = self::getProductBasicPriceInfo($value, $products[$key]->product_price);
            }

            if ($value->tax_id){
                $products[$key]->tax = $taxes[$value->tax_id];
            } else {
				$products[$key]->tax = 0;
			}

            if ($jshopConfig->product_list_show_manufacturer && $value->product_manufacturer_id && isset($manufacturers[$value->product_manufacturer_id])){
                $products[$key]->manufacturer = $manufacturers[$value->product_manufacturer_id];
            }else{
                $products[$key]->manufacturer = new \stdClass();
                $products[$key]->manufacturer->name = '';
            }
            if ($jshopConfig->admin_show_product_extra_field){
                $products[$key]->extra_field = self::getProductExtraFieldForProduct($value);
            } else {
                $products[$key]->extra_field = '';
            }
            if ($jshopConfig->product_list_show_vendor){
                $vendordata = $vendors[$value->vendor_id];
                $vendordata->products = self::SEFLink("index.php?option=com_jshopping&controller=vendor&task=products&vendor_id=".$vendordata->id,1);
                $products[$key]->vendor = $vendordata;
            }else{
                $products[$key]->vendor = '';
            }
            if ($jshopConfig->hide_delivery_time_out_of_stock && $products[$key]->product_quantity<=0){
                $products[$key]->delivery_times_id = 0;
                $value->delivery_times_id = 0;
            }
            if ($jshopConfig->show_delivery_time && isset($value->delivery_times_id) && $value->delivery_times_id) {
                $products[$key]->delivery_time = $deliverytimes[$value->delivery_times_id];
            }else{
                $products[$key]->delivery_time = '';
            }
            $products[$key]->_display_price = self::getDisplayPriceForProduct($products[$key]->product_price);
            if (!$products[$key]->_display_price){
                $products[$key]->product_old_price = 0;
                $products[$key]->product_price_default = 0;
                $products[$key]->basic_price_info['price_show'] = 0;
                $products[$key]->tax = 0;
                $jshopConfig->show_plus_shipping_in_product = 0;
            }
            if ($jshopConfig->product_list_show_qty_stock){
                $products[$key]->qty_in_stock = \JSHelper::getDataProductQtyInStock($products[$key]);
            }
            $image = self::getPatchProductImage($products[$key]->image, 'thumb');
            $products[$key]->product_name_image = $products[$key]->image;
            $products[$key]->product_thumb_image = $image;
            if (!$image) $image = $noimage;
            $products[$key]->image = $image_path."/".$image;
            $products[$key]->template_block_product = "product.php";
            if (!$jshopConfig->admin_show_product_labels) $products[$key]->label_id = null;
            if ($products[$key]->label_id){
                $image = \JSHelper::getNameImageLabel($products[$key]->label_id);
                if ($image){
                    $products[$key]->_label_image = $jshopConfig->image_labels_live_path."/".$image;
                }
                $products[$key]->_label_name = \JSHelper::getNameImageLabel($products[$key]->label_id, 2);
            }
            if ($jshopConfig->display_short_descr_multiline){
                $products[$key]->short_description = nl2br($products[$key]->short_description);
            }            
            if ($jshopConfig->product_use_main_category_id && isset($products[$key]->main_category_id) && $products[$key]->main_category_id && self::checkCategoryAccess($products[$key]->main_category_id)) {
                $products[$key]->orig_category_id = $products[$key]->category_id;
                $products[$key]->category_id = $products[$key]->main_category_id;
            }
            if (!$jshopConfig->product_img_seo) {
                $products[$key]->img_alt = $products[$key]->name;
                $products[$key]->img_title = $products[$key]->name;
            } else {
                $main_image = self::getProductImageInfo($products[$key]->product_id, $products[$key]->product_name_image);
                $products[$key]->img_alt = $main_image->name ?? '';
                $products[$key]->img_title = $main_image->title ?? '';
            }
        }

        if ($setUrl){
            self::addLinkToProducts($products, 0, 1);
        }

        $app->triggerEvent('onListProductUpdateData', array(&$products));
    return $products;
    }

    public static function getProductImageInfo($product_id, $image_name) {
        $db = \JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_products_images` WHERE product_id=".$db->q($product_id)." AND image_name=".$db->q($image_name);
        $db->setQuery($query);
        return $db->loadObject();
    }

    public static function checkCategoryAccess($cat_id) {
		static $res = [];
		if (!isset($res[$cat_id])) {
			$app = \JFactory::getApplication();
			$db = \JFactory::getDBO();
			$user = \JFactory::getUser();
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$adv_query =' AND cat.access IN ('.$groups.')';
			$query = "SELECT cat.category_id FROM `#__jshopping_categories` AS cat
				WHERE cat.category_publish=1 AND cat.category_id=".(int)$cat_id." ".$adv_query;
			$app->triggerEvent('onListProductUpdateDatacheckCategoryAccess', array(&$cat_id, &$query));
			$db->setQuery($query);
			$res[$cat_id] = ($db->loadResult() > 0);
		}
		return $res[$cat_id];
    }

    public static function getProductBasicPriceInfo($obj, $price){
        $jshopConfig = \JSFactory::getConfig();
        $price_show = $obj->weight_volume_units!=0;

        if (!$jshopConfig->admin_show_product_basic_price || $price_show==0){
            return array("price_show"=>0);
        }

        $units = \JSFactory::getAllUnits();
        $unit = $units[$obj->basic_price_unit_id];
        $basic_price = round($price, $jshopConfig->decimal_count) / $obj->weight_volume_units * $unit->qty;

        return array("price_show"=>$price_show, "basic_price"=>$basic_price, "name"=>$unit->name, "unit_qty"=>$unit->qty);
    }

    public static function getProductExtraFieldForProduct($product){
        $fields = \JSFactory::getAllProductExtraField();
        $fieldvalues = \JSFactory::getAllProductExtraFieldValue();
        $displayfields = \JSFactory::getDisplayListProductExtraFieldForCategory($product->category_id);
        $rows = [];
        foreach($displayfields as $field_id){
            $field_name = "extra_field_".$field_id;
            if ($fields[$field_id]->type==0){
                if ($product->$field_name!=0 && $product->$field_name!=''){
                    $listid = explode(',', $product->$field_name);
                    $tmp = [];
                    foreach($listid as $extrafiledvalueid){
						if (isset($fieldvalues[$extrafiledvalueid])) {
							$tmp[] = $fieldvalues[$extrafiledvalueid];
						}
                    }
                    $extra_field_value = implode(", ", $tmp);
                    $rows[$field_id] = array("name"=>$fields[$field_id]->name, "description"=>$fields[$field_id]->description, "value"=>$extra_field_value);
                }
            }else{
                if ($product->$field_name!=""){
                    $rows[$field_id] = array("name"=>$fields[$field_id]->name, "description"=>$fields[$field_id]->description, "value"=>$product->$field_name);
                }
            }
        }
    return $rows;
    }

    public static function getPriceTaxRatioForProducts($products, $group='tax'){
        $prodtaxes = [];
        foreach($products as $k=>$v){
            if (!isset($prodtaxes[$v[$group]])) $prodtaxes[$v[$group]] = 0;
            $prodtaxes[$v[$group]]+= $v['price']*$v['quantity'];
        }
        $sumproducts = array_sum($prodtaxes);
        foreach($prodtaxes as $k=>$v){
            if ($sumproducts>0){
                $prodtaxes[$k] = $v/$sumproducts;
            } else {
                $prodtaxes[$k] = 0;
            }
        }
    return $prodtaxes;
    }

    public static function getFixBrutopriceToTax($price, $tax_id){
        $jshopConfig = \JSFactory::getConfig();
        if ($jshopConfig->no_fix_brutoprice_to_tax==1){
            return $price;
        }
        $taxoriginal = \JSFactory::getAllTaxesOriginal();
        $taxes = \JSFactory::getAllTaxes();
        $tax = $taxes[$tax_id] ?? 0;
        $tax2 = $taxoriginal[$tax_id] ?? 0;
        if ($tax != $tax2){
            $price = $price / (1 + $tax2 / 100);
            $price = $price * (1+$tax/100);
        }
    return $price;
    }

    public static function getPriceCalcParamsTax($price, $tax_id, $products=[]){
        $jshopConfig = \JSFactory::getConfig();
        $taxes = \JSFactory::getAllTaxes();
        if ($tax_id==-1){
            $prodtaxes = \JSHelper::getPriceTaxRatioForProducts($products);
        }
        if ($jshopConfig->display_price_admin==0 && $tax_id>0){
            $price = self::getFixBrutopriceToTax($price, $tax_id);
        }
        if ($jshopConfig->display_price_admin==0 && $tax_id==-1){
            $prices = [];
            $prodtaxesid = \JSHelper::getPriceTaxRatioForProducts($products,'tax_id');
            foreach($prodtaxesid as $k=>$v){
                $prices[$k] = self::getFixBrutopriceToTax($price*$v, $k);
            }
            $price = array_sum($prices);
        }
        if ($tax_id>0){
            $tax = $taxes[$tax_id] ?? 0;
        }elseif ($tax_id==-1){
            $prices = [];
            foreach($prodtaxes as $k=>$v){
                $prices[] = array('tax'=>$k, 'price'=>$price*$v);
            }
        }else{
            $taxlist = array_values($taxes);
            $tax = $taxlist[0] ?? 0;
        }
        if ($jshopConfig->display_price_admin == 1 && $jshopConfig->display_price_front_current == 0){
            if ($tax_id==-1){
                $price = 0;
                foreach($prices as $v){
                    $price+= $v['price'] * (1 + $v['tax'] / 100);
                }
            }else{
                $price = $price * (1 + $tax / 100);
            }
        }
        if ($jshopConfig->display_price_admin == 0 && $jshopConfig->display_price_front_current == 1){
            if ($tax_id==-1){
                $price = 0;
                foreach($prices as $v){
                    $price+= $v['price'] / (1 + (float)$v['tax'] / 100);
                }
            }else{
                $price = $price / (1 + $tax / 100);
            }
        }
    return $price;
    }

    public static function changeDataUsePluginContent(&$data, $type){
        $app =\JFactory::getApplication();
        \JPluginHelper::importPlugin('content');
        $obj = new \stdClass();
        $params = $app->getParams('com_content');

        if ($type=="product"){
            $obj->product_id = $data->product_id;
        }
        if ($type=="category"){
            $obj->category_id = $data->category_id;
        }
        if ($type=="manufacturer"){
            $obj->manufacturer_id = $data->manufacturer_id;
        }
        if (!isset($data->name)) $data->name = '';
        $obj->text = $data->description;
        $obj->title = $data->name;
        $results = $app->triggerEvent('onContentPrepare', array('com_content.article', &$obj, &$params, 0));
        $data->description = $obj->text;
        return 1;
    }

    public static function productTaxInfo($tax, $display_price = null){
        if (!isset($display_price)) {
            $jshopConfig = \JSFactory::getConfig();
            $display_price = $jshopConfig->display_price_front_current;
        }
        if ($display_price==0){
            return sprintf(\JText::_('JSHOP_INC_PERCENT_TAX'), self::formattax($tax));
        }else{
            return sprintf(\JText::_('JSHOP_PLUS_PERCENT_TAX'), self::formattax($tax));
        }
    }

    public static function displayTotalCartTaxName($display_price = null){
        if (!isset($display_price)) {
            $jshopConfig = \JSFactory::getConfig();
            $display_price = $jshopConfig->display_price_front_current;
        }
        if ($display_price==0){
            return \JText::_('JSHOP_INC_TAX');
        }else{
            return \JText::_('JSHOP_PLUS_TAX');
        }
    }

    public static function getPriceTaxValue($price, $tax, $price_netto = 0){
        if ($price_netto==0){
            $tax_value = $price * $tax / (100 + $tax);
        }else{
            $tax_value = $price * $tax / 100;
        }
    return $tax_value;
    }

    public static function getCorrectedPriceForQueryFilter($price) {
        $jshopConfig = \JSFactory::getConfig();
        $taxes = \JSFactory::getAllTaxes();
        $taxlist = array_values($taxes);
        $tax = $taxlist[0] ?? 0;

        if ($jshopConfig->display_price_admin == 1 && $jshopConfig->display_price_front_current == 0){
            $price = $price / (1 + $tax / 100);
        }
        if ($jshopConfig->display_price_admin == 0 && $jshopConfig->display_price_front_current == 1){
            $price = $price * (1 + $tax / 100);
        }
        if ($jshopConfig->currency_value != 0){
            $price = $price / $jshopConfig->currency_value;
        }
        return $price;
    }

    public static function updateAllprices( $ignore = [] ){
        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load();
        $cart->updateCartProductPrice();

        $sh_pr_method_id = $cart->getShippingPrId();
        if ($sh_pr_method_id){
            $shipping_method_price = \JSFactory::getTable('shippingMethodPrice');
            $shipping_method_price->load($sh_pr_method_id);
            $prices = $shipping_method_price->calculateSum($cart);
            $cart->setShippingsDatas($prices, $shipping_method_price);
        }
        $payment_method_id = $cart->getPaymentId();
        if ($payment_method_id){
            $paym_method = \JSFactory::getTable('paymentmethod');
            $paym_method->load($payment_method_id);
            $paym_method->setCart($cart);
            $cart->setDisplayItem(1, 1);
            $price = $paym_method->getPrice();
            $cart->setPaymentDatas($price, $paym_method);
        }

        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->load('wishlist');
        $cart->updateCartProductPrice();
    }

    public static function setNextUpdatePrices(){
        $session =\JFactory::getSession();
        $session->set('js_update_all_price', 1);
    }

    public static function getMysqlVersion(){
        $session =\JFactory::getSession();
        $mysqlversion = $session->get("js_get_mysqlversion");
        if ($mysqlversion ==""){
            $db = \JFactory::getDBO();
            $query = "select version() as v";
            $db->setQuery($query);
            $mysqlversion = $db->loadResult();
            preg_match('/\d+\.\d+\.\d+/',$mysqlversion,$matches);
            $mysqlversion = $matches[0];
            $session->set("js_get_mysqlversion", $mysqlversion);
        }
        return $mysqlversion;
    }

    public static function filterAllowValue($data, $type){

        if ($type=="int+"){
            if (is_array($data)){
                foreach($data as $k=>$v){
                    $v = intval($v);
                    if ($v>0){
                        $data[$k] = $v;
                    }else{
                        unset($data[$k]);
                    }
                }
            }
        }

        if ($type=="array_int_k_v+"){
            if (is_array($data)){
                foreach($data as $k=>$v){
                    $k = intval($k);
                    if (is_array($v)){
                        foreach($v as $k2=>$v2){
                            $k2 = intval($k2);
                            $v2 = intval($v2);
                            if ($v2>0){
                                $data[$k][$k2] = $v2;
                            }else{
                                unset($data[$k][$k2]);
                            }
                        }
                    }
                }
            }
        }

        if ($type=='array_int_k_v_not_empty'){
            if (is_array($data)){
                foreach($data as $k=>$v){
                    $k = intval($k);
                    if (is_array($v)){
                        foreach($v as $k2=>$v2){
                            $k2 = intval($k2);
                            if ($v2!=''){
                                $data[$k][$k2] = $v2;
                            }else{
                                unset($data[$k][$k2]);
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    public static function getListFromStr($stelist){
        if (isset($stelist) && preg_match('/\,/', $stelist)){
            return self::filterAllowValue(explode(',',$stelist), 'int+');
        }else{
            return null;
        }
    }

    /**
    * spec public static function additional query for product list
    */
    public static function getQueryListProductsExtraFields(){
        $query = "";
        $list = \JSFactory::getAllProductExtraField();
        $jshopConfig = \JSFactory::getConfig();
        $config_list = $jshopConfig->getProductListDisplayExtraFields();
        foreach($list as $v){
            if (in_array($v->id, $config_list)){
                $query .= ", prod_to_ef.`extra_field_".$v->id."` ";
            }
        }
    return $query;
    }

    public static function getLicenseKeyAddon($alias){
    static $keys;
        if (!isset($keys)) $keys = [];
        if (!isset($keys[$alias])){
            $addon = \JSFactory::getTable('addon');
            $keys[$alias] = $addon->getKeyForAlias($alias);
        }
    return $keys[$alias];
    }

    public static function getQuerySortDirection($fieldnum, $ordernum){
        $dir = "ASC";
        if ($ordernum) {
            $dir = "DESC";
            if ($fieldnum==5 || $fieldnum==6) $dir = "ASC";
        } else {
            $dir = "ASC";
            if ($fieldnum==5 || $fieldnum==6) $dir = "DESC";
        }
    return $dir;
    }

    public static function getImgSortDirection($fieldnum, $ordernum){
        if ($ordernum) {
            $image = 'arrow_down.gif';
        } else {
            $image = 'arrow_up.gif';
        }
    return $image;
    }

    public static function printContent(){
        $print = \JFactory::getApplication()->input->getInt("print");
        $link =  str_replace("&", '&amp;', $_SERVER["REQUEST_URI"]);
        if (strpos($link,'?')===FALSE)
            $tmpl = "?tmpl=component&amp;print=1";
        else
            $tmpl = "&amp;tmpl=component&amp;print=1";

        $html = '<div class="jshop_button_print">';
        if ($print==1)
            $html .= '<a onclick="window.print();return false;" href="#" title="'.\JText::_('JSHOP_PRINT').'"><img src="'.\JURI::root().'components/com_jshopping/images/print.png" alt=""  /></a>';
        else
            $html .= '<a href="'.$link.$tmpl.'" title="'.\JText::_('JSHOP_PRINT').'" onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" rel="nofollow"><img src="'.\JURI::root().'components/com_jshopping/images/print.png" alt=""  /></a>';
        $html .= '</div>';
        print $html;
    }

    public static function getPageHeaderOfParams(&$params){
        $header = "";
        if ($params->get('show_page_heading') && $params->get('page_heading')){
            $header = $params->get('page_heading');
        }
    return $header;
    }
    
    public static function getMessageLevel($type){    
        $val = ['message'=>0, 'warning'=>E_WARNING, 'notice'=>E_NOTICE, 'error'=>E_ERROR];
        return (int)$val[$type];
    }

    public static function getMessageJson(){
        $errors = \JSError::getErrors();        
        $rows = [];       
        foreach($errors as $k => $e){
            $message = str_replace("<br/>", "\n", $e['message']);
            $code = 0;
            if ($k == (count($errors) - 1)) {
                $code = \JSError::getLastErrorCode();
            }
            $rows[] = array("level"=>self::getMessageLevel($e['type']), "code"=>$code, "message"=>$message);
        }
    return json_encode($rows);
    }

    public static function getOkMessageJson($cart){
        header("Content-type: application/json; charset=utf-8");
        $errors = \JSError::getErrors();
        if (count($errors)){
            return \JSHelper::getMessageJson();
        }else{
            return json_encode($cart);
        }
    }

    public static function getAccessGroups(){
        $db = \JFactory::getDBO();
        $query = "select id,title,rules from #__viewlevels order by ordering";
        $db->setQuery($query);
        $accessgroups = $db->loadObjectList();
    return $accessgroups;
    }

    public static function getDisplayPriceShop(){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        $display_price = 1;
        if ($jshopConfig->displayprice==1){
            $display_price = 0;
        }elseif($jshopConfig->displayprice==2 && !$user->id){
            $display_price = 0;
        }
    return $display_price;
    }

    public static function getDisplayPriceForProduct($price){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        $display_price = 1;
        if ($jshopConfig->displayprice==1){
            $display_price = 0;
        }elseif($jshopConfig->displayprice==2 && !$user->id){
            $display_price = 0;
        }
        if ($display_price && $price==0 && $jshopConfig->user_as_catalog){
            $display_price = 0;
        }
        if ($display_price && $price==0 && $jshopConfig->product_hide_price_null){
            $display_price = 0;
        }
    return $display_price;
    }

    public static function getDocumentType(){
    return \JFactory::getDocument()->getType();
    }

    public static function sprintAtributeInCart($atribute){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher =\JFactory::getApplication();
        $html = "";
        if (count($atribute)) $html .= '<div class="list_attribute">';
        foreach($atribute as $attr){
            \JFactory::getApplication()->triggerEvent('onBeforeSprintAtributeInCart', array(&$attr) );
            $html .= '<p class="jshop_cart_attribute"><span class="name">'.$attr->attr.'</span>: <span class="value">'.$attr->value.'</span></p>';
        }
        if (count($atribute)) $html .= '</div>';
        \JFactory::getApplication()->triggerEvent('onAfterSprintAtributeInCartHtml', array(&$atribute, &$html));
    return $html;
    }

    public static function sprintFreeAtributeInCart($freeatribute){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher = \JFactory::getApplication();
        $html = "";
        if (count($freeatribute)) $html .= '<div class="list_free_attribute">';
        foreach($freeatribute as $attr){
            \JFactory::getApplication()->triggerEvent('onBeforeSprintFreeAtributeInCart', array(&$attr) );
            $html .= '<p class="jshop_cart_attribute"><span class="name">'.$attr->attr.'</span>: <span class="value">'.$attr->value.'</span></p>';
        }
        if (count($freeatribute)) $html .= '</div>';
        \JFactory::getApplication()->triggerEvent('onAfterSprintFreeAtributeInCartHtml', array(&$freeatribute, &$html));
    return $html;
    }

    public static function sprintFreeExtraFiledsInCart($extra_fields){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher =\JFactory::getApplication();
        $html = "";
        if (count($extra_fields)) $html .= '<div class="list_extra_field">';
        foreach($extra_fields as $f){
            \JFactory::getApplication()->triggerEvent('onBeforeSprintExtraFieldsInCart', array(&$f) );
            $html .= '<p class="jshop_cart_extra_field"><span class="name">'.$f['name'].'</span>: <span class="value">'.$f['value'].'</span></p>';
        }
        if (count($extra_fields)) $html .= '</div>';
    return $html;
    }

    public static function sprintAtributeInOrder($atribute, $type="html"){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher =\JFactory::getApplication();
        \JFactory::getApplication()->triggerEvent('onBeforeSprintAtributeInOrder', array(&$atribute, $type));
        if ($type=="html"){
            $html = nl2br($atribute);
        }else{
            $html = $atribute;
        }
        \JFactory::getApplication()->triggerEvent('onAfterSprintAtributeInOrderHtml', array(&$atribute, &$html) );
    return $html;
    }

    public static function sprintFreeAtributeInOrder($freeatribute, $type="html"){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher =\JFactory::getApplication();
        \JFactory::getApplication()->triggerEvent('onBeforeSprintFreeAtributeInOrder', array(&$freeatribute, $type));
        if ($type=="html"){
            $html = nl2br($freeatribute);
        }else{
            $html = $freeatribute;
        }
        \JFactory::getApplication()->triggerEvent('onAfterSprintFreeAtributeInOrderHtml', array(&$freeatribute, &$html) );
    return $html;
    }

    public static function sprintExtraFiledsInOrder($extra_fields, $type="html"){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher =\JFactory::getApplication();
        \JFactory::getApplication()->triggerEvent('onBeforeSprintExtraFieldsInOrder', array(&$extra_fields, $type));
        if ($type=="html"){
            $html = nl2br($extra_fields);
        }else{
            $html = $extra_fields;
        }
    return $html;
    }

    public static function sprintBasicPrice($prod){
        if (is_object($prod)) $prod = (array)$prod;
        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher =\JFactory::getApplication();
        \JFactory::getApplication()->triggerEvent('onBeforeSprintBasicPrice', array(&$prod));
        $html = '';
        if ($prod['basicprice']>0){
            $html = self::formatprice($prod['basicprice'])." / ".$prod['basicpriceunit'];
        }
    return $html;
    }

    public static function getDataProductQtyInStock($product){
        $qty = $product->product_quantity;
        if (method_exists($product, 'getQty')) {
            $qty = $product->getQty();
        }
        $qty = floatval($qty);
        $qty_in_stock = array("qty"=>$qty, "unlimited"=>$product->unlimited);
        if ($qty_in_stock['qty']<0) $qty_in_stock['qty'] = 0;
    return $qty_in_stock;
    }

    public static function sprintQtyInStock($qty_in_stock){
        if (!is_array($qty_in_stock)){
            return $qty_in_stock;
        }else{
            if ($qty_in_stock['unlimited']){
                return \JText::_('JSHOP_UNLIMITED');
            }else{
                return $qty_in_stock['qty'];
            }
        }
    }

    public static function fixRealVendorId($id){
        if ($id==0){
            $mainvendor = \JSFactory::getMainVendor();
            $id = $mainvendor->id;
        }
    return $id;
    }

    public static function xhtmlUrl($url, $filter=1){
        if ($filter){
            $url = self::jsFilterUrl($url);
        }
        $url = str_replace("&","&amp;",$url);
    return $url;
    }

    public static function jsFilterUrl($url, $extra = 0){
        $url = strip_tags($url ?? '');
        if ($extra){
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $trans = array("'"=>"&#039;", '"'=>"&quot;", '('=>'&#40;', ')'=>'&#41;', ';'=>'&#59;');
            $url = strtr($url, $trans);
        }
    return $url;
    }

    public static function getJsDate($date = 'now', $format = 'Y-m-d H:i:s', $local = true) {
        $jdate = new \JDate($date, 'UTC');
        $jdate->setTimezone(
            new \DateTimeZone(
                \JFactory::getConfig()->get('offset')
            )
        );
        return $jdate->format($format, $local);
    }

    public static function getJsTimestamp($date = 'now', $local = true) {
        return strtotime(self::getJsDate($date, 'Y-m-d H:i:s', $local));
    }

    public static function getCalculateDeliveryDay($day, $date=null){
        if (!$date){
            $date = self::getJsDate();
        }
        $time = intval(strtotime($date) + $day*86400);
    return date('Y-m-d H:i:s', $time);
    }

    public static function datenull($date){
        if (!isset($date)) {
            return true;
        } else {
            return substr($date,0,1) == false;
        }
    }

    public static function file_get_content_curl($url, $timeout = 5){
        if (function_exists('curl_init')){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            $str = curl_exec($ch);
            curl_close($ch);
            return $str;
        }else{
            return null;
        }
    }

    public static function getJsDateDB($str, $format='%d.%m.%Y'){
        $f = str_replace(array("%d","%m","%Y"), array('dd','mm','yyyy'), $format);
        $pos = array(strpos($f, 'y'),strpos($f, 'm'),strpos($f, 'd'));
        $date = substr($str, $pos[0], 4).'-'.substr($str, $pos[1], 2).'-'.substr($str, $pos[2], 2);
    return $date;
    }
    public static function getDisplayDate($date, $format='%d.%m.%Y'){
        if (self::datenull($date)){
            return '';
        }
        $adate = array(substr($date, 0, 4), substr($date, 5, 2), substr($date, 8, 2));
        $str = str_replace(array("%Y","%m","%d"), $adate, $format);
    return $str;
    }
    public static function getPatchProductImage($name, $prefix = '', $patchtype = 0){
        $jshopConfig = \JSFactory::getConfig();
        if ($name==''){
            return '';
        }
        if ($prefix!=''){
            $name = $prefix."_".$name;
        }
        if ($patchtype==1){
            $name = $jshopConfig->image_product_live_path."/".$name;
        }
        if ($patchtype==2){
            $name = $jshopConfig->image_product_path."/".$name;
        }
    return $name;
    }

    public static function getDBFieldNameFromConfig($name){
        $lang = \JSFactory::getLang();
        $tmp = explode('.', $name);
        if (count($tmp)>1){
            $res = $tmp[0].'.';
            $field = $tmp[1];
        }else{
            $res = '';
            $field = $tmp[0];
        }
        $tmp2 = explode(':', $field);
        if (count($tmp2)>1 && $tmp2[0]=='ml'){
            $res .= '`'.$lang->get($tmp2[1]).'`';
        }else{
            $res .= '`'.$field.'`';
        }
    return $res;
    }

    public static function json_value_encode($val, $textfix = 0){
        if ($textfix){
            $val = str_replace(
                array("\\", "/", "\n", "\t", "\r", "\b", "\f"),
                array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f'),
                $val);
        }
        $val = str_replace('"', '\"', $val);
        return $val;
    }

    public static function initLoadJoomshoppingLanguageFile(){
    static $load;
        if (!\JFactory::getApplication()->input->getInt('no_lang') && !$load){
            \JSFactory::loadLanguageFile();
            $load = 1;
        }
    }

    public static function reloadPriceJoomshoppingNewCurrency($back = ''){
        header("Cache-Control: no-cache, must-revalidate");
        \JSHelper::updateAllprices();
        if ($back!=''){
            \JFactory::getApplication()->redirect($back);
        }
    }

    public static function disableStrictMysql(){
        $db = \JFactory::getDBO();
        $db->setQuery("set @@sql_mode = ''");
        $db->execute();
    }
}