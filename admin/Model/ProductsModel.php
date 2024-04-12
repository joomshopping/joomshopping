<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\Component\Jshopping\Site\Lib\UploadFile;
use Joomla\Component\Jshopping\Site\Lib\ImageLib;

defined('_JEXEC') or die();

class ProductsModel extends BaseadminModel{
    
    protected $nameTable = 'ProductTable';

    function _getAllProductsQueryForFilter($filter){
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        $where = "";
        if (isset($filter['without_product_id']) && $filter['without_product_id']){
            $where .= " AND pr.product_id <> '".$db->escape($filter['without_product_id'])."' ";
        }
        if (isset($filter['category_id']) && $filter['category_id']){
            $category_id = $filter['category_id'];
            $where .= " AND pr_cat.category_id = '".$db->escape($filter['category_id'])."' ";
        }
        if (isset($filter['text_search']) && $filter['text_search']){
            $text_search = $filter['text_search'];
            $word = addcslashes($db->escape($text_search), "_%");
            $where .=  "AND (LOWER(pr.`".$lang->get('name')."`) LIKE '%" . $word . "%' OR LOWER(pr.`".$lang->get('short_description')."`) LIKE '%" . $word . "%' OR LOWER(pr.`".$lang->get('description')."`) LIKE '%" . $word . "%' OR pr.product_ean LIKE '%" . $word . "%' OR pr.product_id LIKE '%" . $word . "%' OR pr.manufacturer_code LIKE '%" . $word . "%')";
        }
        if (isset($filter['manufacturer_id']) && $filter['manufacturer_id']){
            $where .= " AND pr.product_manufacturer_id = '".$db->escape($filter['manufacturer_id'])."' ";
        }
        if (isset($filter['label_id']) && $filter['label_id']){
            $where .= " AND pr.label_id = '".$db->escape($filter['label_id'])."' ";
        }
        if (isset($filter['publish']) && $filter['publish']){
            if ($filter['publish']==1) $_publish = 1; else $_publish = 0;
            $where .= " AND pr.product_publish = '".$db->escape($_publish)."' ";
        }
        if (isset($filter['vendor_id']) && $filter['vendor_id'] >= 0){
            $where .= " AND pr.vendor_id = '".$db->escape($filter['vendor_id'])."' ";
        }
		extract(\JSHelper::js_add_trigger(get_defined_vars(), "after"));
    return $where;
    }

    function _allProductsOrder($order = null, $orderDir = null, $category_id = 0){
        if ($order && $orderDir){
            $fields = array("product_id"=>"pr.product_id", "name"=>"name",'category'=>"namescats","manufacturer"=>"man_name","vendor"=>"v_f_name","ean"=>"ean","qty"=>"qty","price"=>"pr.product_price","hits"=>"pr.hits","date"=>"pr.product_date_added", "product_name_image"=>"pr.image");
            if ($category_id) $fields['ordering'] = "pr_cat.product_ordering";
            if (strtolower($orderDir)!="asc") $orderDir = "desc";
			if ($orderDir=="desc") $fields['qty'] ='pr.unlimited desc, qty';
            if (!$fields[$order]) return "";
            return "order by ".$fields[$order]." ".$orderDir;
        }else{
            return "";
        }
    }

    function getAllProducts($filter, $limitstart = null, $limit = null, $order = null, $orderDir = null, $options = array()){
        $jshopConfig = \JSFactory::getConfig();
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        if ($limit > 0){
            $limit_query = " LIMIT ".$limitstart.", ".$limit;
        }else{
            $limit_query = "";
        }
        if (isset($filter['category_id']))
            $category_id = $filter['category_id'];
        else
            $category_id = '';

        $where = $this->_getAllProductsQueryForFilter($filter);

        $query_filed = ""; $query_join = "";
        if ($jshopConfig->admin_show_vendors){
            $query_filed .= ", pr.vendor_id, V.f_name as v_f_name, V.l_name as v_l_name";
            $query_join .= " left join `#__jshopping_vendors` as V on pr.vendor_id=V.id ";
        }
		if ($jshopConfig->admin_product_list_manufacture_code){
			$query_filed .= ", pr.manufacturer_code";
		}
        
        $db->setQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $db->execute();

        if ($category_id) {
            $query = "SELECT pr.product_id, pr.product_publish, pr_cat.product_ordering, pr.`".$lang->get('name')."` as name, pr.`".$lang->get('short_description')."` as short_description, man.`".$lang->get('name')."` as man_name, pr.product_ean as ean, pr.product_quantity as qty, pr.image as image, pr.product_price, pr.currency_id, pr.hits, pr.unlimited, pr.product_date_added, pr.label_id $query_filed FROM `#__jshopping_products` AS pr
                      LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      $query_join
                      WHERE pr.parent_id=0 ".$where." ".$this->_allProductsOrder($order, $orderDir, $category_id)." ".$limit_query;
        }else{
            $spec_where = "GROUP_CONCAT(cat.`".$lang->get('name')."` SEPARATOR '<br>') AS namescats";            

            $query = "SELECT pr.product_id, pr.product_publish, pr.`".$lang->get('name')."` as name, pr.`".$lang->get('short_description')."` as short_description, man.`".$lang->get('name')."` as man_name, ".$spec_where.", pr.product_ean as ean, pr.product_quantity as qty, pr.image as image, pr.product_price, pr.currency_id, pr.hits, pr.unlimited, pr.product_date_added, pr.label_id $query_filed FROM `#__jshopping_products` AS pr
                      LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id=cat.category_id
                      LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      $query_join
                      WHERE pr.parent_id=0 ".$where." GROUP BY pr.product_id ".$this->_allProductsOrder($order, $orderDir)." ".$limit_query;
        }
        
		$dispatcher = \JFactory::getApplication();
        $obj = $this;
        $dispatcher->triggerEvent('onBeforeDisplayListProductsGetAllProducts', array(&$obj, &$query, $filter, $limitstart, $limit, $order, $orderDir));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        if (isset($options['label_image']) && $options['label_image']){
            $this->loadLablelImageForProductList($rows);
        }
        if (isset($options['vendor_name']) && $options['vendor_name']){
            $this->loadVendorNameForProductList($rows);
        }
        return $rows;
    }

    function getCountAllProducts($filter){
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        if (isset($filter['category_id']))
            $category_id = $filter['category_id'];
        else
            $category_id = '';

        $where = $this->_getAllProductsQueryForFilter($filter);
        if ($category_id) {
            $query = "SELECT count(pr.product_id) FROM `#__jshopping_products` AS pr
                      LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      WHERE pr.parent_id=0 ".$where;
        } else {
            $query = "SELECT count(pr.product_id) FROM `#__jshopping_products` AS pr
                      LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      WHERE pr.parent_id=0 ".$where;
        }
		$dispatcher = \JFactory::getApplication();
        $obj = $this;
        $dispatcher->triggerEvent('onBeforeDisplayListProductsGetCountAllProducts', array(&$obj, &$query, $filter));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function productInCategory($product_id, $category_id) {
        $db = \JFactory::getDBO();
        $query = "SELECT prod_cat.category_id FROM `#__jshopping_products_to_categories` AS prod_cat
                   WHERE prod_cat.product_id = '".$db->escape($product_id)."' AND prod_cat.category_id = '".$db->escape($category_id)."'";
        $db->setQuery($query);
        $res = $db->execute();
        return $db->getNumRows($res);
    }

    function getMaxOrderingInCategory($category_id) {
        $db = \JFactory::getDBO();
        $query = "SELECT MAX(product_ordering) as k FROM `#__jshopping_products_to_categories` WHERE category_id = '".$db->escape($category_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }

    function setCategoryToProduct($product_id, $categories = array()){
        $db = \JFactory::getDBO();
        foreach($categories as $cat_id){
            if (!$this->productInCategory($product_id, $cat_id)){
                $ordering = $this->getMaxOrderingInCategory($cat_id)+1;
                $query = "INSERT INTO `#__jshopping_products_to_categories` SET `product_id` = '".$db->escape($product_id)."', `category_id` = '".$db->escape($cat_id)."', `product_ordering` = '".$db->escape($ordering)."'";
                $db->setQuery($query);
                $db->execute();
            }
        }

        //delete other cat for product
        $query = "select `category_id` from `#__jshopping_products_to_categories` where `product_id` = '".$db->escape($product_id)."'";
        $db->setQuery($query);
        $listcat = $db->loadObjectList();
        foreach($listcat as $val){
            if (!in_array($val->category_id, $categories)){
                $query = "delete from `#__jshopping_products_to_categories` where `product_id` = '".$db->escape($product_id)."' and `category_id` = '".$db->escape($val->category_id)."'";
                $db->setQuery($query);
                $db->execute();
            }
        }

    }

    function getRelatedProducts($product_id){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT relation.product_related_id AS product_id, prod.`".$lang->get('name')."` as name, prod.image as image, prod.product_ean as ean
                FROM `#__jshopping_products_relations` AS relation
                LEFT JOIN `#__jshopping_products` AS prod ON prod.product_id=relation.product_related_id
                WHERE relation.product_id = '".$db->escape($product_id)."' order by relation.id";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $jshopConfig = \JSFactory::getConfig();
        $_alias = \JSFactory::getModel("alias");
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        foreach($languages as $lang){
            $post['name_'.$lang->language] = trim($post['name_'.$lang->language]);
            if ($jshopConfig->create_alias_product_category_auto && $post['alias_'.$lang->language]==""){
                $post['alias_'.$lang->language] = $post['name_'.$lang->language];
            }
            $post['alias_'.$lang->language] = \JApplicationHelper::stringURLSafe($post['alias_'.$lang->language]);
            if ($post['alias_'.$lang->language]!="" && !$_alias->checkExistAlias2Group($post['alias_'.$lang->language], $lang->language, $post['product_id'])){
                $post['alias_'.$lang->language] = "";
                \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_ALIAS_ALREADY_EXIST'));
            }
            $post['description_'.$lang->language] = $input->get('description'.$lang->id, '', 'RAW');
            $post['short_description_'.$lang->language] = $input->get('short_description_'.$lang->language, '', 'RAW');
        }
        return $post;
    }

    function save(array $post){
        $jshopConfig = \JSFactory::getConfig();        

        $dispatcher = \JFactory::getApplication();
        $product = \JSFactory::getTable('product');
        $id_vendor_cuser = \JSHelperAdmin::getIdVendorForCUser();

        if ($id_vendor_cuser && $post['product_id']){
            checkAccessVendorToProduct($id_vendor_cuser, $post['product_id']);
        }
		$post['different_prices'] = 0;
        if (isset($post['product_is_add_price']) && $post['product_is_add_price']){
            $post['different_prices'] = 1;
        }
        if (!isset($post['product_publish'])) $post['product_publish'] = 0;
        if (!isset($post['product_is_add_price'])) $post['product_is_add_price'] = 0;
        if (!isset($post['unlimited'])) $post['unlimited'] = 0;
        $post['product_price'] = \JSHelper::saveAsPrice($post['product_price']);
        $post['product_old_price'] = \JSHelper::saveAsPrice($post['product_old_price']);
        if (isset($post['product_buy_price'])){
            $post['product_buy_price'] = \JSHelper::saveAsPrice($post['product_buy_price']);
        }else{
            $post['product_buy_price'] = null;
        }
        $post['product_weight'] = \JSHelper::saveAsPrice($post['product_weight']);
        $post['weight_volume_units'] = \JSHelper::saveAsPrice($post['weight_volume_units']);
        if (!isset($post['related_products'])) $post['related_products'] = array();
        if (!$post['product_id']) $post['product_date_added'] = \JSHelper::getJsDate();
        if (!isset($post['attrib_price'])) $post['attrib_price'] = null;
        if (!isset($post['attrib_ind_id'])) $post['attrib_ind_id'] = null;
        if (!isset($post['attrib_ind_price'])) $post['attrib_ind_price'] = null;
        if (!isset($post['attrib_ind_price_mod'])) $post['attrib_ind_price_mod'] = null;
        if (!isset($post['freeattribut'])) $post['freeattribut'] = null;
        $post['date_modify'] = \JSHelper::getJsDate();
        $post['edit'] = intval($post['product_id']);
        if (!isset($post['product_add_discount'])) $post['product_add_discount'] = 0;
        $post['min_price'] = $this->getMinimalPrice($post['product_price'], $post['attrib_price'], array($post['attrib_ind_id'], $post['attrib_ind_price_mod'], $post['attrib_ind_price']), $post['product_is_add_price'], $post['product_add_discount']);
        if ($id_vendor_cuser){
            $post['vendor_id'] = $id_vendor_cuser;
        }

        if (isset($post['attr_count']) && is_array($post['attr_count'])){
            $qty = 0;
            foreach($post['attr_count'] as $key => $_qty) {
				$post['attr_count'][$key] = \JSHelper::saveAsPrice($_qty);
                if ($_qty > 0) $qty += $post['attr_count'][$key];
            }

            $post['product_quantity'] = $qty;
        }

        if ($post['unlimited']){
            $post['product_quantity'] = 1;
        }

		$post['product_quantity'] = \JSHelper::saveAsPrice($post['product_quantity']);

        if (isset($post['productfields']) && is_array($post['productfields'])){
            foreach($post['productfields'] as $productfield=>$val){
                if (is_array($val)){
                    $post[$productfield] = implode(',', $val);
                }
            }
        }
        if ($jshopConfig->admin_show_product_extra_field){
            $_productfields = \JSFactory::getModel("productfields");
            $list_productfields = $_productfields->getList(1);
            foreach($list_productfields as $v){
                if ($v->type==0 && !isset($post['extra_field_'.$v->id])){
                    $post['extra_field_'.$v->id] = '';
                }
            }
        }

        if (is_array($post['attrib_price']) && count(array_unique($post['attrib_price']))>1){
            $post['different_prices'] = 1;
        }
        if (is_array($post['attrib_ind_price'])){
            $tmp_attr_ind_price = array();
            foreach($post['attrib_ind_price'] as $k=>$v){
                $tmp_attr_ind_price[] = $post['attrib_ind_price_mod'][$k].$post['attrib_ind_price'][$k];
            }
            if (count(array_unique($tmp_attr_ind_price))>1){
                $post['different_prices'] = 1;
            }
        }

        $dispatcher->triggerEvent('onBeforeDisplaySaveProduct', array(&$post, &$product));

        $product->bind($post);

        if (($product->min_price==0 || $product->product_price==0) && !$jshopConfig->user_as_catalog && $product->parent_id==0){
            \JSError::raiseNotice("", \JText::_('JSHOP_YOU_NOT_SET_PRICE'));
        }

        if (isset($post['set_main_image'])) {
            $image= \JSFactory::getTable('image');
            $image->load($post['set_main_image']);
            if ($image->image_id){
                $product->image = $image->image_name;
            }
        }

        if (!$product->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE')."<br>".$product->getError());
            return 0;
        }

        $product_id = $product->product_id;

        $dispatcher->triggerEvent('onAfterSaveProduct', array(&$product));

        if ($jshopConfig->admin_show_product_video && $product->parent_id==0) {
            $this->uploadVideo($product, $product_id, $post);
        }

        $this->uploadImages($product, $product_id, $post);

        if ($jshopConfig->admin_show_product_files){
            $this->uploadFiles($product, $product_id, $post);
        }

        $this->saveAttributes($product, $product_id, $post);

        if ($jshopConfig->admin_show_freeattributes){
            $this->saveFreeAttributes($product_id, $post['freeattribut']);
        }

        if ($post['product_is_add_price']){
            $this->saveAditionalPrice($product_id, $post['product_add_discount'], $post['quantity_start'], $post['quantity_finish']);
        }
		
		$this->saveExtraFields($product_id, $post);

        if ($product->parent_id==0){
            $this->setCategoryToProduct($product_id, $post['category_id']);
        }

        $this->saveRelationProducts($product, $product_id, $post);
        $this->saveProductOptions($product_id, (array)$post['options']);

        $dispatcher->triggerEvent('onAfterSaveProductEnd', array($product->product_id));

        return $product;
    }

    function saveAditionalPrice($product_id, $product_add_discount, $quantity_start, $quantity_finish){
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_products_prices` WHERE `product_id` = '".$db->escape($product_id)."'";
        $db->setQuery($query);
        $db->execute();

        $counter = 0;
        if (count($product_add_discount)){
            foreach ($product_add_discount as $key=>$value){

                if ((!$quantity_start[$key] && !$quantity_finish[$key])) continue;

                $query = "INSERT INTO `#__jshopping_products_prices` SET
                            `product_id` = '" . $db->escape($product_id) . "',
                            `discount` = '" . $db->escape(\JSHelper::saveAsPrice($product_add_discount[$key])) . "',
                            `product_quantity_start` = '" . intval($quantity_start[$key]) . "',
                            `product_quantity_finish` = '" . intval($quantity_finish[$key]) . "'";
                $db->setQuery($query);
                $db->execute();
                $counter++;
            }
        }
        $product = \JSFactory::getTable('product');
        $product->load($product_id);
        $product->product_is_add_price = ($counter>0) ? (1) : (0);
        $product->store();
    }

    function saveFreeAttributes($product_id, $attribs){
        $db = \JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_products_free_attr` WHERE `product_id` = '".$db->escape($product_id)."'";
        $db->setQuery($query);
        $db->execute();

        if (is_array($attribs)){
            foreach($attribs as $attr_id=>$v){
                $query = "insert into `#__jshopping_products_free_attr` set `product_id` = '".$db->escape($product_id)."', attr_id='".$db->escape($attr_id)."'";
                $db->setQuery($query);
                $db->execute();
            }
        }
    }
	
	function saveExtraFields($product_id, $post){
		$table = \JSFactory::getTable('producttofield');
		$table->bind($post);
		$table->set('product_id', $product_id);
		return $table->store();
	}

    function saveProductOptions($product_id, $options){
        $db = \JFactory::getDBO();
        foreach($options as $key=>$value){
            $query = "DELETE FROM `#__jshopping_products_option` WHERE `product_id` = '".$db->escape($product_id)."' AND `key`='".$db->escape($key)."'";
            $db->setQuery($query);
            $db->execute();

            $query = "insert into `#__jshopping_products_option` set `product_id` = '".$db->escape($product_id)."', `key`='".$db->escape($key)."', `value`='".$db->escape($value)."'";
            $db->setQuery($query);
            $db->execute();
        }
    }

    function getMinimalPrice($price, $attrib_prices, $attrib_ind_price_data, $is_add_price, $add_discounts){
        $minprice = \JSHelper::saveAsPrice($price);
        if (is_array($attrib_prices)){
            $minprice = min(array_map(array('\JSHelper','saveAsPrice'), $attrib_prices));
        }

        if (is_array($attrib_ind_price_data[0])){
            $attr_ind_id = array_unique($attrib_ind_price_data[0]);
            $startprice = $minprice;
            foreach($attr_ind_id as $attr_id){
                $tmpprice = array();
                foreach($attrib_ind_price_data[0] as $k=>$tmp_attr_id){
                    if ($tmp_attr_id==$attr_id){
						$attrib_ind_price_data[2][$k] = \JSHelper::saveAsPrice($attrib_ind_price_data[2][$k]);
                        if ($attrib_ind_price_data[1][$k]=="+"){
                            $tmpprice[] = $startprice + $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="-"){
                            $tmpprice[] = $startprice - $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="*"){
                            $tmpprice[] = $startprice * $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="/"){
                            $tmpprice[] = $startprice / $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="%"){
                            $tmpprice[] = $startprice * $attrib_ind_price_data[2][$k] / 100;
                        }elseif ($attrib_ind_price_data[1][$k]=="="){
                            $tmpprice[] = $attrib_ind_price_data[2][$k];
                        }
                    }
                }
                $startprice = min($tmpprice);
            }
            $minprice = $startprice;
        }

        if ($is_add_price && is_array($add_discounts)){
            $jshopConfig = \JSFactory::getConfig();
            $max_discount = max($add_discounts);
            if ($jshopConfig->product_price_qty_discount == 1){
                $minprice = $minprice - $max_discount; //discount value
            }else{
                $minprice = $minprice - ($minprice * $max_discount / 100); //discount percent
            }
        }
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        return $minprice;
    }

    function copyProductBuildQuery($table, $array, $product_id){
        $db = \JFactory::getDBO();
        $query = "INSERT INTO `#__jshopping_products_".$table."` SET ";
        $array_keys = array('image_id', 'price_id', 'review_id', 'video_id', 'product_attr_id', 'value_id', 'id');
        foreach ($array as $key=>$value){
            if (in_array($key, $array_keys)) continue;
            if ($key=='product_id') $value = $product_id;
            $query .= "`".$key."` = '".$db->escape($value)."', ";
        }
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        return $query = substr($query, 0, strlen($query) - 2);
    }

    function uploadVideo($product, $product_id, $post){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $image_prev_video = "";
        for($i=0;$i<$jshopConfig->product_video_upload_count;$i++){
			if (!(isset($post['product_insert_code_'.$i]) && ($post['product_insert_code_'.$i] == 1))) {
				$upload = new UploadFile($_FILES['product_video_'.$i]);
				$upload->setDir($jshopConfig->video_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
				if ($upload->upload()){
					$file_video = $upload->getName();
					@chmod($jshopConfig->video_product_path."/".$file_video, 0777);

					$upload2 = new UploadFile($_FILES['product_video_preview_'.$i]);					
                    $upload2->setAllowFile($jshopConfig->allow_image_upload);
					$upload2->setDir($jshopConfig->video_product_path);
                    $upload2->setFileNameMd5(0);
                    $upload2->setFilterName(1);
					if ($upload2->upload()){
						$image_prev_video = $upload2->getName();
						@chmod($jshopConfig->video_product_path."/".$image_prev_video, 0777);
					}else{
						if ($upload2->getError() != 4){
							\JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING_VIDEO_PREVIEW'));
							\JSHelper::saveToLog("error.log", "SaveProduct - Error upload video preview. code: ".$upload2->getError());
						}
					}
					unset($upload2);
					$this->addToProductVideo($product_id, $file_video, $image_prev_video);
				}else{
					if ($upload->getError() != 4){
						\JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING_VIDEO'));
						\JSHelper::saveToLog("error.log", "SaveProduct - Error upload video. code: ".$upload->getError());
					}
				}
				unset($upload);
			} else {
                $code_video = (string)$app->input->get('product_video_code_'.$i, '', 'RAW');
				if ($code_video) {
					$upload2 = new UploadFile($_FILES['product_video_preview_'.$i]);
                    $upload2->setAllowFile($jshopConfig->allow_image_upload);
					$upload2->setDir($jshopConfig->video_product_path);
                    $upload2->setFileNameMd5(0);
                    $upload2->setFilterName(1);
					if ($upload2->upload()){
						$image_prev_video = $upload2->getName();
						@chmod($jshopConfig->video_product_path."/".$image_prev_video, 0777);
					}else{
						if ($upload2->getError() != 4){
							\JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING_VIDEO_PREVIEW'));
							\JSHelper::saveToLog("error.log", "SaveProduct - Error upload video preview. code: ".$upload2->getError());
						}
					}
					unset($upload2);
					$this->addToProductVideoCode($product_id, $code_video, $image_prev_video);
				}
			}
        }
    }

    function addToProductVideo($product_id, $name_video, $preview_image = '') {
        $db = \JFactory::getDBO();
        $query = "INSERT INTO `#__jshopping_products_videos`
                   SET `product_id` = '" . $db->escape($product_id) . "', `video_name` = '" . $db->escape($name_video) . "', `video_preview` = '" . $db->escape($preview_image) . "'";
        $db->setQuery($query);
        $db->execute();
    }

	function addToProductVideoCode($product_id, $code_video, $preview_image = '') {
        $db = \JFactory::getDBO();
        $query = "INSERT INTO `#__jshopping_products_videos`
                   SET `product_id` = '" . $db->escape($product_id) . "', `video_code` = '" . $db->escape($code_video) . "', `video_preview` = '" . $db->escape($preview_image) . "'";
        $db->setQuery($query);
        $db->execute();
    }

    function uploadImages($product, $product_id, $post){
        $jshopConfig = \JSFactory::getConfig();
        $dispatcher = \JFactory::getApplication();
        $app = \JFactory::getApplication();

        for($i=0; $i<$jshopConfig->product_image_upload_count; $i++){
            $upload = new UploadFile($_FILES['product_image_'.$i]);            
            $upload->setAllowFile($jshopConfig->allow_image_upload);
            $upload->setDir($jshopConfig->image_product_path);
			if ($jshopConfig->product_imagename_lowercase){
				$upload->name = strtolower($upload->name);
			}
            $upload->setFileNameMd5(0);
            $upload->setFilterName(1);
            $upload->setMaxSizeFile($jshopConfig->image_product_max_size_file);
            if ($upload->upload()){
                $name_image = $upload->getName();
                $name_thumb = 'thumb_'.$name_image;
                $name_full = 'full_'.$name_image;
                @chmod($jshopConfig->image_product_path."/".$name_image, 0777);

                $path_image = $jshopConfig->image_product_path."/".$name_image;
                $path_thumb = $jshopConfig->image_product_path."/".$name_thumb;
                $path_full =  $jshopConfig->image_product_path."/".$name_full;
                rename($path_image, $path_full);

                if (!$this->checkUploadImageMinSize($path_full)){
                    \JSError::raiseNotice("",\JText::_('JSHOP_ERROR_UPLOAD_MIN_SIZE_IMAGE'));
                }

				if ($jshopConfig->image_product_original_width || $jshopConfig->image_product_original_height){
                    if (!ImageLib::resizeImageMagic($path_full, $jshopConfig->image_product_original_width, $jshopConfig->image_product_original_height, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_full, $jshopConfig->image_quality, $jshopConfig->image_fill_color, $jshopConfig->image_interlace)){
                        \JSError::raiseWarning("",\JText::_('JSHOP_ERROR_CREATE_THUMBAIL'));
                        \JSHelper::saveToLog("error.log", "SaveProduct - Error create thumbail");
                        $error = 1;
                    }
                }

                $error = 0;
                if ($post['size_im_product']==3){
                    copy($path_full, $path_thumb);
                    @chmod($path_thumb, 0777);
                }else{
                    if ($post['size_im_product']==1){
                        $product_width_image = $jshopConfig->image_product_width;
                        $product_height_image = $jshopConfig->image_product_height;
                    }else{
                        $product_width_image = $app->input->getInt('product_width_image');
                        $product_height_image = $app->input->getInt('product_height_image');
                    }

                    if ($product_width_image || $product_height_image){
                        if (!ImageLib::resizeImageMagic($path_full, $product_width_image, $product_height_image, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_thumb, $jshopConfig->image_quality, $jshopConfig->image_fill_color, $jshopConfig->image_interlace)){
                            \JSError::raiseWarning("",\JText::_('JSHOP_ERROR_CREATE_THUMBAIL'));
                            \JSHelper::saveToLog("error.log", "SaveProduct - Error create thumbail");
                            $error = 1;
                        }
                        @chmod($path_thumb, 0777);
                    }
                }

                if ($post['size_full_product']==3){
                    copy($path_full, $path_image);
                    @chmod($path_image, 0777);
                }else{
                    if ($post['size_full_product']==1){
                        $product_full_width_image = $jshopConfig->image_product_full_width;
                        $product_full_height_image = $jshopConfig->image_product_full_height;
                    }else{
                        $product_full_width_image = $app->input->getInt('product_full_width_image');
                        $product_full_height_image = $app->input->getInt('product_full_height_image');
                    }

                    if ($product_full_width_image || $product_full_height_image){
                        if (!ImageLib::resizeImageMagic($path_full, $product_full_width_image, $product_full_height_image, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_image, $jshopConfig->image_quality, $jshopConfig->image_fill_color, $jshopConfig->image_interlace)){
                            \JSError::raiseWarning("",\JText::_('JSHOP_ERROR_CREATE_THUMBAIL'));
                            $error = 1;
                        }
                        @chmod($path_image, 0777);
                    }
                }

                if (!$error){
                    $this->addToProductImage($product_id, $name_image, $post["product_image_descr_".$i]);
                    $dispatcher->triggerEvent('onAfterSaveProductImage', array($product_id, $name_image));
                }
            }else{
                if ($upload->getError() != 4){
                    \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING_IMAGE'));
                    \JSHelper::saveToLog("error.log", "SaveProduct - Error upload image. code: ".$upload->getError());
                }
            }

            unset($upload);
        }

		for($i=0; $i<$jshopConfig->product_image_upload_count; $i++){
			if ($post['product_folder_image_'.$i] != '') {
				if (file_exists($jshopConfig->image_product_path .'/'.$post['product_folder_image_'.$i])) {
					$name_image = $post['product_folder_image_'.$i];
					$name_thumb = 'thumb_'.$name_image;
					$name_full = 'full_'.$name_image;
					$this->addToProductImage($product_id, $name_image, $post["product_image_descr_".$i]);
					$dispatcher->triggerEvent('onAfterSaveProductFolerImage', array($product_id, $name_full, $name_image, $name_thumb));
				}
			}
		}

        if (!$product->image){
            $list_images = $product->getImages();
            if (count($list_images)){
                $product = \JSFactory::getTable('product');
                $product->load($product_id);
                $product->image = $list_images[0]->image_name;
                $product->store();
            }
        }

        if (isset($post['old_image_descr'])){
            $this->renameProductImageOld($post['old_image_descr'], $post['old_image_ordering']);
        }
    }

    function checkUploadImageMinSize($pathimg){
        $jshopConfig = \JSFactory::getConfig();
        $size = getimagesize($pathimg);
        $width = $size[0];
        $height = $size[1];
        if ($jshopConfig->image_product_original_width && $width < $jshopConfig->image_product_original_width){
            return 0;
        }
        if ($jshopConfig->image_product_original_height && $height < $jshopConfig->image_product_original_height){
            return 0;
        }
        if ($jshopConfig->image_product_full_width && $width < $jshopConfig->image_product_full_width){
            return 0;
        }
        if ($jshopConfig->image_product_full_height && $height < $jshopConfig->image_product_full_height){
            return 0;
        }
        return 1;
    }

    function addToProductImage($product_id, $name_image, $image_descr) {
        $image = \JSFactory::getTable('image');
        $image->set("image_id", 0);
        $image->set("product_id", $product_id);
        $image->set("image_name", $name_image);
        $image->set("name", $image_descr);
        $image->set("ordering", $image->getNextOrder("product_id='".intval($product_id)."'"));
        $image->store();
    }

    function renameProductImageOld($image_descr, $image_ordering){
        $db = \JFactory::getDBO();
        foreach($image_descr as $id=>$v){
            $query = "update `#__jshopping_products_images` set `name`='".$db->escape($image_descr[$id])."', `ordering`='".$db->escape($image_ordering[$id])."' where `image_id`='".$db->escape($id)."'";
            $db->setQuery($query);
            $db->execute();
        }
    }

    function uploadFiles($product, $product_id, $post){
        $jshopConfig = \JSFactory::getConfig();
        $dispatcher = \JFactory::getApplication();
        if (!isset($post['product_demo_descr'])) $post['product_demo_descr'] = '';
        if (!isset($post['product_file_descr'])) $post['product_file_descr'] = '';
        if (!isset($post['product_file_sort'])) $post['product_file_sort'] = '';

        for($i=0; $i<$jshopConfig->product_file_upload_count; $i++){
            $file_demo = "";
            $file_sale = "";
            if ($jshopConfig->product_file_upload_via_ftp!=1){
                $upload = new UploadFile($_FILES['product_demo_file_'.$i]);
                $upload->setDir($jshopConfig->demo_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
                if ($upload->upload()){
                    $file_demo = $upload->getName();
                    @chmod($jshopConfig->demo_product_path."/".$file_demo, 0777);
                }else{
                    if ($upload->getError() != 4){
                        \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING_FILE_DEMO'));
                        \JSHelper::saveToLog("error.log", "SaveProduct - Error upload demo. code: ".$upload->getError());
                    }
                }
                unset($upload);

                $upload = new UploadFile($_FILES['product_file_'.$i]);
                $upload->setDir($jshopConfig->files_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
                if ($upload->upload()){
                    $file_sale = $upload->getName();
                    @chmod($jshopConfig->files_product_path."/".$file_sale, 0777);
                }else{
                    if ($upload->getError() != 4){
                        \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING_FILE_SALE'));
                        \JSHelper::saveToLog("error.log", "SaveProduct - Error upload file sale. code: ".$upload->getError());
                    }
                }
                unset($upload);
            }

            if (!$file_demo && isset($post['product_demo_file_name_'.$i]) && $post['product_demo_file_name_'.$i]){
                $file_demo = $post['product_demo_file_name_'.$i];
            }
            if (!$file_sale && isset($post['product_file_name_'.$i]) && $post['product_file_name_'.$i]){
                $file_sale = $post['product_file_name_'.$i];
            }

            if ($file_demo!="" || $file_sale!=""){
                $this->addToProductFiles($product_id, $file_demo, $post['product_demo_descr_'.$i], $file_sale, $post['product_file_descr_'.$i], $post['product_file_sort_'.$i], $i, $post);
            }
        }
        //Update description files
        $this->productUpdateDescriptionFiles($post['product_demo_descr'], $post['product_file_descr'], $post['product_file_sort'], $post);
    }

    function addToProductFiles($product_id, $file_demo, $demo_descr, $file_sale, $file_descr, $sort, $i = null, $post = array()){
        $data = array();
        $data['product_id'] = $product_id;
        $data['demo'] = $file_demo;
        $data['demo_descr'] = $demo_descr;
        $data['file'] = $file_sale;
        $data['file_descr'] = $file_descr;
        $data['ordering'] = $sort;

        $row = \JSFactory::getTable('productFiles');
        $row->bind($data);
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "beforeStore"));
        return $row->store();
    }

    function productUpdateDescriptionFiles($demo_descr, $file_descr, $ordering, $post = array()){
        if (!is_array($demo_descr)){
            return 0;
        }
        foreach($demo_descr as $file_id=>$value){
            $row = \JSFactory::getTable('productFiles');
            $row->id = $file_id;
            $row->demo_descr = $demo_descr[$file_id];
            $row->file_descr = $file_descr[$file_id];
            $row->ordering = $ordering[$file_id];
            extract(\JSHelper::js_add_trigger(get_defined_vars(), "beforeStore"));
            $row->store();
        }
        return 1;
    }

    function saveAttributes($product, $product_id, $post){

        $dispatcher = \JFactory::getApplication();
        $productAttribut = \JSFactory::getTable('productAttribut');
        $productAttribut->set("product_id", $product_id);

        $list_exist_attr = $product->getAttributes();
        if (isset($post['product_attr_id'])){
            $list_saved_attr = $post['product_attr_id'];
        }else{
            $list_saved_attr = array();
        }
        foreach($list_exist_attr as $v){
            if (!in_array($v->product_attr_id, $list_saved_attr)){
                $productAttribut->deleteAttribute($v->product_attr_id);
            }
        }

        if (is_array($post['attrib_price'])){
            foreach($post['attrib_price'] as $k=>$v){
                $a_price = \JSHelper::saveAsPrice($post['attrib_price'][$k]);
                $a_old_price = \JSHelper::saveAsPrice($post['attrib_old_price'][$k]);
                $a_buy_price = \JSHelper::saveAsPrice($post['attrib_buy_price'][$k]);
                $a_count = $post['attr_count'][$k];
                $a_ean = $post['attr_ean'][$k];
                $a_manufacturer_code = $post['attr_manufacturer_code'][$k];
                $a_weight_volume_units = $post['attr_weight_volume_units'][$k];
                $a_weight = $post['attr_weight'][$k];

                if ($post['product_attr_id'][$k]){
                    $productAttribut->load($post['product_attr_id'][$k]);
                }else{
                    $productAttribut->set("product_attr_id", 0);
                    $productAttribut->set("ext_attribute_product_id", 0);
                }
                $productAttribut->set("price", $a_price);
                $productAttribut->set("old_price", $a_old_price);
                $productAttribut->set("buy_price", $a_buy_price);
                $productAttribut->set("count", $a_count);
                $productAttribut->set("ean", $a_ean);
                $productAttribut->set("manufacturer_code", $a_manufacturer_code);
                $productAttribut->set("weight_volume_units", $a_weight_volume_units);
                $productAttribut->set("weight", $a_weight);
                foreach($post['attrib_id'] as $field_id=>$val){
                    $productAttribut->set("attr_".intval($field_id), $val[$k]);
                }
                $dispatcher->triggerEvent('onBeforeProductAttributStore', array(&$productAttribut, &$product, &$product_id, &$post, $k));
                if ($productAttribut->check()){
                    $productAttribut->store();
                }
            }
        }

        $productAttribut2 = \JSFactory::getTable('productAttribut2');
        $productAttribut2->set("product_id", $product_id);
        $productAttribut2->deleteAttributeForProduct();

        if (is_array($post['attrib_ind_id'])){
            foreach($post['attrib_ind_id'] as $k=>$v){
                $a_id = intval($post['attrib_ind_id'][$k]);
                $a_value_id = intval($post['attrib_ind_value_id'][$k]);
                $a_price = \JSHelper::saveAsPrice($post['attrib_ind_price'][$k]);
                $a_mod_price = $post['attrib_ind_price_mod'][$k];

                $productAttribut2->set("id", 0);
                $productAttribut2->set("product_id", $product_id);
                $productAttribut2->set("attr_id", $a_id);
                $productAttribut2->set("attr_value_id", $a_value_id);
                $productAttribut2->set("price_mod", $a_mod_price);
                $productAttribut2->set("addprice", $a_price);
                $dispatcher->triggerEvent('onBeforeProductAttribut2Store', array(&$productAttribut2, &$product, &$product_id, &$post, $k));
                if ($productAttribut2->check()){
                    $productAttribut2->store();
                }
            }
        }
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "after"));
    }

    function saveRelationProducts($product, $product_id, $post){
        $db = \JFactory::getDBO();

        if ($post['edit']) {
            $query = "DELETE FROM `#__jshopping_products_relations` WHERE `product_id` = '".$db->escape($product_id)."'";
            $db->setQuery($query);
            $db->execute();
        }

        $post['related_products'] = array_unique($post['related_products']);
        foreach($post['related_products'] as $key => $value){
            if ($value!=0){
                $query = "INSERT INTO `#__jshopping_products_relations` SET `product_id` = '" . $db->escape($product_id) . "', `product_related_id` = '" . $db->escape($value) . "'";
                $db->setQuery($query);
                $db->execute();
            }
        }
    }

    function getModPrice($price, $newprice, $mod){
        $result = 0;
        switch($mod){
            case '=':
            $result = $newprice;
            break;
            case '+':
            $result = $price + $newprice;
            break;
            case '-':
            $result = $price - $newprice;
            break;
            case '*':
            $result = $price * $newprice;
            break;
            case '/':
            $result = $price / $newprice;
            break;
            case '%':
            $result = $price * $newprice / 100;
            break;
        }
    return $result;
    }

    function updatePriceAndQtyDependAttr($product_id, $post){
        $db = \JFactory::getDBO();
        $_adv_query = array();
        if ($post['product_price']!=""){
            $price = \JSHelper::saveAsPrice($post['product_price']);
            if ($post['mod_price']=='%')
                $_adv_query[] = " `price`=`price` * '".$price."' / 100 ";
            elseif($post['mod_price']=='=')
                $_adv_query[] = " `price`= '".$price."' ";
            else
                $_adv_query[] = " `price`=`price` ".$post['mod_price']." '".$price."' ";
        }

        if ($post['product_old_price']!=""){
            $price = \JSHelper::saveAsPrice($post['product_old_price']);
            if ($post['mod_old_price']=='%')
                $_adv_query[] = " `old_price`=`old_price` * '".$price."' / 100 ";
            elseif($post['mod_old_price']=='=')
                $_adv_query[] = " `old_price`= '".$price."' ";
            else
                $_adv_query[] = " `old_price`=`old_price` ".$post['mod_old_price']." '".$price."' ";
        }

        if ($post['product_quantity']!=""){
            $_adv_query[] = " `count`= '".$db->escape($post['product_quantity'])."' ";
        }

        if (count($_adv_query)>0){
            $adv_query = implode(" , ", $_adv_query);
            $query = "update `#__jshopping_products_attr` SET ".$adv_query." where product_id='".$db->escape($product_id)."'";
            $db->setQuery($query);
            $db->execute();
        }
    }

	function productGroupUpdate($id, $post){
		$jshopConfig = \JSFactory::getConfig();
		$product = \JSFactory::getTable('product');
		$product->load($id);
		if ($post['access']!=-1){
			$product->set('access', $post['access']);
		}
		if ($post['product_publish']!=-1){
			$product->set('product_publish', $post['product_publish']);
		}
		if ($post['product_weight']!=""){
			$product->set('product_weight', $post['product_weight']);
		}
		if ($post['product_quantity']!=""){
			$product->set('product_quantity', $post['product_quantity']);
			$product->set('unlimited', 0);
		}
		if (isset($post['unlimited']) && $post['unlimited']){
			$product->set('product_quantity', 1);
			$product->set('unlimited', 1);
		}
		if (isset($post['product_template']) && $post['product_template'] != -1){
			$product->set('product_template', $post['product_template']);
		}
		if (isset($post['product_tax_id']) && $post['product_tax_id']!=-1){
			$product->set('product_tax_id', $post['product_tax_id']);
		}
		if (isset($post['product_manufacturer_id']) && $post['product_manufacturer_id']!=-1){
			$product->set('product_manufacturer_id', $post['product_manufacturer_id']);
		}
		if (isset($post['vendor_id']) && $post['vendor_id']!=-1){
			$product->set('vendor_id', $post['vendor_id']);
		}
		if (isset($post['delivery_times_id']) && $post['delivery_times_id']!=-1){
			$product->set('delivery_times_id', $post['delivery_times_id']);
		}
		if (isset($post['label_id']) && $post['label_id']!=-1){
			$product->set('label_id', $post['label_id']);
		}
		if (isset($post['weight_volume_units']) && $post['weight_volume_units']!=""){
			$product->set('weight_volume_units', $post['weight_volume_units']);
			$product->set('basic_price_unit_id', $post['basic_price_unit_id']);
		}
		if ($post['product_price']!=""){
			$oldprice = $product->product_price;
			$price = $this->getModPrice($product->product_price, \JSHelper::saveAsPrice($post['product_price']), $post['mod_price']);
			$product->set('product_price', $price);
			if ($post['use_old_val_price']==1){
				$product->set('product_old_price', $oldprice);
			}
		}
		if (isset($post['product_old_price']) && $post['product_old_price']!=""){
			$price = $this->getModPrice($product->product_old_price, \JSHelper::saveAsPrice($post['product_old_price']), $post['mod_old_price']);
			$product->set('product_old_price', $price);
		}
		if (isset($post['product_buy_price']) && $post['product_buy_price']!=""){
			$product->set('product_buy_price', $post['product_buy_price']);
		}
		if (isset($post['product_price']) && $post['product_price']!="" || $post['product_old_price']!=""){
			$product->set('currency_id', $post['currency_id']);
		}
		if (isset($post['category_id']) && $post['category_id']){
			$this->setCategoryToProduct($id, $post['category_id']);
		}
		if ($jshopConfig->admin_show_product_extra_field){
			$_productfields = \JSFactory::getModel("productfields");
			$list_productfields = $_productfields->getList(1);
			$product_ef = [];
			foreach($list_productfields as $v){
				$_nef = 'extra_field_'.$v->id;
				switch($v->type){
					case 0:
						if (isset($post['productfields'][$_nef]) and is_array($post['productfields'][$_nef]) and count($post['productfields'][$_nef]) > 0){
							if ($v->multilist == 1 || ($v->multilist == 0 and !in_array(0,$post['productfields'][$_nef]))){
								$product_ef[$_nef] = implode(',', $post['productfields'][$_nef]);
							}
						}
					break;
					case 1:
						if (isset($post[$_nef]) and $post[$_nef] != ''){
							$product_ef[$_nef] = $post[$_nef];
						}
					break;
				}
			}
			if (count($product_ef)){
				$this->saveExtraFields($id, $product_ef);
			}
		}
		$this->updatePriceAndQtyDependAttr($id, $post);
		$product->store();

		if ($post['product_price']!=""){
			$mprice = $product->getMinimumPrice();
			$product->set('min_price', $mprice);
		}
		if (!$product->unlimited){
			$qty = $product->getFullQty();
			$product->set('product_quantity', $qty);
		}
		$product->date_modify = \JSHelper::getJsDate();
		extract(\JSHelper::js_add_trigger(get_defined_vars(), "beforeStore"));
		$product->store();
	}

    function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $res = array();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveProduct', array(&$cid));
        foreach($cid as $id){
            $this->delete($id);
            if ($msg){
                $app->enqueueMessage(sprintf(\JText::_('JSHOP_PRODUCT_DELETED'), $id), 'message');
            }
            $res[$id] = true;
        }
        $dispatcher->triggerEvent('onAfterRemoveProduct', array(&$cid));
        return $res;
    }

    function delete($pid){
        if (!$pid) {
			return 0;
		}
        $jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();

        $product = \JSFactory::getTable('product');
        $product->load($pid);
        $query = "DELETE FROM `#__jshopping_products` WHERE `product_id` = '".$db->escape($pid)."' or `parent_id` = '".$db->escape($pid)."' ";
        $db->setQuery($query);
        $db->execute();

        $query = "DELETE FROM `#__jshopping_products_attr` WHERE `product_id` = '" . $db->escape($pid) . "'";
        $db->setQuery($query);
        $db->execute();

        $query = "DELETE FROM `#__jshopping_products_attr2` WHERE `product_id` = '" . $db->escape($pid) . "'";
        $db->setQuery($query);
        $db->execute();

        $query = "DELETE FROM `#__jshopping_products_prices` WHERE `product_id` = '".$db->escape($pid)."'";
        $db->setQuery($query);
        $db->execute();

        $query = "DELETE FROM `#__jshopping_products_relations` WHERE `product_id` = '" . $db->escape($pid) . "' OR `product_related_id` = '" . $db->escape($pid) . "'";
        $db->setQuery($query);
        $db->execute();

        $query = "DELETE FROM `#__jshopping_products_to_categories` WHERE `product_id` = '" . $db->escape($pid) . "'";
        $db->setQuery($query);
        $db->execute();
		
		$query = "DELETE FROM `#__jshopping_products_to_extra_fields` WHERE `product_id` = '" . $db->escape($pid) . "'";
        $db->setQuery($query);
        $db->execute();

        $images = $product->getImages();
        $videos = $product->getVideos();
        $files = $product->getFiles();

        if (count($images)){
            foreach($images as $image){
                $query = "select count(*) as k from #__jshopping_products_images where image_name='".$db->escape($image->image_name)."' and product_id!='".$db->escape($pid)."'";
                $db->setQuery($query);
                if (!$db->loadResult()){
                    @unlink(\JSHelper::getPatchProductImage($image->image_name,'thumb',2));
                    @unlink(\JSHelper::getPatchProductImage($image->image_name,'',2));
                    @unlink(\JSHelper::getPatchProductImage($image->image_name,'full',2));
                }
            }
        }

        $query = "DELETE FROM `#__jshopping_products_images` WHERE `product_id` = '".$db->escape($pid)."'";
        $db->setQuery($query);
        $db->execute();

        if (count($videos)) {
            foreach ($videos as $video) {
                $query = "select count(*) as k from #__jshopping_products_videos where video_name='".$db->escape($video->video_name)."' and product_id!='".$db->escape($pid)."'";
                $db->setQuery($query);
                if (!$db->loadResult()){
                    @unlink($jshopConfig->video_product_path . "/" . $video->video_name);
                    if ($video->video_preview){
                        @unlink($jshopConfig->video_product_path . "/" . $video->video_preview);
                    }
                }
            }
        }

        $query = "DELETE FROM `#__jshopping_products_videos` WHERE `product_id` = '" . $db->escape($pid) . "'";
        $db->setQuery($query);
        $db->execute();

        if (count($files)){
            foreach($files as $file){
                $query = "select count(*) as k from #__jshopping_products_files where demo='".$db->escape($file->demo)."' and product_id!='".$db->escape($pid)."'";
                $db->setQuery($query);
                if (!$db->loadResult()){
                    @unlink($jshopConfig->demo_product_path."/".$file->demo);
                }

                $query = "select count(*) as k from #__jshopping_products_files where file='".$db->escape($file->file)."' and product_id!='".$db->escape($pid)."'";
                $db->setQuery($query);
                if (!$db->loadResult()){
                    @unlink($jshopConfig->files_product_path."/".$file->file);
                }
            }
        }

        $query = "DELETE FROM `#__jshopping_products_files` WHERE `product_id` = '" . $db->escape($pid) . "'";
        $db->setQuery($query);
        $db->execute();
        
        $query = "DELETE FROM `#__jshopping_products_free_attr` WHERE `product_id` = '" . $db->escape($pid) . "'";
        $db->setQuery($query);
        $db->execute();
    }

    function deleteImage($image_id){
        $jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();

        $query = "SELECT * FROM `#__jshopping_products_images` WHERE image_id = '".$db->escape($image_id)."'";
        $db->setQuery($query);
        $row = $db->loadObject();

        $query = "DELETE FROM `#__jshopping_products_images` WHERE `image_id` = '".$db->escape($image_id)."'";
        $db->setQuery($query);
        $db->execute();

        $query = "select count(*) as k from #__jshopping_products_images where image_name='".$db->escape($row->image_name)."' and product_id!='".$db->escape($row->product_id)."'";
        $db->setQuery($query);
        if (!$db->loadResult()){
            @unlink(\JSHelper::getPatchProductImage($row->image_name,'thumb',2));
            @unlink(\JSHelper::getPatchProductImage($row->image_name,'',2));
            @unlink(\JSHelper::getPatchProductImage($row->image_name,'full',2));
        }

        $product = \JSFactory::getTable('product');
        $product->load($row->product_id);
        if ($product->image==$row->image_name){
            $product->image = '';
            $list_images = $product->getImages();
            if (count($list_images)){
                $product->image = $list_images[0]->image_name;
            }
            $product->store();
        }
    }

    function deleteVideo($video_id){
        $jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();

        $query = "SELECT * FROM `#__jshopping_products_videos` WHERE video_id = '".$db->escape($video_id)."'";
        $db->setQuery($query);
        $row = $db->loadObject();

        $query = "select count(*) from #__jshopping_products_videos where video_name='".$db->escape($row->video_name)."' and product_id!='".$db->escape($row->product_id)."'";
        $db->setQuery($query);
        if (!$db->loadResult()){
            @unlink($jshopConfig->video_product_path . "/" . $row->video_name);
            if ($row->video_preview){
                @unlink($jshopConfig->video_product_path . "/" . $row->video_preview);
            }
        }

        $query = "DELETE FROM `#__jshopping_products_videos` WHERE `video_id` = '" . $db->escape($video_id) . "'";
        $db->setQuery($query);
        $db->execute();
    }

    function deleteFile($id, $type){
        $jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();

        $query = "SELECT * FROM `#__jshopping_products_files` WHERE `id` = '".$db->escape($id)."'";
        $db->setQuery($query);
        $row = $db->loadObject();

        $delete_row = 0;

        if ($type=="demo"){
            if ($row->file==""){
                $query = "DELETE FROM `#__jshopping_products_files` WHERE `id` = '".$db->escape($id)."'";
                $db->setQuery($query);
                $db->execute();
                $delete_row = 1;
            }else{
                $query = "update `#__jshopping_products_files` set `demo`='', demo_descr='' WHERE `id` = '".$db->escape($id)."'";
                $db->setQuery($query);
                $db->execute();
            }

            $query = "select count(*) as k from #__jshopping_products_files where demo='".$db->escape($row->demo)."'";
            $db->setQuery($query);
            if (!$db->loadResult()){
                @unlink($jshopConfig->demo_product_path."/".$row->demo);
            }
        }

        if ($type=="file"){
            if ($row->demo==""){
                $query = "DELETE FROM `#__jshopping_products_files` WHERE `id` = '".$db->escape($id)."'";
                $db->setQuery($query);
                $db->execute();
                $delete_row = 1;
            }else{
                $query = "update `#__jshopping_products_files` set `file`='', file_descr='' WHERE `id` = '".$db->escape($id)."'";
                $db->setQuery($query);
                $db->execute();
            }

            $query = "select count(*) as k from #__jshopping_products_files where file='".$db->escape($row->file)."'";
            $db->setQuery($query);
            if (!$db->loadResult()){
                @unlink($jshopConfig->files_product_path."/".$row->file);
            }
        }
        return $delete_row;
    }

    function publish(array $cid, $flag){
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishProduct', array(&$cid, &$flag));
        foreach($cid as $id){
            $this->publishProduct($id, $flag);
        }
        $dispatcher->triggerEvent('onAfterPublishProduct', array(&$cid, &$flag));
    }

    function publishProduct($id, $flag){
        $db = \JFactory::getDBO();
        $query = "UPDATE `#__jshopping_products` SET `product_publish`=".(int)$flag." "
                . "WHERE `product_id`=".(int)$id;
        $db->setQuery($query);
        $db->execute();
    }

    function copyProducts($cid){
        $text = array();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeCopyProduct', array(&$cid));
        foreach($cid as $key=>$value){
            $product = $this->copyProduct($value);
			$dispatcher->triggerEvent('onCopyProductEach', array(&$cid, &$key, &$value, &$product));
            $text[] = sprintf(\JText::_('JSHOP_PRODUCT_COPY_TO'), $value, $product->product_id)."<br>";
        }
        $dispatcher->triggerEvent('onAfterCopyProduct', array(&$cid));
        return $text;
    }

    function copyProduct($pid){
        $db = \JFactory::getDBO();
        $languages = \JSFactory::getModel("languages")->getAllLanguages(1);
        $tables = array('attr', 'attr2', 'images', 'prices', 'relations', 'to_categories', 'videos', 'files', 'free_attr', 'to_extra_fields');
        \JFactory::getApplication()->triggerEvent('onBeforeStartCopyProduct', array(&$pid, &$tables, &$languages));

        $product = \JSFactory::getTable('product');
        $product->load($pid);
        $product->product_id = null;
        $product->product_publish = 0;
        foreach($languages as $lang){
            $name_alias = 'alias_'.$lang->language;
            if ($product->$name_alias){
                $product->$name_alias = $product->$name_alias.date('ymdHis');
            }
        }
        $product->product_date_added = \JSHelper::getJsDate();
        $product->date_modify = \JSHelper::getJsDate();
        $product->average_rating = 0;
        $product->reviews_count = 0;
        $product->hits = 0;
        $product->store();

        $array = array();
        foreach($tables as $table){
            $query = "SELECT * FROM `#__jshopping_products_".$table."` AS prod_table WHERE prod_table.product_id = '".$db->escape($pid)."'";
            $db->setQuery($query);
            $array[] = $db->loadAssocList();
        }

        $i = 0;
        foreach($array as $value2){
            if (count($value2)){
                foreach($value2 as $value3){
                    $db->setQuery($this->copyProductBuildQuery($tables[$i], $value3, $product->product_id));
                    $db->execute();
                }
            }
            $i++;
        }

        //change order in category
        $query = "select * from #__jshopping_products_to_categories where product_id='".$product->product_id."'";
        $db->setQuery($query);
        $list = $db->loadObjectList();

        foreach($list as $val){
            $query = "select max(product_ordering) as k from #__jshopping_products_to_categories where category_id='".$val->category_id."' ";
            $db->setQuery($query);
            $ordering = $db->loadResult() + 1;

            $query = "update #__jshopping_products_to_categories set product_ordering='".$ordering."' where category_id='".$val->category_id."' and product_id='".$product->product_id."' ";
            $db->setQuery($query);
            $db->execute();
        }

        $listExtAttrProdId = $this->getListExtAttributeProductId($product->product_id);
		foreach($listExtAttrProdId as $k=>$v){
			$ext_prod = $this->copyProduct($v->ext_attribute_product_id);
			$query = "update #__jshopping_products_attr set `ext_attribute_product_id`=".(int)$ext_prod->product_id." where product_attr_id=".(int)$v->product_attr_id;
			$db->setQuery($query);
			$db->execute();
		}
        return $product;
    }
	
	function getListExtAttributeProductId($product_id){
		$db = \JFactory::getDBO();
		$query = "select product_attr_id, ext_attribute_product_id from #__jshopping_products_attr where product_id=".(int)$product_id." and ext_attribute_product_id!=0";
        $db->setQuery($query);
        return $db->loadObjectList();
	}

    function orderProductInCategory($product_id, $category_id, $number, $order){
        $db = \JFactory::getDBO();
        switch($order){
            case 'up':
                $query = "SELECT a.*
                       FROM `#__jshopping_products_to_categories` AS a
                       WHERE a.product_ordering < '" . $number . "' AND a.category_id = '" . $category_id . "'
                       ORDER BY a.product_ordering DESC
                       LIMIT 1";
                break;
            case 'down':
                $query = "SELECT a.*
                       FROM `#__jshopping_products_to_categories` AS a
                       WHERE a.product_ordering > '" . $number . "' AND a.category_id = '" . $category_id . "'
                       ORDER BY a.product_ordering ASC
                       LIMIT 1";
        }

        $db->setQuery($query);
        $row = $db->loadObject();

        $query1 = "UPDATE `#__jshopping_products_to_categories` AS a
                     SET a.product_ordering = '" . $row->product_ordering . "'
                     WHERE a.product_id = '" . $product_id . "' AND a.category_id = '" . $category_id . "'";
        $query2 = "UPDATE `#__jshopping_products_to_categories` AS a
                     SET a.product_ordering = '" . $number . "'
                     WHERE a.product_id = '" . $row->product_id . "' AND a.category_id = '" . $category_id . "'";
        $db->setQuery($query1);
        $db->execute();
        $db->setQuery($query2);
        $db->execute();
    }

    function saveOrderProductInCategory(array $cid, $order, $category_id){
        $db = \JFactory::getDBO();
        foreach($cid as $k=>$product_id){
            $query = "UPDATE `#__jshopping_products_to_categories`
                     SET product_ordering = '".intval($order[$k])."'
                     WHERE product_id = '".intval($product_id)."' AND category_id = '".intval($category_id)."'";
            $db->setQuery($query);
            $db->execute();
        }
    }

    protected function loadLablelImageForProductList(&$rows){
        $jshopConfig = \JSFactory::getConfig();
        foreach($rows as $key=>$v){
            if ($rows[$key]->label_id){
                $image = \JSHelper::getNameImageLabel($rows[$key]->label_id);
                if ($image){
                    $rows[$key]->_label_image = $jshopConfig->image_labels_live_path."/".$image;
                }
                $rows[$key]->_label_name = \JSHelper::getNameImageLabel($rows[$key]->label_id, 2);
            }
        }
    }

    protected function loadVendorNameForProductList(&$rows){
        $main_vendor = \JSFactory::getTable('vendor');
        $main_vendor->loadMain();
        foreach($rows as $k=>$v){
            if ($v->vendor_id){
                $rows[$k]->vendor_name = $v->v_f_name." ".$v->v_l_name;
            }else{
                $rows[$k]->vendor_name = $main_vendor->f_name." ".$main_vendor->l_name;
            }
        }
    }

}