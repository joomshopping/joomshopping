<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
defined('_JEXEC') or die();

class CategoryController extends BaseController{
    
    public function init(){
        PluginHelper::importPlugin('jshoppingproducts');
        $obj = $this;
        Factory::getApplication()->triggerEvent('onConstructJshoppingControllerCategory', array(&$obj));
    }
    
    function display($cachable = false, $urlparams = false){
		$app = Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $params = $app->getParams();
        $category_id = 0;
        
        $category = JSFactory::getTable('category');
        $category->load($category_id);
        $category->getDescription();
        
        $categories = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);

        $app->triggerEvent('onBeforeDisplayMainCategory', array(&$category, &$categories, &$params));

		Metadata::mainCategory($category, $params);

        $view = $this->getView('category');
        $view->setLayout("maincategory");
        $view->category = $category;
        $view->image_category_path = $jshopConfig->image_category_live_path;
        $view->noimage = $jshopConfig->noimage;
        $view->categories = $categories;
        $view->count_category_to_row = $category->getCountToRow();
        $view->params = $params;
        $view->_tmp_maincategory_html_start = ""; 
        $view->_tmp_maincategory_html_end = ""; 
        $app->triggerEvent('onBeforeDisplayCategoryView', array(&$view) );
        $view->display();
    }

    function view(){
        $user = Factory::getUser();
        $jshopConfig = JSFactory::getConfig();
		$app = Factory::getApplication();        
        $category_id = (int)$this->input->getInt('category_id');

		JSFactory::getModel('productShop', 'Site')->storeEndPages();

        $category = JSFactory::getTable('category');
        $category->load($category_id);
        $category->getDescription();
        $app->triggerEvent('onAfterLoadCategory', array(&$category, &$user));

		if (!$category->checkView($user)){            
			throw new \Exception(Text::_('JSHOP_PAGE_NOT_FOUND'), 404);
            return;
        }
        
        $sub_categories = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);
        $app->triggerEvent('onBeforeDisplayCategory', array(&$category, &$sub_categories) );
		
		Metadata::category($category);

        $productlist = JSFactory::getModel('category', 'Site\\Productlist');
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
        $view->config = $jshopConfig;
        $view->template_block_list_product = $productlist->getTmplBlockListProduct();
        $view->template_no_list_product = $productlist->getTmplNoListProduct();
        $view->template_block_form_filter = $productlist->getTmplBlockFormFilter();
        $view->template_block_pagination = $productlist->getTmplBlockPagination();
        $view->path_image_sorting_dir = $jshopConfig->live_path.'images/'.$image_sort_dir;
        $view->filter_show = 1;
        $view->filter_show_category = 0;
        $view->filter_show_manufacturer = 1;
        $view->pagination = $pagenav;
		$view->pagination_obj = $pagination;
        $view->display_pagination = $pagenav != "";
        $view->rows = $products;
        $view->count_product_to_row = $productlist->getCountProductsToRow();
        $view->image_category_path = $jshopConfig->image_category_live_path;
        $view->noimage = $jshopConfig->noimage;
        $view->category = $category;
        $view->categories = $sub_categories;
        $view->count_category_to_row = $category->getCountToRow();
        $view->allow_review = $allow_review;
        $view->product_count = $product_count_sel;
        $view->sorting = $sorting_sel;
        $view->action = $action;
        $view->orderby = $orderby;
        $view->manufacuturers_sel = $manufacuturers_sel;
        $view->filters = $filters;
        $view->willBeUseFilter = $willBeUseFilter;
        $view->display_list_products = $display_list_products;
        $view->shippinginfo = Helper::SEFLink($jshopConfig->shippinginfourl, 1);
        $view->total = $productlist->getTotal();
        $view->_tmp_category_html_start = ""; 
        $view->_tmp_category_html_before_products = ""; 
        $view->_tmp_category_html_end = "";
        $view->_tmp_list_products_html_start = "";
        $view->_tmp_list_products_html_end = "";
        $view->_tmp_ext_filter_box = "";
        $view->_tmp_ext_filter = "";
        $app->triggerEvent('onBeforeDisplayProductListView', array(&$view, &$productlist));
        $view->display();
    }
}