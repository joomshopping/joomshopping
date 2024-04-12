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

class CategoryController extends BaseController{
    
    public function init(){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshoppingControllerCategory', array(&$obj));
    }
    
    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
		$dispatcher = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $params = $app->getParams();
        $category_id = 0;
        
        $category = \JSFactory::getTable('category');
        $category->load($category_id);
        $category->getDescription();
        
        $categories = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);

        $dispatcher->triggerEvent('onBeforeDisplayMainCategory', array(&$category, &$categories, &$params));

		Metadata::mainCategory($category, $params);

        $view = $this->getView('category');
        $view->setLayout("maincategory");
        $view->set('category', $category);
        $view->set('image_category_path', $jshopConfig->image_category_live_path);
        $view->set('noimage', $jshopConfig->noimage);
        $view->set('categories', $categories);
        $view->set('count_category_to_row', $category->getCountToRow());
        $view->set('params', $params);
        $view->_tmp_maincategory_html_start = ""; 
        $view->_tmp_maincategory_html_end = ""; 
        $dispatcher->triggerEvent('onBeforeDisplayCategoryView', array(&$view) );
        $view->display();
    }

    function view(){
        $user = \JFactory::getUser();
        $jshopConfig = \JSFactory::getConfig();
		$dispatcher = \JFactory::getApplication();        
        $category_id = (int)$this->input->getInt('category_id');

		\JSFactory::getModel('productShop', 'Site')->storeEndPages();

        $category = \JSFactory::getTable('category');
        $category->load($category_id);
        $category->getDescription();
        $dispatcher->triggerEvent('onAfterLoadCategory', array(&$category, &$user));

		if (!$category->checkView($user)){
            \JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
            return;
        }
        
        $sub_categories = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);
        $dispatcher->triggerEvent('onBeforeDisplayCategory', array(&$category, &$sub_categories) );
		
		Metadata::category($category);

        $productlist = \JSFactory::getModel('category', 'Site\\Productlist');
        $productlist->setTable($category);
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
        $manufacuturers_sel = $productlist->getHtmlSelectFilterManufacturer();
        $allow_review = $productlist->getAllowReview();

        $view = $this->getView('category');
        $view->setLayout("category_".$category->category_template);
        $view->set('config', $jshopConfig);
        $view->set('template_block_list_product', $productlist->getTmplBlockListProduct());
        $view->set('template_no_list_product', $productlist->getTmplNoListProduct());
        $view->set('template_block_form_filter', $productlist->getTmplBlockFormFilter());
        $view->set('template_block_pagination', $productlist->getTmplBlockPagination());
        $view->set('path_image_sorting_dir', $jshopConfig->live_path.'images/'.$image_sort_dir);
        $view->set('filter_show', 1);
        $view->set('filter_show_category', 0);
        $view->set('filter_show_manufacturer', 1);
        $view->set('pagination', $pagenav);
		$view->set('pagination_obj', $pagination);
        $view->set('display_pagination', $pagenav!="");
        $view->set('rows', $products);
        $view->set('count_product_to_row', $productlist->getCountProductsToRow());
        $view->set('image_category_path', $jshopConfig->image_category_live_path);
        $view->set('noimage', $jshopConfig->noimage);
        $view->set('category', $category);
        $view->set('categories', $sub_categories);
        $view->set('count_category_to_row', $category->getCountToRow());
        $view->set('allow_review', $allow_review);
        $view->set('product_count', $product_count_sel);
        $view->set('sorting', $sorting_sel);
        $view->set('action', $action);
        $view->set('orderby', $orderby);
        $view->set('manufacuturers_sel', $manufacuturers_sel);
        $view->set('filters', $filters);
        $view->set('willBeUseFilter', $willBeUseFilter);
        $view->set('display_list_products', $display_list_products);
        $view->set('shippinginfo', \JSHelper::SEFLink($jshopConfig->shippinginfourl,1));
        $view->set('total', $productlist->getTotal());
        $view->_tmp_category_html_start = ""; 
        $view->_tmp_category_html_before_products = ""; 
        $view->_tmp_category_html_end = "";
        $view->_tmp_list_products_html_start = "";
        $view->_tmp_list_products_html_end = "";
        $view->_tmp_ext_filter_box = "";
        $view->_tmp_ext_filter = "";
        $dispatcher->triggerEvent('onBeforeDisplayProductListView', array(&$view, &$productlist));
        $view->display();
    }
}