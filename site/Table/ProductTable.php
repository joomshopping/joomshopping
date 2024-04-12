<?php
/**
* @version      5.1.3 14.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die();

class ProductTable extends MultilangTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_products', 'product_id', $_db);
        \JPluginHelper::importPlugin('jshoppingproducts');
    }

    function setAttributeActive($attribs){
		$db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        $JshopConfig = \JSFactory::getConfig();
		$this->setAttributeSubmitted($attribs, 1);
        $this->attribute_active = $attribs;
        if (is_array($this->attribute_active) && count($this->attribute_active)){
            $this->attribute_active_data = new \stdClass();
            $allattribs = \JSFactory::getAllAttributes(1);
            $dependent_attr = array();
            $independent_attr = array();
            foreach($attribs as $k=>$v){
                if ($allattribs[$k]->independent==0){
                    $dependent_attr[$k] = $v;
                }else{
                    $independent_attr[$k] = $v;
                }
            }

            if (count($dependent_attr)){
                $where = "";
                foreach($dependent_attr as $k=>$v){
                    $where.=" and PA.attr_".(int)$k." = ".(int)$v;
                }
                $query = "select PA.* from `#__jshopping_products_attr` as PA where PA.product_id=".(int)$this->product_id." ".$where;
                $db->setQuery($query);
                $this->attribute_active_data = $db->loadObject();
				if (!isset($this->attribute_active_data)) {
                    $this->attribute_active_data = new \stdClass();
                }
                if ($JshopConfig->use_extend_attribute_data && isset($this->attribute_active_data->ext_attribute_product_id) && $this->attribute_active_data->ext_attribute_product_id){
                    $this->attribute_active_data->ext_data = $this->getExtAttributeData($this->attribute_active_data->ext_attribute_product_id);
                }
            }

            if (count($independent_attr)){
				if (!isset($this->attribute_active_data)) $this->attribute_active_data = new \stdClass();
                if (!isset($this->attribute_active_data->price)) $this->attribute_active_data->price = $this->product_price;
                if (!isset($this->attribute_active_data->old_price)) $this->attribute_active_data->old_price = $this->product_old_price;
                foreach($independent_attr as $k=>$v){
                    $query = "select addprice, price_mod from #__jshopping_products_attr2 where product_id=".(int)$this->product_id." and attr_id=".(int)$k." and attr_value_id=".(int)$v;
                    $db->setQuery($query);
                    $attr_data2 = $db->loadObJect();
                    $obj = $this;
                    $dispatcher->triggerEvent('onSetAddPriceIndependentAttr', array(&$independent_attr, &$k, &$v, &$attr_data2, &$obj));
                    if ($attr_data2) {
                        if ($attr_data2->price_mod=="+"){
                            $this->attribute_active_data->price += $attr_data2->addprice;
                            if ($this->attribute_active_data->old_price > 0){
                                $this->attribute_active_data->old_price += $attr_data2->addprice;
                            }
                        }elseif ($attr_data2->price_mod=="-"){
                            $this->attribute_active_data->price -= $attr_data2->addprice;
                            if ($this->attribute_active_data->old_price > 0){
                                $this->attribute_active_data->old_price -= $attr_data2->addprice;
                            }
                        }elseif ($attr_data2->price_mod=="*"){
                            $this->attribute_active_data->price *= $attr_data2->addprice;
                            if ($this->attribute_active_data->old_price > 0){
                                $this->attribute_active_data->old_price *= $attr_data2->addprice;
                            }
                        }elseif ($attr_data2->price_mod=="/"){
                            $this->attribute_active_data->price /= $attr_data2->addprice;
                            if ($this->attribute_active_data->old_price > 0){
                                $this->attribute_active_data->old_price /= $attr_data2->addprice;
                            }
                        }elseif ($attr_data2->price_mod=="%"){
                            $this->attribute_active_data->price *= $attr_data2->addprice/100;
                            if ($this->attribute_active_data->old_price > 0){
                                $this->attribute_active_data->old_price *= $attr_data2->addprice/100;
                            }
                        }elseif ($attr_data2->price_mod=="="){
                            $this->attribute_active_data->price =  $attr_data2->addprice;
                            if ($this->attribute_active_data->old_price > 0){
                                $this->attribute_active_data->old_price =  $attr_data2->addprice;
                            }
                        }
                    }
                }
            }
        }else{
            $this->attribute_active_data = NULL;
        }
        $obj = $this;
		$dispatcher->triggerEvent('onAfterSetAttributeActive', array(&$attribs, &$obj));
    }

	function setAttributeSubmitted($attribs, $only_new = 0){
		if ($only_new==0 || !isset($this->attribute_submited)){
			$this->attribute_submited = $attribs;
		}
	}

    function setFreeAttributeActive($freattribs){
        $this->free_attribute_active = $freattribs;
    }

    function getData($field){
        if (isset($this->attribute_active_data->ext_data) && isset($this->attribute_active_data->ext_data->$field) && $this->attribute_active_data->ext_data->$field!=''){
            return $this->attribute_active_data->ext_data->$field;
        }else{
            return $this->$field;
        }
    }

    //get require attribute
    function getRequireAttribute(){
        $require = array();
        if (!\JSFactory::getConfig()->admin_show_attributes){
			return $require;
		}

        $allattribs = \JSFactory::getAllAttributes(2);
        $dependent_attr = $allattribs['dependent'];
        $independent_attr = $allattribs['independent'];

        if (count($dependent_attr)){
            $prodAttribVal = $this->getAttributes();
            if (count($prodAttribVal)){
                $prodAtrtib = $prodAttribVal[0];
                foreach($dependent_attr as $attrib){
                    $field = "attr_".(int)$attrib->attr_id;
                    if ($prodAtrtib->$field) $require[] = $attrib->attr_id;
                }
            }
        }

        if (count($independent_attr)){
            $prodAttribVal2 = $this->getAttributes2();
            foreach($prodAttribVal2 as $attrib){
                if (!in_array($attrib->attr_id, $require) && isset($independent_attr[$attrib->attr_id])){
                    $require[] = $attrib->attr_id;
                }
            }
        }

        return $require;
    }

    //get dependent attributs
    function getAttributes(){
        $db = \JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_products_attr` WHERE product_id=".(int)$this->product_id." ORDER BY product_attr_id";
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    //get independent attributs
    function getAttributes2(){
        $db = \JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_products_attr2` WHERE product_id=".(int)$this->product_id." ORDER BY id";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onAfterQueryGetAttributes2', array(&$query));
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    //get attrib values
	function getAttribValue($attr_id, $other_attr = array(), $onlyExistProduct = 0){
        $db = \JFactory::getDBO();
        $JshopConfig = \JSFactory::getConfig();
        $allattribs = \JSFactory::getAllAttributes(1);
        $lang = \JSFactory::getLang();
        if ($allattribs[$attr_id]->independent==0){
            $where = "";
            foreach($other_attr as $k=>$v){
                $where.=" and PA.attr_".(int)$k."=".(int)$v;
            }
            if ($onlyExistProduct) $where.=" and PA.count>0 ";
            $sorting = $JshopConfig->attribut_dep_sorting_in_product;
            if ($sorting=="") $sorting = "V.value_ordering";
			if ($sorting=="PA.product_attr_id") $sorting = "min(PA.product_attr_id)";
            $field = "attr_".(int)$attr_id;
            $query = "SELECT PA.$field as val_id, V.`".$lang->get("name")."` as value_name, V.image
                      FROM `#__jshopping_products_attr` as PA
					  INNER JOIN #__jshopping_attr_values as V ON PA.$field=V.value_id
                      WHERE PA.product_id=".(int)$this->product_id." ".$where."
					  GROUP BY PA.$field
                      ORDER BY ".$sorting;
        }else{
            $sorting = $JshopConfig->attribut_nodep_sorting_in_product;
            if ($sorting=="") $sorting = "V.value_ordering";
            $query = "SELECT PA.attr_value_id as val_id, V.`".$lang->get("name")."` as value_name, V.image, price_mod, addprice
                      FROM `#__jshopping_products_attr2` as PA
					  INNER JOIN #__jshopping_attr_values as V ON PA.attr_value_id=V.value_id
                      WHERE PA.product_id=".(int)$this->product_id." and PA.attr_id=".(int)$attr_id."
                      ORDER BY ".$sorting;
        }
        extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    function getAttributesDatas($selected = array()){
        $JshopConfig = \JSFactory::getConfig();
        $data = array('attributeValues'=>array());
        $requireAttribute = $this->getRequireAttribute();
        $actived = array();
        foreach($requireAttribute as $attr_id){
            $options = $this->getAttribValue($attr_id, $actived, $JshopConfig->hide_product_not_avaible_stock);
            $data['attributeValues'][$attr_id] = $options;
            if (!$JshopConfig->product_attribut_first_value_empty){
                $actived[$attr_id] = $options[0]->val_id ?? 0;
            }
            if (isset($selected[$attr_id])){
                $testActived = 0;
                foreach($options as $tmp) if ($tmp->val_id==$selected[$attr_id]) $testActived = 1;
                if ($testActived){
                    $actived[$attr_id] = $selected[$attr_id];
                }
            }
        }
        if (count($requireAttribute) == count($actived)){
            $data['attributeActive'] = $actived;
        }else{
            $data['attributeActive'] = array();
        }
        $data['attributeSelected'] = $actived;
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
    return $data;
    }

	function getInitLoadAttribute($selected = array()){
		$this->setAttributeSubmitted($selected);
		$attributesDatas = $this->getAttributesDatas($selected);
		$this->product_attribute_datas = $attributesDatas;
        $this->setAttributeActive($attributesDatas['attributeActive']);
        $attributeValues = $attributesDatas['attributeValues'];
        return $this->getBuildSelectAttributes($attributeValues, $attributesDatas['attributeSelected']);
	}

    function getPIDCheckQtyValue(){
        if (isset($this->attribute_active_data->product_attr_id)){
            return "A:".$this->attribute_active_data->product_attr_id;
        }else{
            return "P:".$this->product_id;
        }
    }

    function getListFreeAttributes(){
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        $query = "SELECT FA.id, FA.required, FA.`".$lang->get("name")."` as name, FA.`".$lang->get("description")."` as description, FA.type
				  FROM `#__jshopping_products_free_attr` as PFA
				  left Join `#__jshopping_free_attr` as FA on FA.id=PFA.attr_id
                  WHERE PFA.product_id=".(int)$this->product_id." order by FA.ordering";
        $db->setQuery($query);
        $this->freeattributes = $db->loadObJectList();
        return $this->freeattributes;
    }

    /**
    * use after getListFreeAttributes()
    */
    function getRequireFreeAttribute(){
        $rows = array();
        if ($this->freeattributes){
            foreach($this->freeattributes as $k=>$v){
                if ($v->required){
                    $rows[] = $v->id;
                }
            }
        }
    return $rows;
    }

    function getCategories($type_result = 0){
        if (!isset($this->product_categories)){
            $db = \JFactory::getDBO();
            $query = "SELECT * FROM `#__jshopping_products_to_categories` WHERE product_id=".(int)$this->product_id;
            $db->setQuery($query);
            $this->product_categories = $db->loadObJectList();
        }
        if ($type_result==1){
            $cats = array();
            foreach($this->product_categories as $v){
                $cats[] = $v->category_id;
            }
            return $cats;
        }else{
            return $this->product_categories;
        }
    }

    function getPriceWithParams(){
        if (isset($this->attribute_active_data->price)){
            return $this->attribute_active_data->price;
        }else{
            return $this->product_price;
        }
    }

    function getEan(){
        if (isset($this->attribute_active_data->ean)){
            return $this->attribute_active_data->ean;
        }else{
            return $this->product_ean;
        }
    }

    function getManufacturerCode(){
        if (isset($this->attribute_active_data->ean)){
            return $this->attribute_active_data->manufacturer_code;
        }else{
            return $this->manufacturer_code;
        }
    }

    function getQty(){
        if ($this->unlimited) return 1;
        if (isset($this->attribute_active_data->count)){
            return $this->attribute_active_data->count;
        }else{
            return $this->product_quantity;
        }
    }

    function getWeight(){
        if (isset($this->attribute_active_data) && isset($this->attribute_active_data->weight)){
            return $this->attribute_active_data->weight;
        }else{
            return $this->product_weight;
        }
    }

    function getWeight_volume_units(){
        if (isset($this->attribute_active_data->weight_volume_units) && $this->attribute_active_data->weight_volume_units > 0){
            return $this->attribute_active_data->weight_volume_units;
        }else{
            return $this->weight_volume_units;
        }
    }

    function getQtyInStock(){
        if ($this->unlimited) return 1;
        $qtyInStock = floatval($this->getQty());
        if ($qtyInStock < 0) $qtyInStock = 0;
    return $qtyInStock;
    }

    function getOldPrice(){
        if (isset($this->attribute_active_data->old_price)){
            return $this->attribute_active_data->old_price;
        }else{
            return $this->product_old_price;
        }
    }

    function getImages(){
        $db = \JFactory::getDBO();
        $jshopConfig = \JSFactory::getConfig();
        if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
            $list = $this->attribute_active_data->ext_data->getImages();
            if (count($list)){
                return $list;
            }
        }

        $query = "SELECT I.*, IF(P.image=I.image_name,0,1) as sort FROM `#__jshopping_products_images` as I
				 left Join `#__jshopping_products` as P on P.product_id=I.product_id
                 WHERE I.product_id=".(int)$this->product_id." ORDER BY sort, I.ordering";
        $db->setQuery($query);
        $list = $db->loadObJectList();
        foreach($list as $k=>$v){
            $list[$k]->img_alt = $v->name;
            $list[$k]->img_title = $v->title;
            if ($v->name) {
                $list[$k]->_title = $v->name;
            } else {
                $list[$k]->_title = $this->getName();
            }
            if (!$jshopConfig->product_img_seo) {
                if (!$list[$k]->img_alt) {
                    $list[$k]->img_alt = $this->getName();
                }
                $list[$k]->img_title = $list[$k]->img_alt;
            }
            $list[$k]->image_thumb = \JSHelper::getPatchProductImage($v->image_name, 'thumb');
            $list[$k]->image_full = \JSHelper::getPatchProductImage($v->image_name, 'full');
        }
    return $list;
    }

    function getVideos(){
        $db = \JFactory::getDBO();
        $JshopConfig = \JSFactory::getConfig();
        if (!$JshopConfig->admin_show_product_video){
			return array();
		}
        $query = "SELECT  video_name, video_id, video_preview, video_code FROM `#__jshopping_products_videos` WHERE product_id=".(int)$this->product_id;
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    function getFiles(){
        $db = \JFactory::getDBO();
        $JshopConfig = \JSFactory::getConfig();
        if (!$JshopConfig->admin_show_product_files) return array();
		if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
			$list = $this->attribute_active_data->ext_data->getFiles();
			if (count($list)){
                return $list;
            }
		}
		$query = "SELECT * FROM `#__jshopping_products_files` WHERE product_id=".(int)$this->product_id." order by `ordering`";
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "beforeQuery"));
		$db->setQuery($query);
		return $db->loadObJectList();
    }

    function getDemoFiles(){
        $db = \JFactory::getDBO();
        $JshopConfig = \JSFactory::getConfig();
        if (!$JshopConfig->admin_show_product_files) return array();
		$list = array();
        if (isset($this->attribute_active_data) && isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
			$list = $this->attribute_active_data->ext_data->getDemoFiles();
		}
        $query = "SELECT * FROM `#__jshopping_products_files` WHERE product_id=".(int)$this->product_id." and demo!='' order by `ordering`";
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "beforeQuery"));
        $db->setQuery($query);
		$list0 = $db->loadObJectList();
        return array_merge($list0, $list);
    }

    function getSaleFiles(){
        $db = \JFactory::getDBO();
        $JshopConfig = \JSFactory::getConfig();
        if (!$JshopConfig->admin_show_product_files) return array();
		$list = array();
        if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
			$list = $this->attribute_active_data->ext_data->getSaleFiles();
		}
        $query = "SELECT id, file, file_descr FROM `#__jshopping_products_files`
				  WHERE product_id=".(int)$this->product_id." and file!='' order by `ordering`";
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "beforeQuery"));
        $db->setQuery($query);
		$list0 = $db->loadObJectList();
        return array_merge($list0, $list);
    }

    function getManufacturerInfo(){
        $manufacturers = \JSFactory::getAllManufacturer();
        if ($this->product_manufacturer_id && isset($manufacturers[$this->product_manufacturer_id])){
            return $manufacturers[$this->product_manufacturer_id];
        }else{
            return null;
        }
    }

    function getVendorInfo(){
        $vendors = \JSFactory::getAllVendor();
        if (isset($vendors[$this->vendor_id])){
            return $vendors[$this->vendor_id];
        }else{
            return null;
        }
    }

    /**
    * get first catagory for product
    */
    function getCategory() {
        $db = \JFactory::getDBO();
        $user = \JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $adv_query =' AND cat.access IN ('.$groups.')';
        $main_category_id = 0;
        if ($this->main_category_id) {
            $query = "SELECT pr_cat.category_id FROM `#__jshopping_products_to_categories` AS pr_cat
                LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                WHERE pr_cat.product_id=".(int)$this->product_id." AND cat.category_publish=1 AND cat.category_id=".(int)$this->main_category_id." ".$adv_query;
            $db->setQuery($query);
            $main_category_id = $db->loadResult();
        }
        if ($main_category_id) {
            $this->category_id = $main_category_id;
        } else {
            $query = "SELECT pr_cat.category_id FROM `#__jshopping_products_to_categories` AS pr_cat
                    LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                    WHERE pr_cat.product_id=".(int)$this->product_id." AND cat.category_publish=1 ".$adv_query;
            $db->setQuery($query);
            $this->category_id = $db->loadResult();
        }
		$obj = $this;
        \JFactory::getApplication()->triggerEvent('onBeforeProductGetCategory', array(&$obj));
        return $this->category_id;
    }

    function getFullQty(){
        if ($this->unlimited) return 1;
        $db = \JFactory::getDBO();
        $query = "select count(*) as countattr, SUM(count) AS qty from `#__jshopping_products_attr` where product_id=".(int)$this->product_id;
        $db->setQuery($query);
        $tmp = $db->loadObJect();
        if ($tmp->countattr>0){
            return $tmp->qty;
        }else{
            return $this->product_quantity;
        }
    }

    function getMinimumPrice(){
        $JshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();
        $min_price = $this->product_price;

        $query = "select count(*) as countattr, MIN(price) AS min_price from `#__jshopping_products_attr` where product_id=".(int)$this->product_id;
        $db->setQuery($query);
        $tmp = $db->loadObJect();
        if ($tmp->countattr>0){
            $min_price = $tmp->min_price;
        }

        $query = "select * from `#__jshopping_products_attr2` where product_id=".(int)$this->product_id;
        $db->setQuery($query);
        $product_attr_ind = $db->loadObJectList();
        if ($product_attr_ind){
            $tmpprice = array();
            foreach($product_attr_ind as $key=>$val){
                if ($val->price_mod=="+"){
                    $tmpprice[] = $min_price + $val->addprice;
                }elseif ($val->price_mod=="-"){
                    $tmpprice[] = $min_price - $val->addprice;
                }elseif ($val->price_mod=="*"){
                    $tmpprice[] = $min_price * $val->addprice;
                }elseif ($val->price_mod=="/"){
                    $tmpprice[] = $min_price / $val->addprice;
                }elseif ($val->price_mod=="%"){
                    $tmpprice[] = $min_price * $val->addprice / 100;
                }elseif ($val->price_mod=="="){
                    $tmpprice[] = $val->addprice;
                }
            }
            $min_price = min($tmpprice);
        }

        $query = "select MAX(discount) as max_discount from `#__jshopping_products_prices` where product_id=".(int)$this->product_id;
        $db->setQuery($query);
        $max_discount = $db->loadResult();
        if ($max_discount){
            if ($JshopConfig->product_price_qty_discount == 1){
                $min_price = $min_price - $max_discount;
            }else{
                $min_price = $min_price - ($min_price * $max_discount / 100);
            }
        }
        return $min_price;
    }

    function getExtendsData() {
        //$this->getRelatedProducts();
        $this->getDescription();
        $this->getTax();
        $this->getPricePreview();
        $this->getDeliveryTime();
    }

    function loadExtraFieldsData(){
        $ef = $this->getExtraFieldsData();
        foreach($ef as $f=>$v){
            $this->set($f, $v);
        }
    }

    function getExtraFieldsData(){
        $table = \JSFactory::getTable('producttofield');
        $table->load($this->product_id);
        $properties = $table->getProperties();
        $data = [];
        foreach($properties as $k=>$v){
            if (substr_count($k, 'extra_field_')){
                $data[$k] = $v;
            }
        }
        return $data;
    }

    function getDeliveryTimeId($globqty = 0){
        $JshopConfig = \JSFactory::getConfig();
        if ($globqty){
            $qty = $this->product_quantity;
        }else{
            $qty = $this->getQty();
        }
        if ($JshopConfig->hide_delivery_time_out_of_stock && $qty<=0){
            $this->delivery_times_id = 0;
        }
        return $this->delivery_times_id;
    }

    function getDeliveryTime($globqty = 0){
        $JshopConfig = \JSFactory::getConfig();
        $dti = $this->getDeliveryTimeId($globqty);
        if ($JshopConfig->show_delivery_time && $dti){
            $deliveryTimes = \JSFactory::getTable('deliveryTimes');
            $deliveryTimes->load($dti);
            $this->delivery_time = $deliveryTimes->getName();
        }else{
            $this->delivery_time = "";
        }
        return $this->delivery_time;
    }

    function getDescription() {
        $lang = \JSFactory::getLang();
        $name = $lang->get('name');
        $short_description = $lang->get('short_description');
        $description = $lang->get('description');
        $meta_title = $lang->get('meta_title');
        $meta_keyword = $lang->get('meta_keyword');
        $meta_description = $lang->get('meta_description');

        $this->name = $this->$name;
        $this->short_description = $this->$short_description;
        $this->description = $this->$description;
        $this->meta_title = $this->$meta_title;
        $this->meta_keyword = $this->$meta_keyword;
        $this->meta_description = $this->$meta_description;
        return $this->description;
    }

    function getPricePreview(){
        $this->getPrice(1, 1, 1, 1);
        if ($this->product_is_add_price){
            $this->product_add_prices = array_reverse($this->product_add_prices);
            foreach($this->product_add_prices as $k => $v) {
                $v->_tmp_var = "";
                $v->ext_price = "";
            }
        }
        $this->updateOtherPricesIncludeAllFactors();
    }

	function getUseUserDiscount(){
        $JshopConfig = \JSFactory::getConfig();
        if ($JshopConfig->user_discount_not_apply_prod_old_price && $this->product_old_price>0){
            return 0;
        }else{
            return 1;
        }
    }
	
	function getPrice($quantity=1, $enableCurrency=1, $enableUserDiscount=1, $enableParamsTax=1, $cartProduct=array()){
        $dispatcher = \JFactory::getApplication();
        $JshopConfig = \JSFactory::getConfig();
		$this->product_price_wp = $this->product_price;
        $this->product_price_calculate = $this->getPriceWithParams();
		$this->product_user_percent_discount = \JSFactory::getUserShop()->percent_discount;
        $obj = $this;
        $dispatcher->triggerEvent('onBeforeCalculatePriceProduct', array(&$quantity, &$enableCurrency, &$enableUserDiscount, &$enableParamsTax, &$obj, &$cartProduct));

        if ($this->product_is_add_price){
            $this->getAddPrices();
        }else{
            $this->product_add_prices = array();
        }

        if ($quantity && $this->product_is_add_price){
            foreach($this->product_add_prices as $key=>$value){
                if (($quantity >= $value->product_quantity_start && $quantity <= $value->product_quantity_finish) || ($quantity >= $value->product_quantity_start && $value->product_quantity_finish==0)){
                    $this->product_price_calculate = $value->price;
					$this->product_price_wp = $value->price_wp;
                    break;
                }
            }
        }

        if ($enableCurrency){
            $this->product_price_calculate = \JSHelper::getPriceFromCurrency($this->product_price_calculate, $this->currency_id);
			$this->product_price_wp = \JSHelper::getPriceFromCurrency($this->product_price_wp, $this->currency_id);

        }

        if ($enableParamsTax){
            $this->product_price_calculate = \JSHelper::getPriceCalcParamsTax($this->product_price_calculate, $this->product_tax_id);
			$this->product_price_wp = \JSHelper::getPriceCalcParamsTax($this->product_price_wp, $this->product_tax_id);
        }

        if ($enableUserDiscount && $this->product_user_percent_discount && $this->getUseUserDiscount()){            
			$this->product_price_default = $this->product_price_calculate;
			$this->product_price_calculate = \JSHelper::getPriceDiscount($this->product_price_calculate, $this->product_user_percent_discount);
			$this->product_price_wp = \JSHelper::getPriceDiscount($this->product_price_wp, $this->product_user_percent_discount);
        }
        $this->product_price_calculate1 = $this->product_price_calculate;
        $obj = $this;
        $dispatcher->triggerEvent('onCalculatePriceProduct', array($quantity, $enableCurrency, $enableUserDiscount, $enableParamsTax, &$obj, &$cartProduct) );
        $this->product_price_calculate0 = $this->product_price_calculate;
        if ($JshopConfig->price_product_round){
            $this->product_price_calculate = round($this->product_price_calculate, intval($JshopConfig->decimal_count));
        }
        return $this->product_price_calculate;
    }

    function getPriceCalculate(){
        return $this->product_price_calculate;
    }

    function getBasicPriceInfo(){
        $this->product_basic_price_show = $this->weight_volume_units!=0;
        if (!$this->product_basic_price_show) return 0;
        $JshopConfig = \JSFactory::getConfig();
        $units = \JSFactory::getAllUnits();
        $unit = $units[$this->basic_price_unit_id];
        if ($JshopConfig->calc_basic_price_from_product_price){
            $this->product_basic_price_wvu = $this->weight_volume_units;
        }else{
            $this->product_basic_price_wvu = $this->getWeight_volume_units();
        }
        $this->product_basic_price_weight = $this->product_basic_price_wvu / $unit->qty;
        if ($JshopConfig->calc_basic_price_from_product_price){
            $prod_price = $this->product_price_wp;
        }else{
            $prod_price = $this->product_price_calculate1;
        }
        if ($JshopConfig->price_product_round){
            $prod_price = round($prod_price, $JshopConfig->decimal_count);
        }
        $this->product_basic_price_calculate = $prod_price / $this->product_basic_price_weight;
        $this->product_basic_price_unit_name = $unit->name;
        $this->product_basic_price_unit_qty = $unit->qty;
        
        if (isset($this->product_add_prices)) {
            foreach($this->product_add_prices as $k => $v) {
                if ($JshopConfig->calc_basic_price_from_product_price) {
                    $bp = $v->price_wp;
                } else {
                    $bp = $v->price;
                }
                if ($JshopConfig->price_product_round) {
                    $bp = round($bp, $JshopConfig->decimal_count);
                }
                $this->product_add_prices[$k]->basic_price = $bp / $this->product_basic_price_weight;
            }
        }

        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterGetBasicPriceInfoProduct', array(&$obj));
        return 1;
    }

	function getBasicPrice(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_calculate ?? 0;
    }

	function getBasicWeight(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_weight ?? 0;
    }

	function getBasicPriceUnit(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_unit_name ?? '';
    }

    function getAddPrices(){
        $JshopConfig = \JSFactory::getConfig();
        $productprice = \JSFactory::getTable('productprice');
        $this->product_add_prices = $productprice->getAddPrices($this->product_id);

        $price = $this->getPriceWithParams();
		$price_wp = $this->product_price;
        foreach($this->product_add_prices as $k=>$v){
            if ($JshopConfig->product_price_qty_discount == 1){
                $this->product_add_prices[$k]->price = $price - $v->discount; //discount value
				$this->product_add_prices[$k]->price_wp = $price_wp - $v->discount;
            }else{
                $this->product_add_prices[$k]->price = $price - ($price * $v->discount / 100); //discount percent
				$this->product_add_prices[$k]->price_wp = $price_wp - ($price_wp * $v->discount / 100);
            }
        }

        if (!$this->add_price_unit_id) $this->add_price_unit_id = $JshopConfig->product_add_price_default_unit;
        $units = \JSFactory::getAllUnits();
        $unit = $units[$this->add_price_unit_id];
        $this->product_add_price_unit = $unit->name;
        if ($this->product_add_price_unit=="") $this->product_add_price_unit=JSHP_ST_;
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterGetAddPricesProduct', array(&$obj));
    }

    function getTax(){
        $taxes = \JSFactory::getAllTaxes();
		$this->product_tax = isset($taxes[$this->product_tax_id]) ? $taxes[$this->product_tax_id] : 0;
        $dispatcher = \JFactory::getApplication();
        $obj = $this;
        $dispatcher->triggerEvent('onBeforeGetTaxProduct', array(&$obj));
        return $this->product_tax;
    }

    function updateOtherPricesIncludeAllFactors(){
        $JshopConfig = \JSFactory::getConfig();
        $userShop = \JSFactory::getUserShop();

        $this->product_old_price = $this->getOldPrice();
        $this->product_old_price = \JSHelper::getPriceFromCurrency($this->product_old_price, $this->currency_id);
        $this->product_old_price = \JSHelper::getPriceCalcParamsTax($this->product_old_price, $this->product_tax_id);
        if ($this->getUseUserDiscount()){
			$this->product_old_price = \JSHelper::getPriceDiscount($this->product_old_price, $userShop->percent_discount);
		}

        if (is_array($this->product_add_prices)){
            foreach ($this->product_add_prices as $key=>$value){
                $this->product_add_prices[$key]->price = \JSHelper::getPriceFromCurrency($this->product_add_prices[$key]->price, $this->currency_id);
                $this->product_add_prices[$key]->price = \JSHelper::getPriceCalcParamsTax($this->product_add_prices[$key]->price, $this->product_tax_id);
				if ($this->getUseUserDiscount()){
					$this->product_add_prices[$key]->price = \JSHelper::getPriceDiscount($this->product_add_prices[$key]->price, $userShop->percent_discount);
				}
            }
        }
        $dispatcher = \JFactory::getApplication();
        $obj = $this;
        $dispatcher->triggerEvent('onUpdateOtherPricesIncludeAllFactors', array(&$obj) );
    }

	function getExtraFields($type = 1){
        $_cats = $this->getCategories();
        $cats = array();
        foreach($_cats as $v){
            $cats[] = $v->category_id;
        }
        $productExtraField = $this->getExtraFieldsData();

        $fields = array();
        $JshopConfig = \JSFactory::getConfig();
        $hide_fields = $JshopConfig->getProductHideExtraFields();
        $cart_fields = $JshopConfig->getCartDisplayExtraFields();
        $fieldvalues = \JSFactory::getAllProductExtraFieldValue();
        $listfield = \JSFactory::getAllProductExtraField();
        foreach($listfield as $val){
            if ($type==1 && in_array($val->id, $hide_fields)) continue;
            if ($type==2 && !in_array($val->id, $cart_fields)) continue;

            if ($val->allcats){
                $fields[] = $val;
            }else{
                $insert = 0;
                foreach($cats as $cat_id){
                    if (in_array($cat_id, $val->cats)) $insert = 1;
                }
                if ($insert){
                    $fields[] = $val;
                }
            }
        }

        $rows = array();
        foreach($fields as $field){
            $field_id = $field->id;
            $field_name = "extra_field_".$field_id;
            if ($field->type==0){
                if ($productExtraField[$field_name] != 0 && $productExtraField[$field_name] != ''){
                    $listid = explode(',', $productExtraField[$field_name]);
                    $tmp = array();
                    foreach($listid as $extrafiledvalueid){
                        $tmp[] = $fieldvalues[$extrafiledvalueid];
                    }
                    $extra_field_value = implode($JshopConfig->multi_charactiristic_separator, $tmp);
                    $rows[] = array("id"=>$field_id, "name"=>$listfield[$field_id]->name, "description"=>$listfield[$field_id]->description, "value"=>$extra_field_value, "groupname"=>$listfield[$field_id]->groupname, 'field_value_ids'=>$listid, 'group_id' => $field->group);
                }
            }else{
                if (isset($productExtraField[$field_name]) && $productExtraField[$field_name]!=""){
                    $rows[] = array("id"=>$field_id, "name"=>$listfield[$field_id]->name, "description"=>$listfield[$field_id]->description, "value"=>$productExtraField[$field_name], "groupname"=>$listfield[$field_id]->groupname, 'group_id' => $field->group);
                }
            }
        }

        $rowsblock = array();
        foreach($rows as $k=>$v){
            if ($v['groupname']==''){
                $grname = 'defaultgroup';
            }else{
                $grname = $v['groupname'];
            }
            $rowsblock[$grname][] = $v;
        }

        $rows = array();
        foreach($rowsblock as $bl=>$val){
            foreach($val as $k=>$v){
                if ($k==0){
                    $v['grshow'] = 1;
                }else{
                    $v['grshow'] = 0;
                }
                if ($k==(count($val)-1)){
                    $v['grshowclose'] = 1;
                }else{
                    $v['grshowclose'] = 0;
                }
                $rows[$v['id']] = $v;
            }
        }
        return $rows;
    }

    function getReviews($limitstart = 0, $limit = 20) {
        $db = \JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_products_reviews` WHERE product_id=".(int)$this->product_id." and publish=1 order by review_id desc";
        $db->setQuery($query, $limitstart, $limit);
        $rows = $db->loadObJectList();
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterGetReviewsProduct', array(&$obj, &$rows, &$limitstart, &$limit));
        return $rows;
    }

    function getReviewsCount(){
        $db = \JFactory::getDBO();
        $query = "SELECT count(review_id) FROM `#__jshopping_products_reviews` WHERE product_id=".(int)$this->product_id." and publish=1";
        $db->setQuery($query);
        $row = $db->loadResult();
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterGetReviewsCountProduct', array(&$obj, &$row));
        return $row;
    }

    function getAverageRating() {
        $db = \JFactory::getDBO();
        $query = "SELECT ROUND(AVG(mark),2) FROM `#__jshopping_products_reviews` WHERE product_id=".(int)$this->product_id." and mark > 0 and publish=1";
        $db->setQuery($query);
        $row = $db->loadResult();
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onAfterGetAverageRatingProduct', array(&$obj, &$row));
        return $row;
    }

    function loadAverageRating(){
        $this->average_rating = $this->getAverageRating();
        if (!$this->average_rating) $this->average_rating = 0;
    }

    function loadReviewsCount(){
        $this->reviews_count = $this->getReviewsCount();
    }

    function getExtAttributeData($pid){
        $product = \JSFactory::getTable('product');
        $product->load($pid);
    return $product;
    }

    function getBuildSelectAttributes($attributeValues, $attributeActive, $displayonlyattrtype = null){
        $JshopConfig = \JSFactory::getConfig();
        if (!$JshopConfig->admin_show_attributes) return array();
        $dispatcher = \JFactory::getApplication();
        $attrib = \JSFactory::getAllAttributes();
		$userShop = \JSFactory::getUserShop();
        $productShop = \JSFactory::getModel('productshop', 'Site');
        $selects = array();
		$obj = $this;
        $dispatcher->triggerEvent('onBeforeBuildSelectAttribute', array(&$attributeValues, &$attributeActive, &$selects, &$attrib, &$obj));

        foreach($attrib as $k=>$v){
            $attr_id = $v->attr_id;
            if ($displayonlyattrtype){
                $v->attr_type = $displayonlyattrtype;
            }
            if (isset($attributeValues[$attr_id]) && $attributeValues[$attr_id]){
                if (isset($attributeActive[$attr_id])){
                    $_firstval = $attributeActive[$attr_id];
                }else{
                    $_firstval = 0;
                }
                $selects[$attr_id] = new \stdClass();
                $selects[$attr_id]->attr_id = $attr_id;
                $selects[$attr_id]->attr_name = $v->name;
                $selects[$attr_id]->attr_description = $v->description;
                $selects[$attr_id]->groupname = $v->groupname;
                $selects[$attr_id]->firstval = $_firstval;
                $options = $attributeValues[$attr_id];
                $attrimage = array();
                foreach($options as $k2=>$v2){
                    $attrimage[$v2->val_id] = $v2->image;
					$addPrice = isset($v2->addprice) ? $v2->addprice : 0;
                    $addPrice = \JSHelper::getPriceFromCurrency($addPrice, $this->currency_id);
                    $addPrice = \JSHelper::getPriceCalcParamsTax($addPrice, $this->product_tax_id);
                    if ($userShop->percent_discount){
                        $addPrice = \JSHelper::getPriceDiscount($addPrice, $userShop->percent_discount);
                    }
                    $options[$k2]->addprice = $addPrice;
                    $options[$k2]->price_mod = isset($v2->price_mod) ? $v2->price_mod : "";
                }
                
                if ($JshopConfig->attr_display_addprice){
                    foreach($options as $k2=>$v2){
                        if (($v2->price_mod=="+" || $v2->price_mod=="-" || $JshopConfig->attr_display_addprice_all_sign) && $v2->addprice>0){
                            $ext_price_info = " (".$v2->price_mod.\JSHelper::formatprice($v2->addprice, null, 0, -1).")";
                            $options[$k2]->value_name .=$ext_price_info;
                        }
                    }
                }
                if (isset($attributeActive[$attr_id]) && isset($attrimage[$attributeActive[$attr_id]])){
                    $_active_image = $attrimage[$attributeActive[$attr_id]];
                }else{
                    $_active_image = '';
                }
                if (isset($attributeActive[$attr_id])){
                    $_select_active = $attributeActive[$attr_id];
                }else{
                    $_select_active = '';
                }

                if ($v->attr_type==1){
                    if ($JshopConfig->product_attribut_first_value_empty){
                        $first = array();
                        $first[] = \JHTML::_('select.option', '0', \JText::_('JSHOP_SELECT'), 'val_id','value_name');
                        $options = array_merge($first, $options);
                    }
                }

                $view = $productShop->getView("product");
                $view->setLayout($JshopConfig->product_attribute_type_template[$v->attr_type]);                
                $view->set('attr_id', $attr_id);
                $view->set('options', $options);                    
                $view->set('config', $JshopConfig);
                $view->set('active', $_select_active);
                $view->set('url_attr_img', $this->getUrlProdAttrImg($_active_image));
                $view->set('attribute', $v);
                $dispatcher->triggerEvent('onBuildSelectAttributeView', array(&$view));                    
                $selects[$attr_id]->selects = $view->loadTemplate();

                $dispatcher->triggerEvent('onBuildSelectAttribute', array(&$attributeValues, &$attributeActive, &$selects, &$options, &$attr_id, &$v));
            }
        }
        $grname = '';
        foreach($selects as $k=>$v){
            if ($v->groupname!=$grname){
                $grname = $v->groupname;
                $selects[$k]->grshow = 1;
            }else{
                $selects[$k]->grshow = 0;
            }
        }
    return $selects;
    }
    
    function getUrlProdAttrImg($img){
        $JshopConfig = \JSFactory::getConfig();
        if ($img){
            $path = $JshopConfig->image_attributes_live_path;
        }else{
            $path = $JshopConfig->live_path."images";
            $img = "blank.gif";
        }
        return $path."/".$img;
    }

	function checkView(&$category, &$user, &$category_id, &$listcategory){
        $obj = $this;
		\JFactory::getApplication()->triggerEvent('onBeforeCheckProductPublish', array(&$obj, &$category, &$category_id, &$listcategory));
		if ($category->category_publish==0 || $this->product_publish==0 || !in_array($this->access, $user->getAuthorisedViewLevels()) || !in_array($category_id, $listcategory)){
			return 0;
		}else{
			return 1;
		}
	}

}