<?php
/**
* @version      5.1.3 03.07.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;

defined('_JEXEC') or die();

class ConfigTable{

    public function load($keys = null, $reset = true) {
        if (is_null($keys)) {
            $keys = $this->id;
        }
        $this->id = $keys;
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select([$db->qn('key'), $db->qn('value')])->from($db->qn('#__jshopping_configs'))->where([$db->qn('config_id') . '=' . $db->q($keys)]);        
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        foreach ($fields as $v) {
            $this->{$v->key} = $v->value;
        }
        if (empty($fields)) {
            return false;
        } else {
            return true;
        }
    }

    public function bind($src, $ignore = array()) {
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onBeforeBindConfigTable', array(&$obj, &$src, &$ignore));

        foreach ($src as $k => $v) {
			if (!in_array($k, $ignore)) {
				if (isset($src[$k])) {
					$this->$k = $src[$k];
				}
			}
		}
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterBindConfigTable', array(&$obj, &$src, &$ignore));
        return true;
    }

    public function store($updateNulls = false){
        $config_id = $this->id;
        foreach (get_object_vars($this) as $k => $v) {
			if (\is_array($v) || \is_object($v) || $k[0] === '_' || $k === 'id') {
				continue;
			}
            if ($v === null && $updateNulls === false) {
                continue;
            }            
            $data = $this->getFieldFromKey($k, $config_id);
            if (isset($data->id) && $data->id) {
                $this->updateRow($data->id, $k, $v, $config_id);
            } else {
                $this->insertRow($k, $v, $config_id);
            }
        }
        return true;
    }

    protected function getFieldFromKey($key, $config_id = 1) {        
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->qn('#__jshopping_configs'))->where([$db->qn('key') . '=' . $db->q($key), $db->qn('config_id') . '=' . $db->q($config_id)]);
        $db->setQuery($query);
        return $db->loadObject();
    }

    protected function updateRow($id, $key, $value, $config_id = 1) {
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__jshopping_configs'))
            ->set([$db->qn('key') . '=' . $db->q($key), $db->qn('value') . '=' . $db->q($value), $db->qn('config_id') . '=' . $db->q($config_id)])
            ->where([$db->qn('id') . '=' . $db->q($id)]);
        $db->setQuery($query);
        $db->execute();
    }
    
    protected function insertRow($key, $value, $config_id = 1) {
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->insert($db->qn('#__jshopping_configs'))
            ->columns($db->qn(['key', 'value', 'config_id']))
            ->values(implode(',', [$db->q($key), $db->q($value), $db->q($config_id)]));
        $db->setQuery($query);
        $db->execute();
    }

    function transformPdfParameters() {
        if (isset($this->pdf_parameters) && is_array($this->pdf_parameters)) {
            $this->pdf_parameters = implode(":", $this->pdf_parameters);
        }
    }

    function loadCurrencyValue(){
        $app = \JFactory::getApplication();
        $session = \JFactory::getSession();
        $id_currency_session = $session->get('Js_id_currency');
        $id_currency = $app->input->getInt('id_currency');
        $main_currency = $this->mainCurrency ?? 0;
        if ($this->default_frontend_currency){
            $main_currency = $this->default_frontend_currency;
        }		
        
        if ($session->get('Js_id_currency_orig') && $session->get('Js_id_currency_orig')!=$main_currency) {
            $id_currency_session = 0;
            $session->set('Js_update_all_price', 1);
        }

        if (!$id_currency && $id_currency_session){
            $id_currency = $id_currency_session;
        }
        $session->set('Js_id_currency_orig', $main_currency);

        if ($id_currency){
            $this->cur_currency = $id_currency;
        }else{
            $this->cur_currency = $main_currency;
        }
        if (!$app->isClient('administrator')){
            $session->set('Js_id_currency', $this->cur_currency);
        }
        $all_currency = \JSFactory::getAllCurrency();
		if (isset($all_currency[$this->cur_currency])) {
			$current_currency = $all_currency[$this->cur_currency];
		} else {
			$current_currency = array_shift($all_currency);
		}        
        if (!$current_currency->currency_value) {
            $current_currency->currency_value = 1;
        }
        $this->currency_value = $current_currency->currency_value;
        $this->currency_code = $current_currency->currency_code;
        $this->currency_code_iso = $current_currency->currency_code_iso;
    }
    
    function getDisplayPriceFront(){
        $display_price = $this->display_price_front;

        if ($this->use_extend_display_price_rule > 0){
            $adv_user = \JSFactory::getUserShop();
            $country_id = $adv_user->country;
            $client_type = $adv_user->client_type;
            if (!$adv_user->user_id){
                $adv_user = \JSFactory::getUserShopGuest();
                $country_id = $adv_user->country;
                $client_type = $adv_user->client_type;
            }
            if (!$country_id){
                $country_id = $this->default_country;
            }    
            if ($country_id){
                $configDisplayPrice = \JSFactory::getTable('configdisplaypriceTable');
                $rows = $configDisplayPrice->getList();
                foreach($rows as $v){
                    if (in_array($country_id, $v->countries)){
                        if ($client_type==2){
                            $display_price = $v->display_price_firma;
                        }else{
                            $display_price = $v->display_price;
                        }
                    }
                }
            }
        }
        return $display_price;
    }
    
    function setDisplayPriceFront($display_price){
        if ($display_price==='0' || $display_price==='1' || $display_price===0 || $display_price===1){
            $this->display_price_front_current = (int)$display_price;
        }
    }
    
    function getListFieldsRegister(){
        $config = new \stdClass();
        include(JPATH_JOOMSHOPPING."/config/default_config.php");
        if ($this->fields_register!=""){
            $data = unserialize($this->fields_register);
        }else{
            $data = array();
        }
        foreach($fields_client as $type=>$_v){
            foreach($fields_client[$type] as $k=>$v){
                if (!isset($data[$type][$v])){
                    $data[$type][$v] = array('display'=>0,'require'=>0);                    
                }
                if (!isset($data[$type][$v]['display'])) $data[$type][$v]['display'] = 0;
                if (!isset($data[$type][$v]['require'])) $data[$type][$v]['require'] = 0;
            }
        }  
        foreach($fields_client['editaccount'] as $k=>$v){
            $data['editaccountadmin'][$v] = array('display'=>1, 'require'=>0);
        }      
    return $data;
    }
	
	function getListFieldsRegisterType($type){
		$tmp_fields = $this->getListFieldsRegister();
        return $tmp_fields[$type];
	}
    
    function getEnableDeliveryFiledRegistration($type='address'){
        $tmp_fields = $this->getListFieldsRegister();
        $config_fields = (array)$tmp_fields[$type];
        $count = 0;
        foreach($config_fields as $k=>$v){
            if (substr($k, 0, 2)=="d_" && $v['display']==1) $count++;
        }
    return ($count>0);
    }
    
    function getProductListDisplayExtraFields(){
        if ($this->product_list_display_extra_fields!=""){
            return unserialize($this->product_list_display_extra_fields);
        }else{
            return array();
        }
    }

    function setProductListDisplayExtraFields($data){
        if (is_array($data)){
            $this->product_list_display_extra_fields = serialize($data);
        }else{
            $this->product_list_display_extra_fields = serialize(array());
        }
    }

    function getFilterDisplayExtraFields(){
        if ($this->filter_display_extra_fields!=""){
            return unserialize($this->filter_display_extra_fields);
        }else{
            return array();
        }
    }
    
    function setFilterDisplayExtraFields($data){
        if (is_array($data)){
            $this->filter_display_extra_fields = serialize($data);
        }else{
            $this->filter_display_extra_fields = serialize(array());
        }
    }
    
    function getProductHideExtraFields(){
        if ($this->product_hide_extra_fields!=""){
            return unserialize($this->product_hide_extra_fields);
        }else{
            return array();
        }
    }
    
    function setProductHideExtraFields($data){
        if (is_array($data)){
            $this->product_hide_extra_fields = serialize($data);
        }else{
            $this->product_hide_extra_fields = serialize(array());
        }
    }
    
    function getCartDisplayExtraFields(){
        if ($this->cart_display_extra_fields!=""){
            return unserialize($this->cart_display_extra_fields);
        }else{
            return array();
        }
    }
    
    function setCartDisplayExtraFields($data){
        if (is_array($data)){
            $this->cart_display_extra_fields = serialize($data);
        }else{
            $this->cart_display_extra_fields = serialize(array());
        }
    }
    
    function updateNextOrderNumber(){
        $db = \JFactory::getDBO();
        $query = "update `#__jshopping_configs` set `value`=`value`+1 where `key`='next_order_number' and `config_id`=".intval($this->id);
        $db->setQuery($query);
        $db->execute();
    }
	
    function getNextOrderNumber($update = 0){
        $db = \JFactory::getDBO();
		if ($update){
			$db->lockTable('#__jshopping_configs');
		}
        $query = "select `value` from `#__jshopping_configs` where `key`='next_order_number' and  `config_id`=".intval($this->id);
        $db->setQuery($query);
        $next_order_number = $db->loadResult();
		if ($update){
			$this->updateNextOrderNumber();
			$db->unlockTables();
		}
		return $next_order_number;
    }

    function getVersion(){
        $data = \JInstaller::parseXMLInstallFile($this->admin_path . "jshopping.xml");
        return $data['version'];
    }
    
    function loadLang(){
        $this->cur_lang = \JFactory::getLanguage()->getTag();
    }
    
    function loadFrontLand(){
        $params = \JComponentHelper::getParams('com_languages');
        $this->frontend_lang = $params->get('site', 'en-GB');
    }
    
    function setLang($lang){
        $this->cur_lang = $lang;
    }
    
    function getLang(){
        return $this->cur_lang;
    }
    
    function getFrontLang(){
        return $this->frontend_lang;
    }
    
    function getCopyrightTexts(){
        return array('Copyright MAXXmarketing GmbH', 'JoomShopping Download & Support');
    }
	
	function parseConfigVars(){
		list($this->pdf_header_width, $this->pdf_header_height, $this->pdf_footer_width, $this->pdf_footer_height) = explode(":", $this->pdf_parameters);
		
		if (!$this->allow_reviews_prod){
			unset($this->sorting_products_field_select[5]);
			unset($this->sorting_products_name_select[5]);
			unset($this->sorting_products_field_s_select[5]);
			unset($this->sorting_products_name_s_select[5]);
		}

		if ($this->product_count_related_in_row<1) $this->product_count_related_in_row = 1;

		if ($this->user_as_catalog){
			$this->show_buy_in_category = 0;
		}
		if (!$this->stock){
			$this->hide_product_not_avaible_stock = 0;
			$this->hide_buy_not_avaible_stock = 0;
			$this->hide_text_product_not_available = 1;
			$this->product_list_show_qty_stock = 0;
			$this->product_show_qty_stock = 0;
		}

		if ($this->hide_product_not_avaible_stock || $this->hide_buy_not_avaible_stock){
			$this->controler_buy_qty = 1;
		}else{
			$this->controler_buy_qty = 0;
		}

		$this->display_price_front_current = $this->getDisplayPriceFront();// 0 - Brutto, 1 - Netto

		if ($this->template==""){
			$this->template = "default";
		}

		if ($this->show_product_code || $this->show_product_code_in_cart){
			$this->show_product_code_in_order = 1;
		}else{
			$this->show_product_code_in_order = 0;
		}

		if ($this->admin_show_vendors==0){
			$this->vendor_order_message_type = 0; //0 - none, 1 - mesage, 2 - order if not multivendor
			$this->admin_not_send_email_order_vendor_order = 0;
			$this->product_show_vendor = 0;
			$this->product_show_vendor_detail = 0;
		}
		$cpt = $this->getCopyrightTexts();
        $this->copyrightText = '<span id="mxcpr">'.$cpt[0].'<br><a rel="nofollow" target="_blank" href="https://www.joomshopping.com/">'.$cpt[1].'</a></span>';
        if ($this->image_resize_type==0){
			$this->image_cut = 1;
			$this->image_fill = 2;
		}elseif ($this->image_resize_type==1){
			$this->image_cut = 0;
			$this->image_fill = 2;
		}else{
			$this->image_cut = 0;
			$this->image_fill = 0;
		}
		if (!$this->tax){
			$this->show_tax_in_product = 0;
			$this->show_tax_product_in_cart = 0;
			$this->hide_tax = 1;
		}
		if (!$this->admin_show_delivery_time){
			$this->show_delivery_time = 0;
			$this->show_delivery_time_checkout = 0;
			$this->show_delivery_time_step5 = 0;
			$this->display_delivery_time_for_product_in_order_mail = 0;
			$this->show_delivery_date = 0;
		}
		if (!$this->admin_show_product_basic_price){
			$this->cart_basic_price_show = 0;
		}
		if (!$this->admin_show_weight){
			$this->product_show_weight = 0;
			$this->product_list_show_weight = 0;
		}
		$this->use_ssl = intval($this->use_ssl);
        if ($this->disable_admin['manufacturer_code']){
            $this->manufacturer_code_in_cart = 0;
            $this->manufacturer_code_in_product_list = 0;
            $this->manufacturer_code_in_product_detail = 0;
        }
		
		$this->generate_pdf = ($this->order_send_pdf_client || $this->order_send_pdf_admin);        
        $this->user_number_in_invoice = isset($this->user_number_in_invoice) ? $this->user_number_in_invoice : 0;
        $this->not_redirect_in_wishlist_after_buy = isset($this->not_redirect_in_wishlist_after_buy) ? $this->not_redirect_in_wishlist_after_buy : 0;
		$this->max_mark = (!isset($this->max_mark) || $this->max_mark<1) ? 10 : $this->max_mark;
		$this->rating_starparts = (!isset($this->rating_starparts) || $this->rating_starparts<1) ? 1 : $this->rating_starparts;
	}
	
	function getAdminContactEmails(){
		return explode(',', $this->contact_email);
	}

}