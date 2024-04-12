<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die();

class OrderTable extends ShopbaseTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_orders', 'order_id', $_db);
        \JPluginHelper::importPlugin('jshoppingcheckout');
        \JPluginHelper::importPlugin('jshoppingorder');
    }
    
    public function store($updateNulls = false){
        if (isset($this->prepareOrderPrint)){
            throw new Exception('Error JshopOrder::store()');
        }
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onBeforeStoreTableOrder', array(&$obj));
        return parent::store($updateNulls);
    }

    function getAllItems(){
        $db = \JFactory::getDBO();
        if (!isset($this->items)){
            $JshopConfig = \JSFactory::getConfig();
            $query = "SELECT OI.* FROM `#__jshopping_order_item` as OI WHERE OI.order_id=".(int)$this->order_id;
            $db->setQuery($query);
            $this->items = $db->loadObJectList();
            foreach($this->items as $k=>$v){
                $this->items[$k]->_qty_unit = '';
                $this->items[$k]->delivery_time = '';
            }
            if ($JshopConfig->display_delivery_time_for_product_in_order_mail){
                $deliverytimes = \JSFactory::getAllDeliveryTime();
                foreach($this->items as $k=>$v){
                    if (isset($deliverytimes[$v->delivery_times_id])) {
                        $this->items[$k]->delivery_time = $deliverytimes[$v->delivery_times_id];
                    }
                }
            } 
        }
    return $this->items;
    }
    
    function getWeightItems(){
        $items = $this->getAllItems();
        $weight = 0;
        foreach($items as $row){
            $weight += $row->product_quantity * $row->weight;
        }
        $dispatcher = \JFactory::getApplication();
        $obj = $this;
        $dispatcher->triggerEvent('onGetWeightOrderProducts', array(&$obj, &$weight));
    return $weight;
    }

    function getHistory() {
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT history.*, status.*, status.`".$lang->get('name')."` as status_name  FROM `#__jshopping_order_history` AS history
                  INNER JOIN `#__jshopping_order_status` AS status ON history.order_status_id = status.status_id
                  WHERE history.order_id = '" . $db->escape($this->order_id) . "'
                  ORDER BY history.status_date_added";
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    function getStatusTime(){
        $db = \JFactory::getDBO();
        $query = "SELECT max(status_date_added) FROM `#__jshopping_order_history` WHERE order_id = '".$db->escape($this->order_id)."'";
        $db->setQuery($query);
        $res = $db->loadResult();
    return strtotime($res);
    }

    function getStatus(){
		$status = \JSFactory::getTable('orderStatus');
        $status->load($this->order_status);        
        return $status->getName();
    }

    function copyDeliveryData(){
        $dispatcher = \JFactory::getApplication();
        $this->d_title = $this->title;
        $this->d_f_name = $this->f_name;
        $this->d_l_name = $this->l_name;
		$this->d_m_name = $this->m_name;
        $this->d_firma_name = $this->firma_name;
        $this->d_home = $this->home;
        $this->d_apartment = $this->apartment;
        $this->d_street = $this->street;
        $this->d_street_nr = $this->street_nr;
        $this->d_zip = $this->zip;
        $this->d_city = $this->city;
        $this->d_state = $this->state;
        $this->d_email = $this->email;
		$this->d_birthday = $this->birthday;
        $this->d_country = $this->country;
        $this->d_phone = $this->phone;
        $this->d_mobil_phone = $this->mobil_phone;
        $this->d_fax = $this->fax;
        $this->d_ext_field_1 = $this->ext_field_1;
        $this->d_ext_field_2 = $this->ext_field_2;
        $this->d_ext_field_3 = $this->ext_field_3;
        $obj = $this;
		$dispatcher->triggerEvent('onAfterCopyDeliveryData', array(&$obj));
    }

    function getOrdersForUser($id_user) {
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang(); 
        $query = "SELECT orders.*, order_status.`".$lang->get('name')."` as status_name, COUNT(order_item.order_id) AS count_products
                  FROM `#__jshopping_orders` AS orders
                  LEFT JOIN `#__jshopping_order_status` AS order_status ON orders.order_status = order_status.status_id
                  LEFT JOIN `#__jshopping_order_item` AS order_item ON order_item.order_id = orders.order_id
                  WHERE orders.user_id = '".$db->escape($id_user)."' and orders.order_created='1'
                  GROUP BY order_item.order_id 
                  ORDER BY orders.order_date DESC";
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    /**
    * Next order id    
    */
    function getLastOrderId() {
        $db = \JFactory::getDBO(); 
        $query = "SELECT MAX(orders.order_id) AS max_order_id FROM `#__jshopping_orders` AS orders";
        $db->setQuery($query);
        return $db->loadResult() + 1;
    }

    function formatOrderNumber($num){
		$JshopConfig = \JSFactory::getConfig();
        $number = \JSHelper::outputDigit($num, $JshopConfig->ordernumberlength);
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onAfterFormatOrderNumber', array(&$number, &$num));
        return $number;
    }

    /**
    * save name pdf from order
    */
    function insertPDF(){
        $db = \JFactory::getDBO();
        $query = "UPDATE `#__jshopping_orders` SET pdf_file = '".$db->escape($this->pdf_file)."' WHERE order_id='".$db->escape($this->order_id)."'";
        $db->setQuery($query);
        $db->execute();
    }
    
	function setInvoiceDate(){
        if (\JSHelper::datenull($this->invoice_date)){
            $db = \JFactory::getDBO();
            $this->invoice_date = \JSHelper::getJsDate();
            $query = "UPDATE `#__jshopping_orders` SET invoice_date='".$db->escape($this->invoice_date)."' WHERE order_id = '".$db->escape($this->order_id)."'";
            $db->setQuery($query);
            $db->execute();
        }
    }
	
	function getFilesStatDownloads($fileinfo = 0){
        if ($this->file_stat_downloads == "") return array();
        $rows = unserialize($this->file_stat_downloads);
        if ($fileinfo && count($rows)){
            $db = \JFactory::getDBO();
            $files_id = array_keys($rows);
            $query = "SELECT * FROM `#__jshopping_products_files` where id in (".implode(',',$files_id).")";
            $db->setQuery($query);
            $_list = $db->loadObJectList();
            $list = array();
            foreach($_list as $k=>$v){
                if (is_array($rows[$v->id])){
                    $v->count_download = $rows[$v->id]['download'];
                    $v->time = $rows[$v->id]['time'];
                }else{
                    $v->count_download = $rows[$v->id];
                }
                $list[$v->id] = $v;
            }
            return $list;
        }else{
            foreach($rows as $k=>$v){
                if (!is_array($v)){
                    $rows[$k] = array('download'=>$v, 'time'=>'');
                }
            }
            return $rows;
        }
    }
    
    function setFilesStatDownloads($array){
        $this->file_stat_downloads = serialize($array);
    }
    
    function getTaxExt(){
        if ($this->order_tax_ext == "") return array();
        return unserialize($this->order_tax_ext);
    }
    
    function setTaxExt($array){
        $this->order_tax_ext = serialize($array);
    }
    
    function setShippingTaxExt($array){
        $this->shipping_tax_ext = serialize($array);
    }
    
    function getShippingTaxExt(){
        if ($this->shipping_tax_ext == "") return array();
        return unserialize($this->shipping_tax_ext);
    }
    
    function setPackageTaxExt($array){
        $this->package_tax_ext = serialize($array);
    }
    
    function getPackageTaxExt(){
        if ($this->shipping_tax_ext == "") return array();
        return unserialize($this->package_tax_ext);
    }

    function setPaymentTaxExt($array){
        $this->payment_tax_ext = serialize($array);
    }
    
    function getPaymentTaxExt(){
        if ($this->payment_tax_ext == "") return array();
        return unserialize($this->payment_tax_ext);
    }
    
    function getPaymentParamsData(){
        if ($this->payment_params_data == "") return array();
        return unserialize($this->payment_params_data);
    }
    
    function setPaymentParamsData($array){
        $this->payment_params_data = serialize($array);
    }
    
    function getLang(){
        $lang = $this->lang;
        if ($lang=="") $lang = "en-GB";
        return $lang;
    }
	
	function getListFieldCopyUserToOrder(){
        $dispatcher = \JFactory::getApplication();        
        $list = array('user_id','f_name','l_name','m_name','firma_name','client_type','firma_code','tax_number','email','birthday','home','apartment','street','street_nr','zip','city','state','country','phone','mobil_phone','fax','title','ext_field_1','ext_field_2','ext_field_3','d_f_name','d_l_name','d_m_name','d_firma_name','d_email','d_birthday','d_home','d_apartment','d_street','d_street_nr','d_zip','d_city','d_state','d_country','d_phone','d_mobil_phone','d_title','d_fax','d_ext_field_1','d_ext_field_2','d_ext_field_3');
        $dispatcher->triggerEvent('onBeforeGetListFieldCopyUserToOrder', array(&$list));
    return $list;
    }
    
    function saveOrderItem($items) {
        $dispatcher = \JFactory::getApplication();
        
        foreach($items as $key=>$value){
            $order_item = \JSFactory::getTable('orderItem');
            $order_item->order_id = $this->order_id;
            $order_item->product_id = $value['product_id'];
            $order_item->category_id = $value['category_id'];
            $order_item->product_ean = $value['ean'];
            $order_item->manufacturer_code = $value['manufacturer_code'];
            $order_item->product_name = $value['product_name'];
            $order_item->product_quantity = $value['quantity'];
            $order_item->product_item_price = $value['price'];
            $order_item->product_tax = $value['tax'];
            $order_item->product_attributes = $attributes_value = '';
            $order_item->product_freeattributes = $free_attributes_value = '';
            $order_item->attributes = $value['attributes'];
            $order_item->files = $value['files'];
            $order_item->freeattributes = $value['freeattributes'];
            $order_item->weight = $value['weight'];
            $order_item->thumb_image = $value['thumb_image'];
            $order_item->delivery_times_id = $value['delivery_times_id'];
            $order_item->vendor_id = $value['vendor_id'];
            $order_item->manufacturer = $value['manufacturer'];
			$order_item->basicprice = isset($value['basicprice']) ? $value['basicprice'] : "";
            $order_item->basicpriceunit = isset($value['basicpriceunit']) ? $value['basicpriceunit'] : "";
            $order_item->params = isset($value['params']) ? $value['params'] : "";
            
            if (isset($value['attributes_value'])){
                foreach($value['attributes_value'] as $attr){
                    $attributes_value .= $attr->attr.': '.$attr->value."\n";
                }
            }
            $order_item->product_attributes = $attributes_value;
            
            if (isset($value['free_attributes_value'])){
                foreach ($value['free_attributes_value'] as $attr){
                    $free_attributes_value .= $attr->attr.': '.$attr->value."\n";
                }
            }
            $order_item->product_freeattributes = $free_attributes_value;
            
            if (isset($value['extra_fields'])){
                $order_item->extra_fields = '';
                foreach($value['extra_fields'] as $extra_field){
                    $order_item->extra_fields .= $extra_field['name'].': '.$extra_field['value']."\n";
                }
            }
            
            $dispatcher->triggerEvent('onBeforeSaveOrderItem', array(&$order_item, &$value) );
            
            $order_item->store();
        }
        return 1;
    }
    
    /**
    * get or return product in Stock
    * @param $change ("-" - get, "+" - return) 
    */
    function changeProductQTYinStock($change = "-"){
        $db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        
        $query = "SELECT OI.*, P.unlimited FROM `#__jshopping_order_item` as OI left Join `#__jshopping_products` as P on P.product_id=OI.product_id
                  WHERE order_id = '".$db->escape($this->order_id)."'";
        $db->setQuery($query);
        $items = $db->loadObJectList();
        $obj = $this;
		$dispatcher->triggerEvent('onBeforechangeProductQTYinStock', array(&$items, &$obj, &$change));

        foreach($items as $item){
            
            if ($item->unlimited) continue;
            
            if ($item->attributes!=""){
                $attributes = unserialize($item->attributes);
            }else{
                $attributes = array();
            }            
            if (!is_array($attributes)) $attributes = array();
            
            $allattribs = \JSFactory::getAllAttributes(1);
            $dependent_attr = array();
            foreach($attributes as $k=>$v){
                if ($allattribs[$k]->independent==0){
                    $dependent_attr[$k] = $v;
                }
            }
            
            if (count($dependent_attr)){
                $where="";
                foreach($dependent_attr as $k=>$v){
                    $where.=" and `attr_".(int)$k."`=".intval($v);
                }
                $query = "update `#__jshopping_products_attr` set `count`=`count`  ".$change." ".$item->product_quantity." where product_id='".intval($item->product_id)."' ".$where;
                $db->setQuery($query);
                $db->execute();
                
                $query="select sum(count) as qty from `#__jshopping_products_attr` where product_id='".intval($item->product_id)."' and `count`>0 ";
                $db->setQuery($query);
                $qty = $db->loadResult();
                
                $query = "UPDATE `#__jshopping_products` SET product_quantity = '".$qty."' WHERE product_id = '".intval($item->product_id)."'";
                $db->setQuery($query);
                $db->execute();
            }else{
                $query = "UPDATE `#__jshopping_products` SET product_quantity = product_quantity ".$change." ".$item->product_quantity." WHERE product_id = '".intval($item->product_id)."'";
                $db->setQuery($query);
                $db->execute();
            }
            $obj = $this;
            $dispatcher->triggerEvent('onAfterchangeProductQTYinStock', array(&$item, &$change, &$obj));
        }
        
        if ($change=='-'){
            $product_stock_removed = 1;
        }else{
            $product_stock_removed = 0;
        }
        $query = "update #__jshopping_orders set product_stock_removed=".$product_stock_removed." WHERE order_id = '".$db->escape($this->order_id)."'";
        $db->setQuery($query);
        $db->execute();
        $obj = $this;
		$dispatcher->triggerEvent('onAfterchangeProductQTYinStockPSR', array(&$items, &$obj, &$change, &$product_stock_removed));
    }
    
    /**    
    * get list vendors for order
    */
    function getVendors(){
        $db = \JFactory::getDBO();
        $query = "SELECT distinct V.* FROM `#__jshopping_order_item` as OI
                  left Join `#__jshopping_vendors` as V on V.id = OI.vendor_id
                  WHERE order_id = '".$db->escape($this->order_id)."'";
        $db->setQuery($query);
    return $db->loadObJectList();
    }
    
    function getVendorItems($vendor_id){
        $items = $this->getAllItems();
        foreach($items as $k=>$v){
            if ($v->vendor_id!=$vendor_id){
                unset($items[$k]);
            }
        }
    return $items;
    }
    
    function getVendorInfo(){
        $JshopConfig = \JSFactory::getConfig();
        $vendor_id = $this->vendor_id;
        if ($vendor_id==-1) $vendor_id = 0;
        if ($JshopConfig->vendor_order_message_type<2) $vendor_id = 0;
        $vendor = \JSFactory::getTable('vendor');
        $vendor->loadFull($vendor_id);
        $vendor->country_id = $vendor->country;
        $lang = \JSFactory::getLang($this->getLang());
        $country = \JSFactory::getTable('country');
        $country->load($vendor->country_id);
        $field_country_name = $lang->get("name");
        $vendor->country = $country->$field_country_name;
    return $vendor;
    }
    
    function getVendorIdForItems(){
        $items = $this->getAllItems();
		$vendors = array();
        foreach($items as $v){
            $vendors[] = $v->vendor_id;
        }
        $vendors = array_unique($vendors);
        if (count($vendors)==0){
            return 0;
        }elseif (count($vendors)>1){
            return -1;
        }else{
            return $vendors[0];
        }
    }
    
    function getReturnPolicy(){
        $items = $this->getAllItems();
        $products = array();
        foreach($items as $v){
            $products[] = $v->product_id;
        }
        $products = array_unique($products);
        $statictext = \JSFactory::getTable("statictext");
        $rows = $statictext->getReturnPolicyForProducts($products);
        $dispatcher = \JFactory::getApplication();
        $obj = $this;
        $dispatcher->triggerEvent('onAfterOrderGetReturnPolicy', array(&$obj, &$rows));
        return $rows;
    }
    
    function saveTransactionData($rescode, $status_id, $data){
        $row = \JSFactory::getTable("PaymentTrx");
        $row->order_id = $this->order_id;
        $row->rescode = $rescode;
        $row->status_id = $status_id;
        $row->transaction = $this->transaction;
        $row->date = \JSHelper::getJsDate();
        $row->store();
        if (is_array($data)){
            foreach($data as $k=>$v){
                $rowdata = \JSFactory::getTable("PaymentTrxData");
                $rowdata->id = 0;
                $rowdata->trx_id = $row->id;
                $rowdata->order_id = $this->order_id;
                $rowdata->key = $k;
                $rowdata->value = $v;
                $rowdata->store();
            }
        }
    }
    
    function getListTransactions(){
        $db = \JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_payment_trx` WHERE order_id = '".$db->escape($this->order_id)."' order by id desc";
        $db->setQuery($query);
        $rows = $db->loadObJectList();
        foreach($rows as $k=>$v){
            $rows[$k]->data = $this->getTransactionData($v->id);
        }
    return $rows;
    }
    
    function getTransactionData($trx_id){
        $db = \JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_payment_trx_data` WHERE trx_id = '".$db->escape($trx_id)."' order by id";
        $db->setQuery($query);        
    return $db->loadObJectList();
    }
    
    function setShippingParamsData($array){
        $this->shipping_params_data = serialize($array);
    }
    
    function getShippingParamsData(){
        if ($this->shipping_params_data == "") return array();
        return unserialize($this->shipping_params_data);
    }
    
    function prepareOrderPrint($page = '', $date_format = 0){
        $JshopConfig = \JSFactory::getConfig();
        $lang = \JSFactory::getLang();
        $JshopConfig->user_field_title[0] = '';
        $JshopConfig->user_field_client_type[0] = '';
        
        if ($page=='order_show'){
            $this->status_name = $this->getStatus();
        }else{
            $this->status = $this->getStatus();
        }
		
        if (!isset($this->order_date_print)){
			$this->order_date_print = \JSHelper::formatdate($this->order_date);
			$this->order_datetime_print = \JSHelper::formatdate($this->order_date, 1);
			if ($date_format){
				$this->order_date = $this->order_date_print;
			}
		}
		
        $this->products = $this->getAllItems();
        $this->weight = $this->getWeightItems();
		
        if ($JshopConfig->show_delivery_time_checkout){
            $deliverytimes = \JSFactory::getAllDeliveryTime();
            if (isset($deliverytimes[$this->delivery_times_id])){
                $this->order_delivery_time = $deliverytimes[$this->delivery_times_id];
            }else{
                $this->order_delivery_time = '';
            }
            if ($this->order_delivery_time==""){
                $this->order_delivery_time = $this->delivery_time;
            }
            if ($page=='order_show'){
                $this->delivery_time_name = $this->order_delivery_time;
            }
        }
        $this->order_tax_list = $this->getTaxExt();
        
        if (!isset($this->country_id)){
            $this->country_id = $this->country;
            $this->d_country_id = $this->d_country;
        }
        
        $country = \JSFactory::getTable('country');
        $country->load($this->country_id);
        $this->country = $country->getName();
        
        $d_country = \JSFactory::getTable('country');
        $d_country->load($this->d_country_id);
        $this->d_country = $d_country->getName();
		
        if ($JshopConfig->show_delivery_date && !\JSHelper::datenull($this->delivery_date)){
            $this->delivery_date_f = \JSHelper::formatdate($this->delivery_date);
        }else{
            $this->delivery_date_f = '';
        }
        
        if (!isset($this->title_id)){
            $this->title_id = $this->title;
            $this->d_title_id = $this->d_title;
        }
        if (!isset($this->birthday_date)){
            $this->birthday_date = $this->birthday;
            $this->d_birthday_date = $this->d_birthday;
        }
        
        $this->title = \JText::_($JshopConfig->user_field_title[$this->title_id]);
        $this->d_title = \JText::_($JshopConfig->user_field_title[$this->d_title_id]);
        $this->birthday = \JSHelper::getDisplayDate($this->birthday_date, $JshopConfig->field_birthday_format);
        $this->d_birthday = \JSHelper::getDisplayDate($this->d_birthday_date, $JshopConfig->field_birthday_format);
        $this->client_type_name = $this->getClientTypeName();
        
        $shippingMethod = $this->getShipping();
        
        $pm_method = $this->getPayment();
        $paymentsysdata = $pm_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;

        if ($page=='order_show'){
            $this->shipping_info = $shippingMethod->getName();
        }else{
            $this->shipping_information = $shippingMethod->getName();
        }
        $shippingForm = $shippingMethod->getShippingForm();
        if ($shippingForm){
            $shippingForm->prepareParamsDispayMail($this, $shippingMethod);
        }
        $this->payment_name = $pm_method->getName();
        $this->payment_information = $this->payment_params;
        if ($payment_system){
            $payment_system->prepareParamsDispayMail($this, $pm_method);
        }
        if ($pm_method->show_descr_in_email)
            $this->payment_description = $pm_method->getDescription();
        else 
            $this->payment_description = "";
        
        if ($this->coupon_id){
            $this->coupon_code = $this->getCouponCode();
        }
        if ($page=='order_show'){
            $this->history = $this->getHistory();
        }

        foreach($this->items as $k => $v) {
			$this->items[$k]->_tmpl_html_order_items_end = "";
			$this->items[$k]->_ext_attribute_html = "";
			$this->items[$k]->_ext_file_html = "";
			$this->items[$k]->_ext_price_html = "";
			$this->items[$k]->_ext_price_total_html = "";
		}
		
		$this->_tmp_ext_order_number = "";
        $this->_tmp_ext_status_name = "";

        $this->prepareOrderPrint = 1;
    }

    function getCouponCode(){
        $coupon = \JSFactory::getTable('coupon');
        $coupon->load($this->coupon_id);
        return $coupon->coupon_code;
    }
    
    function generatePdf($file_generete_pdf_order){
        $this->setInvoiceDate();        
        $this->pdf_file = $file_generete_pdf_order::generatePdf($this);
        $this->insertPDF();
        $obj = $this;
		\JFactory::getApplication()->triggerEvent('onAfterGeneratePdfOrder', array(&$obj));
		return $this->pdf_file;
    }
    
    function prepareBirthdayFormat(){
        $JshopConfig = \JSFactory::getConfig();
        if (!isset($this->birthday_date)){
            $this->birthday_date = $this->birthday;
            $this->d_birthday_date = $this->d_birthday;
        }
        $this->birthday = \JSHelper::getDisplayDate($this->birthday_date, $JshopConfig->field_birthday_format);
        $this->d_birthday = \JSHelper::getDisplayDate($this->d_birthday_date, $JshopConfig->field_birthday_format);
    }
    
    function delete($id = null){
        $k = $this->_tbl_key;
        $id = (is_null($id)) ? $this->$k : $id;
        if ($id === null){
            throw new Exception('Null primary key not allowed.');
        }
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_orders` WHERE `order_id` = '".$db->escape($id)."'";
        $db->setQuery($query);
        $db->execute();
        $query = "DELETE FROM `#__jshopping_order_item` WHERE `order_id` = '".$db->escape($id)."'";
        $db->setQuery($query);
        $db->execute();
        $query = "DELETE FROM `#__jshopping_order_history` WHERE `order_id` = '".$db->escape($id)."'";
        $db->setQuery($query);
        $db->execute();
    }
	
	function getPayment(){
		$pm_method = \JSFactory::getTable('paymentMethod');
        $pm_method->load($this->payment_method_id);
        return $pm_method;
	}
	
	function getShipping(){
		$sh = \JSFactory::getTable('shippingMethod');
        $sh->load($this->shipping_method_id);
		return $sh;
	}
    
    function getPaymentName(){
        return $this->getPayment()->getName();
    }
    
    function getShippingName(){
        return $this->getShipping()->getName();
    }
    
    function getClientTypeName(){
        if ($this->client_type){
            return \JText::_(\JSFactory::getConfig()->user_field_client_type[$this->client_type]);
        }else{
            return '';
        }
    }
    
    function getProductStockRemoved($status, $order_create = 0){
        $JshopConfig = \JSFactory::getConfig();
        if ($JshopConfig->order_stock_removed_only_paid_status){
            $product_stock_removed = (in_array($status, $JshopConfig->payment_status_enable_download_sale_file));
        }else{
			if ($order_create==1){
				$product_stock_removed = 1;
			}else{
				$product_stock_removed = (!in_array($status, $JshopConfig->payment_status_return_product_in_stock));
			}
        }
    return $product_stock_removed;
    }
	
	function updateProductsInStock($order_create = 0){
		$product_stock_removed = $this->getProductStockRemoved($this->order_status, $order_create);
        
        if ($this->order_created && !$product_stock_removed && $this->product_stock_removed==1){
            $this->changeProductQTYinStock("+");            
        }
        
        if ($this->order_created && $product_stock_removed && $this->product_stock_removed==0){
            $this->changeProductQTYinStock("-");            
        }
	}
    
    function saveOrderHistory($notify, $comments){
        $history = \JSFactory::getTable('orderHistory');
        $history->order_id = $this->order_id;
        $history->order_status_id = $this->order_status;
        $history->status_date_added = \JSHelper::getJsDate();
        $history->customer_notify = $notify;
        $history->comments = $comments;	
        $obj = $this;	
        \JFactory::getApplication()->triggerEvent('onBeforeJshopOrderSaveOrderHistory', array(&$history, &$notify, &$comments, &$obj));
        return $history->store();
    }
    
    function getClientAllowCancel(){
        $JshopConfig = \JSFactory::getConfig();
        if ($JshopConfig->client_allow_cancel_order && 
            $this->order_status!=$JshopConfig->payment_status_for_cancel_client && 
            !in_array($this->order_status, $JshopConfig->payment_status_disable_cancel_client) ){
            $allow_cancel = 1;
        }else{
            $allow_cancel = 0;
        }
        return $allow_cancel;
    }
    
    function getShowPercentTax(){
        $JshopConfig = \JSFactory::getConfig();
        if (!$this->prepareOrderPrint){
            throw new Exception('Error JshopOrder::getShowPercentTax()');
        }
        $show = 0;
        if (count($this->order_tax_list)>1 || $JshopConfig->show_tax_in_product) $show = 1;
        if ($JshopConfig->hide_tax) $show = 0;
    return $show;
    }
    
    function getHideSubtotal(){
        $JshopConfig = \JSFactory::getConfig();
        if (!$this->prepareOrderPrint){
            throw new Exception('Error JshopOrder::getHideSubtotal()');
        }
        $hide_subtotal = 0;
        if (
            ($JshopConfig->hide_tax || count($this->order_tax_list)==0) && 
            $this->order_discount==0 && 
            $this->order_payment==0 && 
            $JshopConfig->without_shipping){
            $hide_subtotal = 1;
        }
        return $hide_subtotal;
    }
    
    function getTextTotal(){
        $JshopConfig = \JSFactory::getConfig();
        if (!$this->prepareOrderPrint){
            throw new Exception('Error JshopOrder::getTextTotal()');
        }
        $text_total = \JText::_('JSHOP_ENDTOTAL');
        if (($JshopConfig->show_tax_in_product || $JshopConfig->show_tax_product_in_cart) && (count($this->order_tax_list)>0)){
            $text_total = \JText::_('JSHOP_ENDTOTAL_INKL_TAX');
        }
        return $text_total;
    }
    
    function fixConfigShowWeightOrder(){
        $JshopConfig = \JSFactory::getConfig();
        if ($this->weight==0 && $JshopConfig->hide_weight_in_cart_weight0){
            $JshopConfig->show_weight_order = 0;
        }
    }
    
    function loadItemsNewDigitalProducts(){
        $JshopConfig = \JSFactory::getConfig();
        if (isset($this->items) && $JshopConfig->order_display_new_digital_products){
            $product = \JSFactory::getTable('product');
            foreach($this->items as $k=>$v){
                $product->product_id = $v->product_id;
                $product->setAttributeActive(unserialize($v->attributes));
                $files = $product->getSaleFiles();
                $this->items[$k]->files = serialize($files);
            }
        }
    }
    
    function getStaticText($alias){
        $JshopConfig = \JSFactory::getConfig();
        $statictext = \JSFactory::getTable("statictext");
        $row = $statictext->loadData($alias);
        $text = $row->text;
        $text = str_replace("{name}", $this->f_name, $text);
        $text = str_replace("{family}", $this->l_name, $text);
        $text = str_replace("{email}", $this->email, $text);
        $text = str_replace("{title}", $this->title, $text);
        
        if ($alias == 'order_email_descr_end' && $JshopConfig->show_return_policy_text_in_email_order){
            $list = $this->getReturnPolicy();
            $listtext = array();
            foreach($list as $v){
                $listtext[] = $v->text;
            }
            $rptext = implode('<div class="return_policy_space"></div>', $listtext);
            $text = $rptext.$text;
        }
        
    return $text;
    }

    function applyCoupon($code){
        if ($code == ''){
            $this->coupon_id = 0;
            return 0;
        }else{
            $coupon = \JSFactory::getTable('coupon');
            $coupon_id = $coupon->getIdFromCode($code);
            if ($coupon_id){
                $this->coupon_id = $coupon_id;
                $coupon->load($coupon_id);
                $coupon->finish(1, $this->user_id);
                return 1;
            }else{
                $this->coupon_id = 0;
                return 0;
            }
        }
    }

}