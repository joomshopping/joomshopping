<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class ProductShopModel extends BaseModel{
    
	public $product = null;
	public $attributes;
	public $all_attr_values;
	public $allow_review;
	public $text_review;
	public $select_review;
	
    public function __construct(){
        \JPluginHelper::importPlugin('jshoppingcheckout');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshopProductShop', array(&$obj));
    }
	
	public function setProduct($product){
		$this->product = $product;
        $obj = $this;
		\JFactory::getApplication()->triggerEvent('onAfterJshopProductShopSetProduct', array(&$obj));
	}
	
	public function getProduct(){
		return $this->product;
	}
	
	public function storeEndPageBuy(){	
		$session = \JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
	}
	
	public function storeEndPageList(){
		$session = \JFactory::getSession();
		$session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);
	}
	
	public function storeEndPages(){
		$this->storeEndPageBuy();
		$this->storeEndPageList();
	}
	
	public function getEndPageBuy(){
		$session = \JFactory::getSession();
        return $session->get("jshop_end_page_buy_product");
	}
	
	public function getEndPageList(){
		$session = \JFactory::getSession();
        return $session->get("jshop_end_page_list_product");
	}
	
	public function setBackValue(array $back_value){
		$session = \JFactory::getSession();
        $session->set('product_back_value', $back_value);
	}
	
	public function getBackValue($product_id, $attr = null){
		$session = \JFactory::getSession();
		$back_value = $session->get('product_back_value');        
        if (!isset($back_value['pid'])) $back_value = array('pid'=>null, 'attr'=>null, 'qty'=>null);
        if ($back_value['pid']!=$product_id) $back_value = array('pid'=>null, 'attr'=>null, 'qty'=>null);
        if (!is_array($back_value['attr'])) $back_value['attr'] = array();
        if (count($back_value['attr'])==0 && is_array($attr)) $back_value['attr'] = $attr;		
        return $back_value;
	}
	
	public function clearBackValue(){
		$this->setBackValue(array());
	}
	
	public function getCategories(){
		return $this->product->getCategories(1);
	}
	
	public function prepareView($back_value = array()){
		$jshopConfig = \JSFactory::getConfig();
		$product = $this->product;
        $product->product_price_default = 0;
		
		if (!\JSHelper::getDisplayPriceForProduct($product->product_price)){
            $jshopConfig->attr_display_addprice = 0;
        }

		$back_value_attr = (array)$back_value['attr'];
		
		$this->attributes = $product->getInitLoadAttribute($back_value_attr);
		
		if (count($this->attributes)){
            $_attributevalue = \JSFactory::getTable('AttributValue');
            $this->all_attr_values = $_attributevalue->getAllAttributeValues();
        }else{
            $this->all_attr_values = array();
        }
		
		$product->getExtendsData();
		
		$product->product_basic_price_unit_qty = 1;
        if ($jshopConfig->admin_show_product_basic_price){
            $product->getBasicPriceInfo();
        }else{
            $product->product_basic_price_show = 0;
        }
		
		if ($product->product_template==""){
			$product->product_template = "default";
		}
		
		$_review = \JSFactory::getTable('review');
		$this->allow_review = $_review->getAllowReview();
        if ($this->allow_review > 0){            
            $this->text_review = '';            
        } else {            
            $this->text_review = $_review->getText();
        }
		
		if ($jshopConfig->product_show_manufacturer_logo || $jshopConfig->product_show_manufacturer){
            $product->manufacturer_info = $product->getManufacturerInfo();
            if (!isset($product->manufacturer_info)){
                $product->manufacturer_info = new \stdClass();
                $product->manufacturer_info->manufacturer_logo = '';
                $product->manufacturer_info->name = '';
            }
        }else{
            $product->manufacturer_info = new \stdClass();
            $product->manufacturer_info->manufacturer_logo = '';
            $product->manufacturer_info->name = '';
        }
        
        if ($jshopConfig->product_show_vendor){
            $vendorinfo = $product->getVendorInfo();
            $vendorinfo->urllistproducts = \JSHelper::SEFLink("index.php?option=com_jshopping&controller=vendor&task=products&vendor_id=".$vendorinfo->id,1);
            $vendorinfo->urlinfo = \JSHelper::SEFLink("index.php?option=com_jshopping&controller=vendor&task=info&vendor_id=".$vendorinfo->id,1);
            $product->vendor_info = $vendorinfo;
        }else{
            $product->vendor_info = null;
        }
		
		if ($jshopConfig->admin_show_product_extra_field){
            $product->extra_field = $product->getExtraFields();
        }else{
            $product->extra_field = null;
        }
		
		if ($jshopConfig->admin_show_freeattributes){
            $product->getListFreeAttributes();
            foreach($product->freeattributes as $k=>$v){
                if (!isset($back_value['freeattr'][$v->id])) $back_value['freeattr'][$v->id] = '';
                $product->freeattributes[$k]->input_field = $this->getInputFreeAttribute($v->id, $back_value['freeattr'][$v->id]);
            }
            $attrrequire = $product->getRequireFreeAttribute();
            $product->freeattribrequire = count($attrrequire);
        }else{
            $product->freeattributes = null;
            $product->freeattribrequire = 0;
        }
        if ($jshopConfig->product_show_qty_stock){
            $product->qty_in_stock = \JSHelper::getDataProductQtyInStock($product);
        }
		
		if (!$jshopConfig->admin_show_product_labels){
			$product->label_id = null;
		}
        if ($product->label_id){
            $image = \JSHelper::getNameImageLabel($product->label_id);
            if ($image){
                $product->_label_image = $jshopConfig->image_labels_live_path."/".$image;
            }
            $product->_label_name = \JSHelper::getNameImageLabel($product->label_id, 2);
        }
		
		$product->_display_price = \JSHelper::getDisplayPriceForProduct($product->getPriceCalculate());
        if (!$product->_display_price){
            $product->product_old_price = 0;
            $product->product_price_default = 0;
            $product->product_basic_price_show = 0;
            $product->product_is_add_price = 0;
            $product->product_tax = 0;
            $jshopConfig->show_plus_shipping_in_product = 0;
        }

        if (trim($product->description) == "" && $jshopConfig->show_short_descr_insted_of == 1) {
			$product->description = $product->short_description;
		}
		
		if ($jshopConfig->use_plugin_content){
            \JSHelper::changeDataUsePluginContent($product, "product");
        }
		
		$product->hide_delivery_time = 0;
        if (!$product->getDeliveryTimeId()){
            $product->hide_delivery_time = 1;
        }
		
		$product->button_back_js_click = "history.go(-1);";
		$end_page_list = $this->getEndPageList();
        if ($end_page_list && $jshopConfig->product_button_back_use_end_list){
            $product->button_back_js_click = "location.href='".\JSHelper::jsFilterUrl($end_page_list)."';";
        }
		
	}
	
	public function getHideBuy(){
		$jshopConfig = \JSFactory::getConfig();
		$hide_buy = 0;
        if ($jshopConfig->user_as_catalog) $hide_buy = 1;
        if ($jshopConfig->hide_buy_not_avaible_stock && $this->product->product_quantity <= 0) $hide_buy = 1;
		if (!$this->product->_display_price) $hide_buy = 1;
		return $hide_buy;
	}
	
	public function getTextAvailable(){
		$product = $this->product;
		$available = "";
        if ( ($product->getQty() <= 0) && $product->product_quantity >0 ){
            $available = \JText::_('JSHOP_PRODUCT_NOT_AVAILABLE_THIS_OPTION');
        }elseif ($product->product_quantity <= 0){
            $available = \JText::_('JSHOP_PRODUCT_NOT_AVAILABLE');
        }
		return $available;
	}
	
	public function getDefaultCountProduct($back_value = array()){
		$jshopConfig = \JSFactory::getConfig();
		$default_count_product = 1;
        if ($jshopConfig->min_count_order_one_product>1){
            $default_count_product = $jshopConfig->min_count_order_one_product;
        }
        if ($back_value['qty']){
            $default_count_product = $back_value['qty'];
        }
		return $default_count_product;
	}
	
	public function getDisplayButtonsStyle(){
		$style = '';
        if (\JSFactory::getConfig()->hide_buy_not_avaible_stock && $this->product->getQty() <= 0){
			$style = 'display:none;';
		}
		return $style;
	}
	
	public function getInputFreeAttribute($id, $value){
        $view = $this->getView("product");
        $view->setLayout('freeattribute_input');
        $view->set('id', $id);
        $view->set('value', $value);
        return $view->loadTemplate();		
	}
	
	public function getAttributes(){
		return $this->attributes;
	}
	
	public function getAllAttrValues(){
		return $this->all_attr_values;
	}
	
	public function getAllowReview(){
		return $this->allow_review;
	}
	
	public function getTextReview(){
		return $this->text_review;
	}
    
}