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

class VendorController extends BaseController{
    
    public function init(){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshoppingControllerVendor', array(&$obj));
    }

    function display($cachable = false, $urlparams = false){
        $mainframe = \JFactory::getApplication();
        $params = $mainframe->getParams();        
        $jshopConfig = \JSFactory::getConfig();
        $dispatcher = \JFactory::getApplication();
		$model = \JSFactory::getModel('vendorList', 'Site');
        
        Metadata::listVendors();
		
		$model->load();
		$rows = $model->getList();
		$pagination = $model->getPagination();
		$pagenav = $pagination->getPagesLinks();
		
        $view = $this->getView('vendor');
        $view->setLayout("vendors");
        $view->set("rows", $rows);        
        $view->set('count_to_row', $model->getCountToRow());
        $view->set('params', $params);
        $view->set('pagination', $pagenav);
        $view->set('display_pagination', $pagenav!="");
        $dispatcher->triggerEvent('onBeforeDisplayVendorView', array(&$view) );
        $view->display();
    }  

    function info(){
        $jshopConfig = \JSFactory::getConfig();
        $dispatcher = \JFactory::getApplication();
		$vendor_id = $this->input->getInt("vendor_id");
		
        if (!$jshopConfig->product_show_vendor_detail){
            \JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
            return;
        }

        $vendor = \JSFactory::getTable('vendor');
        $vendor->load($vendor_id);
        if (!$vendor->id){
            \JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
            return;
        }
        
        $dispatcher->triggerEvent('onBeforeDisplayVendorInfo', array(&$vendor));
                
        $header = $vendor->shop_name;
		
		Metadata::vendorInfo($vendor);        

        $vendor->country = $vendor->getCountryName();

        $view = $this->getView('vendor');
        $view->setLayout("info");
        $view->set('vendor', $vendor);
        $view->set('header', $header);
        $dispatcher->triggerEvent('onBeforeDisplayVendorInfoView', array(&$view) );
        $view->display();        
    }
    
    function products(){
        $jshopConfig = \JSFactory::getConfig();
        $dispatcher = \JFactory::getApplication();
        $vendor_id = $this->input->getInt("vendor_id");

		\JSFactory::getModel('productShop', 'Site')->storeEndPages();
        
        $vendor = \JSFactory::getTable('vendor');
        $vendor->load($vendor_id);
        if (!$vendor->id){
            \JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
            return;
        }

        $dispatcher->triggerEvent('onBeforeDisplayVendor', array(&$vendor));
        
        Metadata::vendorProducts($vendor);

        $productlist = \JSFactory::getModel('vendor', 'Site\\Productlist');
        $productlist->setTable($vendor);
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

        $view = $this->getView('vendor');
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
        $view->set("rows", $products);
        $view->set("count_product_to_row", $productlist->getCountProductsToRow());
        $view->set("vendor", $vendor);
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
        $view->_tmp_ext_filter_box = "";
        $view->_tmp_ext_filter = "";
        $view->_tmp_list_products_html_start = "";
        $view->_tmp_list_products_html_end = "";
        $dispatcher->triggerEvent('onBeforeDisplayProductListView', array(&$view, &$productlist));
        $view->display();
    }
    
}