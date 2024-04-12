<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model\Productlist;
defined('_JEXEC') or die();

class FiltersearchModel{
	
	public $request;

    function getFilter($contextfilter, $no_filter = array()){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $this->loadData();

        $date_to = $this->getDateTo();
        $date_from = $this->getDateFrom();
        $price_to = $this->getPriceTo();
		$price_from = $this->getPriceFrom();
        $search = $this->getSearch();
        $search_type = $this->getSearchType();
		$extra_fields = $this->getExtraFields();
        $categorys = $this->getCategorys();
        $labels = $this->getLabels();
        $manufacturers = $this->getManufacturers();

		$filters = array();
        $filters['categorys'] = $categorys;
        $filters['manufacturers']= $manufacturers;
        $filters['price_from'] = $price_from;
        $filters['price_to'] = $price_to;
        if ($jshopConfig->admin_show_product_extra_field){
            $filters['extra_fields'] = $extra_fields;
        }
        $filters['labels'] = $labels;

		$filters['search'] = $search;
		$filters['date_from'] = $date_from;
		$filters['date_to'] = $date_to;
		$filters['search_type'] = $search_type;
		\JPluginHelper::importPlugin('jshoppingproducts');
        $app->triggerEvent('onAfterGetBuildFilterListProduct', array(&$filters));
		return $filters;
    }
	
	public function loadData(){
		$session = \JFactory::getSession();
		$post = \JFactory::getApplication()->input->getArray();
        if (isset($post['setsearchdata']) && $post['setsearchdata']==1){
            $session->set("jshop_end_form_data", $post);
        }else{
            $data = $session->get("jshop_end_form_data");
            if (isset($data) && count($data)){
                $post = $data;
            }
        }
		$this->request = $post; 
	}
	
	public function getData(){
		return $this->request;
	}
	
	public function getCategoryId(){
		return (int)$this->request['category_id'];
	}
	
	public function getManufacturerId(){
		return (int)$this->request['manufacturer_id'];
	}
    
    public function getManufacturers(){
        $manufacturers = array();        
        if (isset($this->request['manufacturers'])){
            $manufacturers = $this->request['manufacturers'];
            $manufacturers = \JSHelper::filterAllowValue($manufacturers, "int+");
        }elseif($this->request['manufacturer_id']){            
            $manufacturers[] = $this->getManufacturerId();
        }
		return $manufacturers;
	}
	
	public function getDateTo(){
		if (isset($this->request['date_to'])) 
            $date_to = $this->request['date_to'];
        else 
            $date_to = null;
		return $date_to;
	}
	
	public function getDateFrom(){
		if (isset($this->request['date_from'])) 
            $date_from = $this->request['date_from'];
        else 
            $date_from = null;
		return $date_from;
	}
    
    public function getPriceTo(){
		if (isset($this->request['price_to'])) 
            $price_to = \JSHelper::saveAsPrice($this->request['price_to']);
        else 
            $price_to = null;
        if (isset($this->request['fprice_to'])){
            $price_to = \JSHelper::saveAsPrice($this->request['fprice_to']);
        }
        return $price_to;        
	}
	
	public function getPriceFrom(){		        
        if (isset($this->request['price_from'])) 
            $price_from = \JSHelper::saveAsPrice($this->request['price_from']);
        else 
            $price_from = null;
        if (isset($this->request['fprice_from'])){
            $price_from = \JSHelper::saveAsPrice($this->request['fprice_from']);
        }
		return $price_from;
	}
	
	public function getIncludeSubcat(){
		if (isset($this->request['include_subcat']))
            $include_subcat = intval($this->request['include_subcat']);
        else
            $include_subcat = 0;
		return $include_subcat;
	}
	
	public function getSearch(){
		return trim($this->request['search']);
	}
	
	public function getSearchType(){
		$search_type = $this->request['search_type'];
        if (!$search_type) $search_type = "any";
		return $search_type;
	}
	
	public function getExtraFields(){
		$jshopConfig = \JSFactory::getConfig();
		if ($jshopConfig->admin_show_product_extra_field){
            if (isset($this->request['extra_fields'])) 
                $extra_fields = $this->request['extra_fields'];
            else
                $extra_fields = array();
            $extra_fields = \JSHelper::filterAllowValue($extra_fields, "array_int_k_v+");
        }else{
			$extra_fields = array();
		}
		return $extra_fields;
	}
	
	public function getCategorys(){
		$categorys = array();
		$category_id = $this->getCategoryId();
		$include_subcat = $this->getIncludeSubcat();
        if ($category_id) {
            if ($include_subcat){
                $_category = \JSFactory::getTable('category');
                $all_categories = $_category->getAllCategories();
                $cat_search[] = $category_id;
                \JSHelper::searchChildCategories($category_id, $all_categories, $cat_search);
                foreach($cat_search as $key=>$value) {
                    $categorys[] = $value;
                }
            }else{
                $categorys[] = $category_id;
            }
        }
		return $categorys;
	}

    public function getLabels(){
        if (isset($this->request['labels']))
            $labels = $this->request['labels'];
        else
            $labels = array();
        return \JSHelper::filterAllowValue($labels, "int+");
    }
	
}