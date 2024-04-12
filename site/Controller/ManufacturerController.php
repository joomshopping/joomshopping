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

class ManufacturerController extends BaseController{
    
    public function init(){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshoppingControllerManufacturer', array(&$obj));
    }
	
	function display($cachable = false, $urlparams = false){        
        $params = \JFactory::getApplication()->getParams();        
		$jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();
        
		$manufacturer = \JSFactory::getTable('manufacturer');
        $manufacturer->getDescription();
		$rows = $manufacturer->getAllManufacturers(1, $manufacturer->getFieldListOrdering(), $manufacturer->getSortingDirection());

        $dispatcher->triggerEvent('onBeforeDisplayListManufacturers', array(&$rows, &$params));

        Metadata::listManufacturers($params);
        
        $view = $this->getView('manufacturer');
		$view->setLayout("manufacturers");
		$view->set("rows", $rows);
		$view->set("image_manufs_live_path", $jshopConfig->image_manufs_live_path);
        $view->set('noimage', $jshopConfig->noimage);
        $view->set('count_manufacturer_to_row', $manufacturer->getCountToRow());
        $view->set('params', $params);        
		$view->set('manufacturer', $manufacturer);
        $dispatcher->triggerEvent('onBeforeDisplayManufacturerView', array(&$view) );
		$view->display();
	}	
	
	function view(){
	    $dispatcher = \JFactory::getApplication();
		$jshopConfig = \JSFactory::getConfig();        
        $manufacturer_id = $this->input->getInt('manufacturer_id');

		\JSFactory::getModel('productShop', 'Site')->storeEndPages();
		
		$manufacturer = \JSFactory::getTable('manufacturer');		
		$manufacturer->load($manufacturer_id);
		$manufacturer->getDescription();

        $dispatcher->triggerEvent('onBeforeDisplayManufacturer', array(&$manufacturer));
        
        if (!$manufacturer->checkView()){
            \JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
            return;
        }

        Metadata::manufacturer($manufacturer);
        
        $productlist = \JSFactory::getModel('manufacturer', 'Site\\Productlist');
        $productlist->setTable($manufacturer);
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
        $categorys_sel = $productlist->getHtmlSelectFilterCategory();
        $allow_review = $productlist->getAllowReview();

        $view = $this->getView('manufacturer');
		$view->setLayout("products");
        $view->set('config', $jshopConfig);
        $view->set('template_block_list_product', $productlist->getTmplBlockListProduct());
        $view->set('template_no_list_product', $productlist->getTmplNoListProduct());
        $view->set('template_block_form_filter', $productlist->getTmplBlockFormFilter());
        $view->set('template_block_pagination', $productlist->getTmplBlockPagination());
        $view->set('path_image_sorting_dir', $jshopConfig->live_path.'images/'.$image_sort_dir);
        $view->set('filter_show', 1);
        $view->set('filter_show_category', 1);
        $view->set('filter_show_manufacturer', 0);
        $view->set('pagination', $pagenav);
		$view->set('pagination_obj', $pagination);
        $view->set('display_pagination', $pagenav!="");
		$view->set("rows", $products);
		$view->set("count_product_to_row", $productlist->getCountProductsToRow());
		$view->set("manufacturer", $manufacturer);
        $view->set('action', $action);
        $view->set('allow_review', $allow_review);
		$view->set('orderby', $orderby);		
		$view->set('product_count', $product_count_sel);
        $view->set('sorting', $sorting_sel);
        $view->set('categorys_sel', $categorys_sel);
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