<?php
/**
* @version      5.1.0 13.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model\Productlist;
use Joomla\Component\Jshopping\Site\Helper\Selects;
defined('_JEXEC') or die();

class ListModel{
    	
    public $multi_page_list = 1;
    public $table = null;
    public $default_adv_query = "";
    public $default_adv_from = "";

    public function loadRequestData(){
		$app = \JFactory::getApplication();
		$context = $this->getContext();
		$limitstart = $app->input->getInt('limitstart');
        $orderby = $app->getUserStateFromRequest($context.'orderby', 'orderby', $this->getDefaultProductSortingDirection(), 'int');
        $order = $app->getUserStateFromRequest($context.'order', 'order', $this->getDefaultProductSorting(), 'int');
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', $this->getCountProductsPerPage(), 'int');
        if (!$limit){
            $limit = $this->getCountProductsPerPage();
        }
		$this->setOrder($order);
		$this->setOrderBy($orderby);
        $this->setLimit($limit);
        $this->setLimitStart($limitstart);
	}

	public function load(){
        $dispatcher = \JFactory::getApplication();
		$dispatcher->triggerEvent('onBeforeLoadProductList', array());

		$this->loadRequestData();
		$limitstart = $this->getLimitStart();
		$orderby = $this->getOrderBy();
		$order = $this->getOrder();
		$limit = $this->getLimit();

        $orderbyq = \JSHelper::getQuerySortDirection($order, $orderby);
        $image_sort_dir = \JSHelper::getImgSortDirection($order, $orderby);
		$this->setImageSortDir($image_sort_dir);
        $field_order = $this->getProductFieldSorting($order);
		$filters = $this->getFilterListProduct();
        $this->setFilters($filters);

        if ($this->getMultiPageList()){
            $total = $this->getLoadCountProducts($filters, $field_order, $orderbyq);
            $dispatcher->triggerEvent('onBeforeFixLimitstartDisplayProductList', array(&$limitstart, &$total, $this->getProductListName()));
			$this->setTotal($total);
            if ($limitstart>=$total){
                $limitstart = 0;
				$this->setLimitStart($limitstart);
            }
			$pagination = new \JPagination($total, $limitstart, $limit);
            $pagenav = $pagination->getPagesLinks();
			$this->setPagination($pagination);
			$this->setPagenav($pagenav);
        }

        $products = $this->getLoadProducts($filters, $field_order, $orderbyq, $limitstart, $limit);

		$dispatcher->triggerEvent('onBeforeDisplayProductList', array(&$products));
        $this->setProducts($products);
		return 1;
	}

    function getLoadProducts($filters = [], $order = null, $orderby = null, $limitstart = 0, $limit = 0, $listProductUpdateData = 1){
        $db = \JFactory::getDBO();
        $adv_query = $this->default_adv_query;
        $adv_from = $this->default_adv_from;
        $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct($this->getProductListName(), "list", $filters, $adv_query, $adv_from, $adv_result);
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);

        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeQueryGetProductList', array($this->getProductListName(), &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish=1 AND cat.category_publish=1 ".$adv_query."
                  GROUP BY prod.product_id ".$order_query;
        $obj = $this;
        $dispatcher->triggerEvent('onBeforeExeQueryGetProductList', array($this->getProductListName(), &$query, &$filters, &$obj));
        if ($limit){
            $db->setQuery($query, $limitstart, $limit);
        }else{
            $db->setQuery($query);
        }
        $products = $db->loadObjectList();
        if ($listProductUpdateData){
            $products = \JSHelper::listProductUpdateData($products, 1);
        }
        return $products;
    }

    function getLoadCountProducts($filters = [], $order = null, $orderby = null){
        $db = \JFactory::getDBO();
        $adv_query = $this->default_adv_query;
        $adv_from = $this->default_adv_from;
        $adv_result = "COUNT(distinct prod.product_id)";
        $this->getBuildQueryListProduct($this->getProductListName(), "count", $filters, $adv_query, $adv_from, $adv_result);

        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeQueryCountProductList', array($this->getProductListName(), &$adv_result, &$adv_from, &$adv_query, &$filters) );
        $query = "SELECT $adv_result  FROM `#__jshopping_products` as prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish=1 AND cat.category_publish=1 ".$adv_query;
        $obj = $this;
        $dispatcher->triggerEvent('onBeforeExeQueryCountProductList', array($this->getProductListName(), &$query, &$filters, &$obj));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getBuildQueryListProductDefaultResult($adfields=array()){
        $lang = \JSFactory::getLang();
		if (count($adfields)>0) $adquery = ",".implode(', ',$adfields); else $adquery = '';
        return "prod.product_id, pr_cat.category_id, prod.main_category_id, prod.`".$lang->get('name')."` as name, prod.`".$lang->get('short_description')."` as short_description, prod.product_ean, prod.manufacturer_code, prod.image, prod.product_price, prod.currency_id, prod.product_tax_id as tax_id, prod.product_old_price, prod.product_weight, prod.average_rating, prod.reviews_count, prod.hits, prod.weight_volume_units, prod.basic_price_unit_id, prod.label_id, prod.product_manufacturer_id, prod.min_price, prod.product_quantity, prod.different_prices".$adquery;
    }

    function getBuildQueryListProduct($type, $restype, &$filters, &$adv_query, &$adv_from, &$adv_result){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        $db = \JFactory::getDBO();
        $originaladvres = $adv_result;

        $groups = implode(',', $user->getAuthorisedViewLevels());
        if ($type=="category"){
            $adv_query .=' AND prod.access IN ('.$groups.')';
        }else{
            $adv_query .=' AND prod.access IN ('.$groups.') AND cat.access IN ('.$groups.')';
        }

        if (isset($jshopConfig->product_list_show_delivery_time) && $jshopConfig->product_list_show_delivery_time){
            $adv_result .= ", prod.delivery_times_id";
        }
        if ($jshopConfig->admin_show_product_extra_field){
            $adv_result .= \JSHelper::getQueryListProductsExtraFields();
            $adv_from .= " LEFT JOIN `#__jshopping_products_to_extra_fields` AS prod_to_ef ON prod.product_id=prod_to_ef.product_id ";
        }
        if ($jshopConfig->product_list_show_vendor){
            $adv_result .= ", prod.vendor_id";
        }
        if ($jshopConfig->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        if (isset($filters['categorys']) && is_array($filters['categorys']) && count($filters['categorys'])){
            $adv_query .= " AND cat.category_id in (".implode(",",$filters['categorys']).")";
        }
        if (isset($filters['manufacturers']) && is_array($filters['manufacturers']) && count($filters['manufacturers'])){
            $adv_query .= " AND prod.product_manufacturer_id in (".implode(",",$filters['manufacturers']).")";
        }
        if (isset($filters['labels']) && is_array($filters['labels']) && count($filters['labels'])){
            $adv_query .= " AND prod.label_id in (".implode(",",$filters['labels']).")";
        }
        if (isset($filters['vendors']) && is_array($filters['vendors']) && count($filters['vendors'])){
            $adv_query .= " AND prod.vendor_id in (".implode(",",$filters['vendors']).")";
        }
        if (isset($filters['extra_fields']) && is_array($filters['extra_fields'])){
            foreach($filters['extra_fields'] as $f_id=>$vals){
                if (is_array($vals) && count($vals)){
                    $tmp = array();
                    foreach($vals as $val_id){
                        $tmp[] = " find_in_set('".$db->escape($val_id)."', prod_to_ef.`extra_field_".(int)$f_id."`) ";
                    }
                    $mchfilterlogic = 'OR';
                    if (isset($jshopConfig->mchfilterlogic_and[$f_id]) && $jshopConfig->mchfilterlogic_and[$f_id]) $mchfilterlogic = 'AND';
                    $_tmp_adv_query = implode(' '.$mchfilterlogic.' ', $tmp);
                    $adv_query .= " AND (".$_tmp_adv_query.")";
                }elseif(is_string($vals) && $vals!=""){
                    $adv_query .= " AND prod_to_ef.`extra_field_".(int)$f_id."`='".$db->escape($vals)."'";
                }
            }
        }
		if (isset($filters['extra_fields_t']) && is_array($filters['extra_fields_t'])){
            foreach($filters['extra_fields_t'] as $f_id=>$vals){
                if (is_array($vals) && count($vals)){
                    $tmp = array();
                    foreach($vals as $val){
						$tmp[] = " prod_to_ef.`extra_field_".(int)$f_id."`='".$db->escape($val)."'";
                    }
                    $mchfilterlogic = 'OR';
                    if (isset($jshopConfig->mchfilterlogic_and[$f_id]) && $jshopConfig->mchfilterlogic_and[$f_id]) $mchfilterlogic = 'AND';
                    $_tmp_adv_query = implode(' '.$mchfilterlogic.' ', $tmp);
					$adv_query .= " AND (".$_tmp_adv_query.")";
                }
            }
        }

        if (isset($filters['date_to']) && $filters['date_to'] && \JSHelper::checkMyDate($filters['date_to'])) {
            $adv_query .= " AND prod.product_date_added <= '".$db->escape($filters['date_to'])."'";
        }
        if (isset($filters['date_from']) && $filters['date_from'] && \JSHelper::checkMyDate($filters['date_from'])) {
            $adv_query .= " AND prod.product_date_added >= '".$db->escape($filters['date_from'])."'";
        }
        if (isset($filters['products']) && $filters['products'] && is_array($filters['products']) && count($filters['products'])){
            $adv_query .= " AND prod.product_id in (".implode(',', array_map('intval', $filters['products'])).") ";
        }
        if (isset($filters['search']) && $filters['search']){
            $where_search = "";
			$filters['search_type'] = $filters['search_type'] ?? '';
            if ($filters['search_type']=="exact"){
                $word = addcslashes($db->escape($filters['search']), "_%");
                $tmp = array();
                foreach($jshopConfig->product_search_fields as $field){
					if (isset($jshopConfig->product_search_fields_exact) && in_array($field, $jshopConfig->product_search_fields_exact)) {
                        $tmp[] = "LOWER(".\JSHelper::getDBFieldNameFromConfig($field).") LIKE '".$word."'";
                    } else {
                        $tmp[] = "LOWER(".\JSHelper::getDBFieldNameFromConfig($field).") LIKE '%".$word."%'";
                    }
                }
                $where_search = implode(' OR ', $tmp);
            }else{
                $words = explode(" ", $filters['search']);
                $search_word = array();
                foreach($words as $word){
                    $word = addcslashes($db->escape($word), "_%");
                    $tmp = array();
                    foreach($jshopConfig->product_search_fields as $field){
                        if (isset($jshopConfig->product_search_fields_exact) && in_array($field, $jshopConfig->product_search_fields_exact)) {
							$tmp[] = "LOWER(".\JSHelper::getDBFieldNameFromConfig($field).") LIKE '".$word."'";
						} else {
							$tmp[] = "LOWER(".\JSHelper::getDBFieldNameFromConfig($field).") LIKE '%".$word."%'";
						}
                    }
                    $where_search_block = implode(' OR ', $tmp);
                    $search_word[] = "(".$where_search_block.")";
                }
                if ($filters['search_type']=="any"){
                    $where_search = implode(" OR ", $search_word);
                }else{
                    $where_search = implode(" AND ", $search_word);
                }
            }
            if ($where_search){
                $adv_query .= " AND ($where_search)";
            }
        }

        $this->getBuildQueryListProductFilterPrice($filters, $adv_query, $adv_from);

        if ($jshopConfig->product_list_show_qty_stock){
            $adv_result .= ", prod.unlimited";
        }

        if ($restype=="count"){
            $adv_result = $originaladvres;
        }
    }

    function getBuildQueryListProductFilterPrice($filters, &$adv_query, &$adv_from){
        if (isset($filters['price_from'])){
            $price_from = \JSHelper::getCorrectedPriceForQueryFilter($filters['price_from']);
        }else{
            $price_from = 0;
        }
        if (isset($filters['price_to'])){
            $price_to = \JSHelper::getCorrectedPriceForQueryFilter($filters['price_to']);
        }else{
            $price_to = 0;
        }
        if (!$price_from && !$price_to) return 0;

        $jshopConfig = \JSFactory::getConfig();
        $userShop = \JSFactory::getUserShop();
        $multyCurrency = count(\JSFactory::getAllCurrency());
        if ($userShop->percent_discount){
            $price_part = 1-$userShop->percent_discount/100;
        }else{
            $price_part = 1;
        }

        $adv_query2 = "";
        $adv_from2 = "";

        if ($multyCurrency > 1){
            $adv_from2 .= " LEFT JOIN `#__jshopping_currencies` AS cr USING (currency_id) ";
            if ($price_to){
                if ($jshopConfig->product_list_show_min_price){
                    $adv_query2 .= " AND (( prod.product_price*$price_part / cr.currency_value )<=".$price_to." OR ( prod.min_price*$price_part / cr.currency_value)<=" . $price_to." )";
                }else{
                    $adv_query2 .= " AND ( prod.product_price*$price_part / cr.currency_value ) <= ".$price_to;
                }
            }

            if ($price_from){
                if ($jshopConfig->product_list_show_min_price){
                    $adv_query2 .= " AND (( prod.product_price*$price_part / cr.currency_value ) >= ".$price_from." OR ( prod.min_price*$price_part / cr.currency_value ) >= " . $price_from." )";
                }else{
                    $adv_query2 .= " AND ( prod.product_price*$price_part / cr.currency_value ) >= ".$price_from;
                }
            }
        }else{
            if ($price_to){
                if ($jshopConfig->product_list_show_min_price){
                    $adv_query2 .= " AND (prod.product_price*$price_part <=".$price_to." OR prod.min_price*$price_part <=" . $price_to." )";
                }else{
                    $adv_query2 .= " AND prod.product_price*$price_part <= ".$price_to;
                }
            }
            if ($price_from){
                if ($jshopConfig->product_list_show_min_price){
                    $adv_query2 .= " AND (prod.product_price*$price_part >= ".$price_from." OR prod.min_price*$price_part >= " . $price_from." )";
                }else{
                    $adv_query2 .= " AND prod.product_price*$price_part >= ".$price_from;
                }
            }
        }

        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBuildQueryListProductFilterPrice', array($filters, &$adv_query, &$adv_from, &$adv_query2, &$adv_from2) );

        $adv_query .= $adv_query2;
        $adv_from .= $adv_from2;
    }

    function getBuildQueryOrderListProduct($order, $orderby, &$adv_from){
        $order_query = "";
        if (!$order) return $order_query;
        $order_original = $order;
        $jshopConfig = \JSFactory::getConfig();
        $multyCurrency = count(\JSFactory::getAllCurrency());
        if ($multyCurrency>1 && $order=="prod.product_price"){
            if (strpos($adv_from,"jshopping_currencies")===false){
                $adv_from .= " LEFT JOIN `#__jshopping_currencies` AS cr USING (currency_id) ";
            }
            if ($jshopConfig->product_list_show_min_price){
                $order = "prod.min_price/cr.currency_value";
            }else{
                $order = "prod.product_price/cr.currency_value";
            }
        }
        if ($order=="prod.product_price" && $jshopConfig->product_list_show_min_price){
            $order = "prod.min_price";
        }
        $order_query = " ORDER BY ".$order;
        if ($orderby){
            $order_query .= " ".$orderby;
        }

        \JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBuildQueryOrderListProduct', array($order, $orderby, &$adv_from, &$order_query, $order_original) );

    return $order_query;
    }
    
    public function setMultiPageList($multi_page_list){
        $this->multi_page_list = $multi_page_list;
    }
    
    public function getMultiPageList(){
        return $this->multi_page_list;
    }

    public function setTable($table){
        $this->table = $table;
    }

    public function setOrderBy(&$orderby){
        $this->orderby = $orderby;
    }
    
    public function setOrder(&$order){
        $this->order = $order;
    }
	
    public function setLimit(&$limit){
        $this->limit = $limit;
    }
	
	public function setLimitStart(&$limitstart){
        $this->limitstart = $limitstart;
    }
	
    public function setImageSortDir(&$image_sort_dir){
        $this->image_sort_dir = $image_sort_dir;
    }
	
    public function setFilters(&$filters){
        $this->filters = $filters;
    }
	
    public function setProducts(&$products){
        $this->products = $products;
    }
	
    public function setPagination(&$pagination){
        $this->pagination = $pagination;
    }
	
    public function setPagenav(&$pagenav){
        $this->pagenav = $pagenav;
    }
	
	public function setTotal(&$total){
        $this->total = $total;
    }
    
    public function getOrderBy(){
        return $this->orderby;
    }
	
    public function getOrder(){
        return $this->order;
    }            
	
    public function getLimit(){
        return $this->limit;
    }
	
	public function getLimitStart(){
        return $this->limitstart;
    }
	
    public function getImageSortDir(){
        return $this->image_sort_dir;
    }
	
    public function getFilters(){
        return $this->filters;
    }
	
    public function getAction(){
		$action = \JSHelper::xhtmlUrl($_SERVER['REQUEST_URI']);
        return $action;
    }
	
    public function getProducts(){
        return $this->products;
    }
	
    public function getPagination(){
        return $this->pagination;
    }
	
    public function getPagenav(){
        return $this->pagenav;
    }
	
	public function getTotal(){
        return $this->total;
    }
    
    function getDefaultProductSorting(){
        return \JSFactory::getConfig()->product_sorting;
    }

    function getDefaultProductSortingDirection(){
        return \JSFactory::getConfig()->product_sorting_direction;
    }

    function getCountProductsPerPage(){
        return \JSFactory::getConfig()->count_products_to_page;
    }

    function getCountProductsToRow(){
        return \JSFactory::getConfig()->count_products_to_row;
    }

    function getProductFieldSorting($order){
        return \JSFactory::getConfig()->sorting_products_field_select[$order];
    }

    public function getContext(){
        return "jshoping.list.front.product";
    }

    public function getContextFilter(){
        return "jshoping.list.front.product";
    }

    public function getNoFilterListProduct(){
        return array();
    }

    public function getStandartFilterListProduct(){
        return array();
    }

    public function getProductListName(){
        return 'list';
    }

    public function getProductsOrderingTypeList(){
        return 0;
    }

	public function getFilterListProduct(){
		return \JSFactory::getModel('filter', 'Site\\Productlist')->getFilter($this->getContextFilter(), $this->getNoFilterListProduct());
	}
    
    public function getHtmlSelectSorting(){
        return Selects::getProductsOrdering($this->getProductsOrderingTypeList(), $this->getOrder());
    }
    
    public function getHtmlSelectCount(){
        return Selects::getProductsCount($this->getCountProductsPerPage(), $this->getLimit());
    }
    
    public function getHtmlSelectFilterManufacturer($fulllist = 0){
        if (\JSFactory::getConfig()->show_product_list_filters){
            $filters = $this->getFilters();
            if (!$fulllist){
                $filter_manufactures = $this->table->getManufacturers();
            }else{
                $filter_manufactures = \JSFactory::getTable('manufacturer')->getList();
            }
            if (isset($filters['manufacturers'][0])){
                $active_manufacturer = $filters['manufacturers'][0];            
            }else{
                $active_manufacturer = 0;
            }
			$manufacuturers_sel = Selects::getFilterManufacturer($filter_manufactures, $active_manufacturer);
        }else{
			$manufacuturers_sel = '';
		}
        return $manufacuturers_sel;
    }
    
    public function getHtmlSelectFilterCategory($fulllist = 0){
        if (\JSFactory::getConfig()->show_product_list_filters){
            $filters = $this->getFilters();
            if (!$fulllist){
                $filter_categorys = $this->table->getCategorys();
            }else{
                $filter_categorys = \JSHelper::buildTreeCategory(1);
            }
            if (isset($filters['categorys'][0])){
                $active_category = $filters['categorys'][0];
            }else{
                $active_category = 0;
            }
			$categorys_sel = Selects::getFilterCategory($filter_categorys, $active_category);
        }else{
            $categorys_sel = '';
        }
        return $categorys_sel;
    }
    
    public function getWillBeUseFilter(){
        return \JSFactory::getModel('filter', 'Site\\Productlist')->willBeUseFilter($this->getFilters(), $this->getStandartFilterListProduct());
    }
    
    public function getDisplayListProducts(){
        $display_list_products = (count($this->getProducts())>0 || $this->getWillBeUseFilter());
        extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
        return $display_list_products;
    }
    
    public function getAllowReview(){
        $allow_review = \JSFactory::getTable('review')->getAllowReview();
        extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
        return $allow_review;
    }
    
    public function configDisableSortAndFilters(){
        $jshopConfig = \JSFactory::getConfig();
        $jshopConfig->show_sort_product = 0;
        $jshopConfig->show_count_select_products = 0;
        $jshopConfig->show_product_list_filters = 0;
    }
	
	public function getTmplBlockListProduct(){
		return \JSFactory::getConfig()->default_template_block_list_product;
	}
	
	public function getTmplNoListProduct(){
		return \JSFactory::getConfig()->default_template_no_list_product;
	}
	
	public function getTmplBlockFormFilter(){
		return \JSFactory::getConfig()->default_template_block_form_filter_product;
	}
	
	public function getTmplBlockPagination(){
		return \JSFactory::getConfig()->default_template_block_pagination_product;
	}

}