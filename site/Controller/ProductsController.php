<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
defined('_JEXEC') or die();

class ProductsController extends BaseController{
    
    public function init(){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshoppingControllerProducts', array(&$obj));
    }
	
	function display($cachable = false, $urlparams = false){
		$jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();

		\JSFactory::getModel('productShop', 'Site')->storeEndPages();
        $params = \JFactory::getApplication()->getParams();

        $header = \JSHelper::getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');
		
        Metadata::allProducts();

        $productlist = \JSFactory::getModel('product', 'Site\\Productlist');
        $productlist->load();
        
        $orderby = $productlist->getOrderBy();
        $image_sort_dir = $productlist->getImageSortDir();
        $filters = $productlist->getFilters();
        $action = $productlist->getAction();
        $products = $productlist->getProducts();
        $pagination = $productlist->getPagination();
        $pagenav = $productlist->getPagenav();
        $sorting_sel = $productlist->getHtmlSelectSorting();
        $product_count_sel = $productlist->getHtmlSelectCount();        
        $willBeUseFilter = $productlist->getWillBeUseFilter();
        $display_list_products = $productlist->getDisplayListProducts();
        $manufacuturers_sel = $productlist->getHtmlSelectFilterManufacturer(1);
        $categorys_sel = $productlist->getHtmlSelectFilterCategory(1);
        $allow_review = $productlist->getAllowReview();

        $view = $this->getView('products');
		$view->setLayout("products");
        $view->set('config', $jshopConfig);
        $view->set('template_block_list_product', $productlist->getTmplBlockListProduct());
        $view->set('template_no_list_product', $productlist->getTmplNoListProduct());
        $view->set('template_block_form_filter', $productlist->getTmplBlockFormFilter());
        $view->set('template_block_pagination', $productlist->getTmplBlockPagination());
        $view->set('path_image_sorting_dir', $jshopConfig->live_path.'images/'.$image_sort_dir);
        $view->set('filter_show', 1);
        $view->set('filter_show_category', 1);
        $view->set('filter_show_manufacturer', 1);
        $view->set('pagination', $pagenav);
        $view->set('pagination_obj', $pagination);
        $view->set('display_pagination', $pagenav!="");
        $view->set("header", $header);
        $view->set("prefix", $prefix);
		$view->set("rows", $products);
		$view->set("count_product_to_row", $productlist->getCountProductsToRow());
        $view->set('action', $action);
        $view->set('allow_review', $allow_review);
		$view->set('orderby', $orderby);		
		$view->set('product_count', $product_count_sel);
        $view->set('sorting', $sorting_sel);
        $view->set('categorys_sel', $categorys_sel);
        $view->set('manufacuturers_sel', $manufacuturers_sel);
        $view->set('filters', $filters);
        $view->set('willBeUseFilter', $willBeUseFilter);
        $view->set('display_list_products', $display_list_products);
        $view->set('shippinginfo', \JSHelper::SEFLink($jshopConfig->shippinginfourl,1));
        $view->set('total', $productlist->getTotal());
        $view->_tmp_list_products_html_start = "";
        $view->_tmp_list_products_html_end = "";
        $view->_tmp_ext_filter_box = "";
        $view->_tmp_ext_filter = "";
        $dispatcher->triggerEvent('onBeforeDisplayProductListView', array(&$view, &$productlist));
		$view->display();
	}
    
    function tophits(){
        Metadata::productsTophits();
        $this->verySimpleProductList('tophits');        
    }
    
    function toprating(){
        Metadata::productsToprating();
        $this->verySimpleProductList('toprating');
    }
    
    function label(){
        Metadata::productsLabel();
        $this->verySimpleProductList('label');
    }
    
    function bestseller(){
        Metadata::productsBestseller();
        $this->verySimpleProductList('bestseller');
    }
    
    function random(){
        Metadata::productsRandom();
        $this->verySimpleProductList('random');
    }
    
    function last(){
        Metadata::productsLast();
        $this->verySimpleProductList('last');
    }
    
    protected function verySimpleProductList($type){        
        $jshopConfig = \JSFactory::getConfig();        
        
		\JSFactory::getModel('productShop', 'Site')->storeEndPages();		

        $params = \JFactory::getApplication()->getParams();
        $header = \JSHelper::getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');

        $productlist = \JSFactory::getModel($type, 'Site\\Productlist');
        $productlist->setMultiPageList(0);
        $productlist->load();
        $productlist->configDisableSortAndFilters();
        
        $products = $productlist->getProducts();        
        $display_list_products = $productlist->getDisplayListProducts();        
        $allow_review = $productlist->getAllowReview();
        $action = $productlist->getAction();
        $orderby = $productlist->getOrderBy();

        $view = $this->getView('products');
        $view->setLayout("products");
        $view->set('config', $jshopConfig);
		$view->set('template_block_list_product', $productlist->getTmplBlockListProduct());
        $view->set('template_block_form_filter', $productlist->getTmplBlockFormFilter());
        $view->set('template_block_pagination', $productlist->getTmplBlockPagination());
        $view->set("header", $header);
        $view->set("prefix", $prefix);
        $view->set("rows", $products);
        $view->set("count_product_to_row", $productlist->getCountProductsToRow());
        $view->set('allow_review', $allow_review);
        $view->set('display_list_products', $display_list_products);
        $view->set('display_pagination', 0);
        $view->set('shippinginfo', \JSHelper::SEFLink($jshopConfig->shippinginfourl,1));
        $view->set('action', $action);
        $view->set('orderby', $orderby);
        $view->_tmp_list_products_html_start = "";
        $view->_tmp_list_products_html_end = "";
        $view->_tmp_ext_filter_box = "";
        $view->_tmp_ext_filter = "";
        \JFactory::getApplication()->triggerEvent('onBeforeDisplayProductListView', array(&$view, &$productlist));
        $view->display();
    }

}